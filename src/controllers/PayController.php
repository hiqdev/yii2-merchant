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

use hiqdev\yii2\merchant\models\Deposit;
use Yii;
use yii\base\UserException;
use yii\helpers\Json;

class PayController extends \yii\web\Controller
{
    /**
     * Disable CSRF validation for POST requests we receive from outside.
     */
    public function beforeAction()
    {
        if (in_array($this->action->id, ['notify', 'return', 'cancel'], true)) {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return true;
    }

    public function actionCancel()
    {
        Yii::$app->session->addFlash('error', Yii::t('merchant', 'Payment failed or cancelled'));
        $back = $this->module->previousUrl();
        $this->redirect($back ?: ['deposit']);
    }

    public function actionReturn()
    {
        Yii::$app->session->addFlash('success', Yii::t('merchant', 'Payment performed successfully'));
        $back = $this->module->previousUrl();
        $this->redirect($back ?: ['deposit']);
    }

    public function actionDeposit()
    {
        $model   = Yii::createObject($this->module->depositClass);
        $request = Yii::$app->request;
        if (!$model->load($request->isPost ? $request->post() : $request->get())) {
            return $this->render('deposit-form', compact('model'));
        }
        $params = array_merge($model->getAttributes(), ['back' => $request->get('back')]);

        return $this->renderDeposit($params);
    }

    /**
     * Renders depositing buttons for given params.
     *
     * @param array $params array of supported params: sum, currency, back
     *
     * @return \yii\web\Response
     */
    public function renderDeposit(array $params)
    {
        if ($params['back']) {
            $this->module->rememberUrl($params['back']);
        }
        $params['description']   = Yii::$app->request->getServerName() . ' deposit: ' . Yii::$app->user->identity->username;
        $params['transactionId'] = Yii::$app->user->identity->username . ':' . $params['sum'];
        $merchants = $this->module->getCollection($params)->getItems();
        $requests = [];
        foreach ($merchants as $id => $merchant) {
            $requests[$id] = $merchant->request('purchase', $params);
        }

        return $this->render('deposit', compact('requests'));
    }

    public function actionRequest()
    {
        $merchant   = Yii::$app->request->post('merchant');
        $data       = Json::decode(Yii::$app->request->post('data'));
        $merchant   = $this->module->getMerchant($merchant, $data);
        $request    = $merchant->request('purchase', $data);
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
