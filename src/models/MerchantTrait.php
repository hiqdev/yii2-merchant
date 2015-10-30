<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (https://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\models;

use Yii;

trait MerchantTrait
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'system'],    'string'],
            [['purse'],             'string'],
            [['currency'],          'string'],
            [['username'],          'string'],
            [['signature'],         'string'],
            [['total', 'fee'],      'number'],
            [['commission'],        'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'          => Yii::t('merchant', 'Name'),
            'system'        => Yii::t('merchant', 'Payment System'),
            'purse'         => Yii::t('merchant', 'Purse'),
            'total'         => Yii::t('merchant', 'Total'),
            'fee'           => Yii::t('merchant', 'Fee'),
            'currency'      => Yii::t('merchant', 'Currency'),
            'signature'     => Yii::t('merchant', 'Signature'),
            'commission'    => Yii::t('merchant', 'Commission'),
        ];
    }
}
