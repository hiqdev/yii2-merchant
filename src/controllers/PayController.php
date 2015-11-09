<?php

/*
 * Payment merchants extension for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\controllers;

use hiqdev\yii2\merchant\models\Deposit;
use Yii;

class PayController extends \yii\web\Controller
{
    /**
     * Disable CSRF validation for POST requests we receive from outside.
     */
    public function beforeAction()
    {
        if (in_array($this->action->id, ['confirm', 'success', 'failure'])) {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return true;
    }

    /*
    public function actions()
    {
        return [
            'confirm' => [
                'class' => 'hiqdev\yii2\merchant\actions\ConfirmAction',
            ],
            'success' => [
                'class' => 'hiqdev\yii2\merchant\actions\SuccessAction',
            ],
            'failure' => [
                'class' => 'hiqdev\yii2\merchant\actions\FailureAction',
            ],
        ];
    }
    */

    public function actionFailure()
    {
        $back = $this->module->previousUrl();
        $this->redirect($back ?: ['deposit']);
    }

    public function actionSuccess()
    {
        $back = $this->module->previousUrl();
        $this->redirect($back ?: ['deposit']);
    }

    public function actionDeposit()
    {
        $model = new Deposit();
        $back  = Yii::$app->request->get('back');
        $load  = $model->load(Yii::$app->request->post());
        $view  = $load ? 'proceed-deposit' : 'deposit';
        /* if ($load) {
            $this->module->fetchMerchants($model->getAttributes());
        } */
        if ($back) {
            $this->module->rememberUrl($back);
        }

        return $this->render($view, compact('model'));
    }

}
