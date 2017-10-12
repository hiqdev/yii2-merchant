<?php
/**
 * Yii2 extension for payment processing with Omnipay, Payum and more later.
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\models;

use Yii;

/**
 * Class MerchantTrait.
 */
trait MerchantTrait
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'system'],    'string'],
            [['purse'],             'string'],
            [['method'],            'string'],
            [['currency'],          'string'],
            [['label'],             'string'],
            [['amount', 'fee'],     'number'],
            [['commission'],        'number'],
            [['invoice_id'],        'number'],
            [['action'],            'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name'       => Yii::t('merchant', 'Name'),
            'system'     => Yii::t('merchant', 'Payment System'),
            'purse'      => Yii::t('merchant', 'Purse'),
            'amount'      => Yii::t('merchant', 'Amount'),
            'fee'        => Yii::t('merchant', 'Fee'),
            'currency'   => Yii::t('merchant', 'Currency'),
            'signature'  => Yii::t('merchant', 'Signature'),
            'commission' => Yii::t('merchant', 'Commission'),
        ];
    }
}
