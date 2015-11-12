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
        $model = Yii::createObject($this->module->depositClass);
        $back  = Yii::$app->request->get('back');
        $load  = $model->load(Yii::$app->request->post());
        $view  = $load ? 'proceed-deposit' : 'deposit';
        if ($load) {
            $params = array_merge([
                'description' => Yii::$app->request->getServerName() . ' deposit: ' . Yii::$app->user->identity->username,
            ], $model->getAttributes());
            $merchants = $this->module->getCollection($params)->getItems();
            $requests = [];
            foreach ($merchants as $id => $merchant) {
                $requests[$id] = $merchant->request('purchase', $params);
            }
        }
        if ($back) {
            $this->module->rememberUrl($back);
        }

        return $this->render($view, compact('requests', 'model', 'params'));
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
