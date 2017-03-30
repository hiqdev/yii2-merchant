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

use hiqdev\yii2\merchant\Module;
use hiqdev\yii2\merchant\transactions\Transaction;
use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
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
            'status' => $transaction->getStatus(),
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
            return $this->renderDeposit($model->getAttributes());
        }

        return $this->render('deposit-form', [
            'model' => $model,
            'availableMerchants' => $this->getMerchantModule()->getCollection()->getItems(),
        ]);
    }

    /**
     * Renders depositing buttons for given request data.
     *
     * @param array $data request data:
     *  - `sum` - the amount of payment without fees
     *  - `currency` - the currency of transaction
     *  - `finishPage/Url` - page or URL to redirect user after the payment
     *  - `returnPage/Url` - page or URL to return user from payment system on success
     *  - `cancelPage/Url` - page or URL to return user from payment system on fail
     *  - `notifyPage/Url` - page or URL used by payment system to notify us on successful payment
     * @return \yii\web\Response
     */
    public function renderDeposit(array $data)
    {
        $merchants = $this->getMerchantModule()->getCollection($data)->getItems();
        $requests = [];
        foreach ($merchants as $id => $merchant) {
            $requests[$id] = $merchant->request('purchase', $this->getMerchantModule()->prepareRequestData($id, $data));
        }

        return $this->render('deposit', compact('requests'));
    }

    /**
     * Performs purchase request.
     * @void
     */
    public function actionRequest()
    {
        $merchant   = Yii::$app->request->post('merchant');
        if (empty($merchant)) {
            throw new BadRequestHttpException('Merchant is missing');
        }

        $data       = Json::decode(Yii::$app->request->post('data', '{}'));
        $merchant   = $this->getMerchantModule()->getMerchant($merchant, $data);
        $request    = $merchant->request('purchase', $data);
        $this->getMerchantModule()->insertTransaction(array_merge($data, [
            'username' => $this->getMerchantModule()->getUsername()
        ]));

        $response   = $request->send();

        if ($response->isSuccessful()) {
            throw new InvalidCallException('Instant payment is not implemented yet');
//            $merchant->registerMoney($response);
        } elseif ($response->isRedirect()) {
            $response->redirect();
        } else {
            throw new UserException('Merchant request failed');
        }
    }
}
