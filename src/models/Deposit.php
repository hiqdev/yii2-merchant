<?php

namespace hiqdev\yii2\merchant\models;

use Yii;

class Deposit extends \yii\base\Model
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sum'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'sum' => Yii::t('app', 'Sum'),
        ]);
    }
}
