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
use yii\base\UserException;
use yii\helpers\Json;
use yii\web\Response;

class PayController extends \yii\web\Controller
{
    /**
     * @var Module
     *             {@inheritdoc}
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
     *
     * @return string
     */
    public function actionReturn($transactionId = null)
    {
        return $this->render('return', [
            'transactionId' => $transactionId,
            'back'          => $this->module->previousUrl(),
        ]);
    }

    public function actionCheckReturn($transactionId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = $this->module->readHistory($transactionId);
        $data = $data['username'] === Yii::$app->user->identity->username ? $data : [];

        return ['COMPLETED' => $data['COMPLETED']];
    }

    public function actionNotify()
    {
        return $this->renderNotify($_REQUEST);
    }

    public function renderNotify(array $params)
    {
        $params['COMPLETED'] = Err::not($params) && $params['id'];
        $this->module->updateHistory($params['transactionId'], $params);
        Yii::$app->getResponse()->headers->set('Content-Type', 'text/plain');

        return Err::is($params) ? Err::get($params) : 'OK';
    }

    public function actionDeposit()
    {
        $model   = Yii::createObject($this->module->depositClass);
        $request = Yii::$app->request;
        if ($model->load($request->isPost ? $request->post() : $request->get()) && $model->validate()) {
            $data = array_merge($model->getAttributes(), ['back' => $request->get('back')]);
            return $this->renderDeposit($data);
        }
        return $this->render('deposit-form', compact('model'));
    }

    /**
     * Renders depositing buttons for given request data.
     *
     * @param array $data request data: sum, currency, back
     *
     * @return \yii\web\Response
     */
    public function renderDeposit(array $data)
    {
        if ($data['back']) {
            $this->module->rememberUrl($data['back']);
        }

        $merchants = $this->module->getCollection($data)->getItems();
        $requests = [];
        foreach ($merchants as $id => $merchant) {
            $requests[$id] = $merchant->request('purchase', $this->module->prepareRequestData($id, $data));
        }

        return $this->render('deposit', compact('requests'));
    }

    /**
     * Performs purchase request.
     */
    public function actionRequest()
    {
        $merchant   = Yii::$app->request->post('merchant');
        $data       = Json::decode(Yii::$app->request->post('data'));
        $merchant   = $this->module->getMerchant($merchant, $data);
        $request    = $merchant->request('purchase', $data);
        $this->module->writeHistory($data['transactionId'], $data);
        $response   = $request->send();
        if ($response->isSuccessful()) {
            $merchant->registerMoney($response);
        } elseif ($response->isRedirect()) {
            $response->redirect();
        } else {
            throw new UserException('merchant request failed');
        };
    }
}
