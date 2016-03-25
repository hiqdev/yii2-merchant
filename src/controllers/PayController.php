<?php

/*
 * Yii2 extension for payment processing with Omnipay, Payum and more later
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\controllers;

use hiqdev\yii2\merchant\models\Deposit;
use hiqdev\yii2\merchant\Module;
use Yii;
use yii\base\InvalidCallException;
use yii\base\UserException;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\Session;

class PayController extends \yii\web\Controller
{
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
     * @param string $transactionId
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
        $data = $this->getMerchantModule()->readHistory($transactionId);

        if ($data['username'] !== $this->getMerchantModule()->username) {
            throw new BadRequestHttpException('Access denied', 403);
        }

        return [
            'status' => $data['_isCompleted'],
            'url'    => $data['_isCompleted'] ? $data['finishUrl'] : $data['cancelUrl'],
        ];
    }

    /**
     * Action is designed to get the system notification from payment system,
     * process it and report success or error for the payment system.
     * @return null|string
     */
    public function actionNotify()
    {
        $result = $this->checkNotify();
        Yii::$app->response->format = Response::FORMAT_RAW;

        return $result['_isCompleted'] ? 'OK' : $result['_error'];
    }

    /**
     * Check notifications.
     * TODO: implement actual request check and proper handling.
     * @return array
     */
    public function checkNotify()
    {
        $result = $_REQUEST;
        return $this->completeHistory($result);
    }

    public function actionDeposit()
    {
        $model   = Yii::createObject($this->getMerchantModule()->depositClass);
        $request = Yii::$app->request;
        if ($model->load($request->isPost ? $request->post() : $request->get()) && $model->validate()) {
            return $this->renderDeposit($model->getAttributes());
        }

        return $this->render('deposit-form', compact('model'));
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
        $data       = Json::decode(Yii::$app->request->post('data', '{}'));
        $merchant   = $this->getMerchantModule()->getMerchant($merchant, $data);
        $request    = $merchant->request('purchase', $data);

        $this->getMerchantModule()->writeHistory(array_merge($data, ['username' => $this->getMerchantModule()->username]));

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
