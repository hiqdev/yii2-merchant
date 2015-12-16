<?php

/*
 * Yii2 extension for payment processing with Omnipay, Payum and more later
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\controllers;

use hipanel\base\Err;
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
    /**
     * @var Module
     * {@inheritdoc}
     */
    public $module;
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

        return $this->redirect($this->module->previousUrl() ?: ['deposit']);
    }

    /**
     * @param string $transactionId
     * @return string
     */
    public function actionReturn($transactionId)
    {
        return $this->render('return', [
            'transactionId' => $transactionId,
            'back'          => $this->module->previousUrl(),
        ]);
    }

    public function actionCheckReturn($transactionId)
    {
        $result = ['status' => null];
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (empty($data = $this->module->readHistory($transactionId))) {
            return [];
        }
        if ($data['username'] !== $this->module->username) {
            throw new BadRequestHttpException('Access denied', 403);
        }

        if ($data['_isCompleted']) {
            $result['status'] = true;
            $result['url'] = $this->module->previousUrl();
        }

        return $result;
    }

    /**
     * @return null|string
     */
    public function actionNotify()
    {
        // TODO: implement request check and proper handling
        return $this->renderNotify($_REQUEST);
    }

    /**
     * @param array $params
     * @return null|string
     */
    public function renderNotify(array $params)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $params['_isCompleted'] = Err::not($params) && $params['id'];
        $this->module->updateHistory($params['transactionId'], $params);

        return Err::is($params) ? Err::get($params) : 'OK';
    }

    public function actionDeposit()
    {
        $model   = Yii::createObject($this->module->depositClass);
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
     *  - `finishPage` - the URL for user redirect after the payment
     * @return \yii\web\Response
     */
    public function renderDeposit(array $data)
    {
        $this->module->rememberUrl(isset($data['finishPage']) ? $data['finishPage'] : $this->module->finishPage);

        $merchants = $this->module->getCollection($data)->getItems();
        $requests = [];
        foreach ($merchants as $id => $merchant) {
            $requests[$id] = $merchant->request('purchase', $this->module->prepareRequestData($id, $data));
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
        $merchant   = $this->module->getMerchant($merchant, $data);
        $request    = $merchant->request('purchase', $data);

        $this->module->writeHistory(
            $data['transactionId'],
            array_merge($data, ['username' => $this->module->username])
        );

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
