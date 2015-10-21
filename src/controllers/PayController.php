<?php

namespace hiqdev\yii2\merchant\controllers;

class PayController extends \yii\web\Controller
{
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
}
