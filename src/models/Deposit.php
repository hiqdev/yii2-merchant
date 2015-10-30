<?php

namespace hiqdev\yii2\merchant\models;

use Yii;

class Deposit extends \yii\base\Model
{
    public $sum;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sum'], 'number'],
        ];
    }

    public function attributes()
    {
        return ['sum'];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sum' => Yii::t('merchant', 'Sum'),
        ];
    }
}
