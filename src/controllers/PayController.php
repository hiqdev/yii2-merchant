<?php
/**
 * Yii2 extension for payment processing with Omnipay, Payum and more later.
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\controllers;

use hiqdev\yii2\merchant\models\DepositForm;
use hiqdev\yii2\merchant\models\DepositRequest;
use hiqdev\yii2\merchant\Module;
use hiqdev\yii2\merchant\transactions\Transaction;
use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

class PayController extends \yii\web\Controller
{
    /**
     * @return Module|\yii\base\Module
     */
    public function getMerchantModule()
    {
        return $this->module;
    }

    /**
     * Disable CSRF validation for POST requests we receive from outside
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if (in_array($this->action->id, ['notify', 'return', 'cancel'], true)) {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * @return Response
     */
    public function actionCancel()
    {
        Yii::$app->session->addFlash('error', Yii::t('merchant', 'Payment failed or cancelled'));

        return $this->redirect($this->getMerchantModule()->previousUrl() ?: ['deposit']);
    }

    /**
     * @return string
     */
    public function actionReturn()
    {
        $this->checkNotify();

        return $this->render('return', [
            'transactionId' => Yii::$app->request->get('transactionId'),
        ]);
    }

    /**
     * @param string $transactionId
     * @throws BadRequestHttpException
     * @return array
     */
    public function actionCheckReturn($transactionId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction = $this->getMerchantModule()->findTransaction($transactionId);

        if ($transaction->getParameter('username') !== $this->getMerchantModule()->getUsername()) {
            throw new BadRequestHttpException('Access denied', 403);
        }

        return [
            'status' => $transaction->getSuccess(),
            'url'    => $transaction->isConfirmed()
                ? $transaction->getParameter('finishUrl')
                : $transaction->getParameter('cancelUrl'),
        ];
    }

    /**
     * Action is designed to get the system notification from payment system,
     * process it and report success or error for the payment system.
     * @return null|string
     */
    public function actionNotify()
    {
        $transaction = $this->checkNotify();
        if ($transaction === null) {
            return 'Unknown transaction';
        }

        Yii::$app->response->format = Response::FORMAT_RAW;

        return $transaction->isConfirmed() ? 'OK' : $transaction->getParameter('error');
    }

    /**
     * Check notifications.
     * TODO: implement actual request check and proper handling.
     * @return Transaction
     */
    public function checkNotify()
    {
        throw new InvalidConfigException('Method checkNotify must be implemented');
    }

    public function actionDeposit()
    {
        $model   = Yii::createObject($this->getMerchantModule()->depositClass);
        $request = Yii::$app->request;
        if ($model->load($request->isPost ? $request->post() : $request->get()) && $model->validate()) {
            return $this->renderDeposit($model);
        }

        return $this->render('deposit-form', [
            'model' => $model,
            'availableMerchants' => $this->getMerchantModule()->getPurchaseRequestCollection()->getItems(),
        ]);
    }

    /**
     * Renders depositing buttons for given request data.
     *
     * @param DepositForm $form request data:
     * @return \yii\web\Response
     */
    public function renderDeposit($form)
    {
        $request = new DepositRequest();
        $request->amount = $form->sum;

        $requests = $this->getMerchantModule()->getPurchaseRequestCollection($request)->getItems();

        return $this->render('deposit', compact('requests'));
    }

    /**
     * Performs purchase request.
     * @void
     */
    public function actionRequest()
    {
        $depositRequest = new DepositRequest();
        $depositRequest->load(Yii::$app->request->post());
        if (!$depositRequest->validate()) {
            throw new BadRequestHttpException('Deposit request is not loaded');
        }

        $this->getMerchantModule()->prepareRequestData($depositRequest);
        $request = $this->getMerchantModule()->getPurchaseRequest($depositRequest->merchant, $depositRequest);
        $this->getMerchantModule()->insertTransaction($depositRequest->id, $depositRequest->merchant, array_merge([
            'username' => $depositRequest->username,
        ], $depositRequest->toArray()));

        if ('GET' === $request->getFormMethod()) {
            return $this->redirect($request->getFormAction());
        } elseif ('POST' === $request->getFormMethod()) {
            $hiddenFields = '';
            foreach ($request->getFormInputs() as $key => $value) {
                $hiddenFields .= sprintf(
                    '<input type="hidden" name="%1$s" value="%2$s" />',
                    htmlentities($key, ENT_QUOTES, 'UTF-8', false),
                    htmlentities($value, ENT_QUOTES, 'UTF-8', false)
                )."\n";
            }

            $output = '<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Redirecting...</title>
    </head>
    <body onload="document.forms[0].submit();">
        <form action="%1$s" method="post">
            <p>Redirecting to payment page...</p>
            <p>
                %2$s
                <input type="submit" value="Continue" />
            </p>
        </form>
    </body>
</html>';
            $output = sprintf(
                $output,
                htmlentities($request->getFormAction(), ENT_QUOTES, 'UTF-8', false),
                $hiddenFields
            );

            echo $output;
            Yii::$app->end();
        }

        throw new BadRequestHttpException();
    }
}
