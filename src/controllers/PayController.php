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

use hiqdev\yii2\merchant\actions\RequestAction;
use hiqdev\yii2\merchant\models\DepositForm;
use hiqdev\yii2\merchant\models\DepositRequest;
use hiqdev\yii2\merchant\Module;
use hiqdev\yii2\merchant\transactions\Transaction;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class PayController extends \yii\web\Controller
{
    public function actions()
    {
        return array_merge(parent::actions(), [
            'request' => [
                'class' => RequestAction::class
            ]
        ]);
    }

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
        $model   = Yii::createObject($this->getMerchantModule()->depositFromClass);
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
        $request->amount = $form->amount;

        $requests = $this->getMerchantModule()->getPurchaseRequestCollection($request)->getItems();

        return $this->render('deposit', [
            'requests' => $requests,
            'depositForm' => $form
        ]);
    }
}
