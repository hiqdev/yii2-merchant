<?php

/*
 * Yii2 extension for payment processing with Omnipay, Payum and more later
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\models;

use Yii;

/**
 * Class Deposit
 * @package hiqdev\yii2\merchant\models
 */
class Deposit extends \yii\base\Model
{
    /**
     * @var double the amount of money
     */
    public $amount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount'], 'number'],
        ];
    }

    public function attributes()
    {
        return ['amount'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'amount' => Yii::t('merchant', 'Amount'),
        ];
    }
}
