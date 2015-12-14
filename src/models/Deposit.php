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
use yii\base\Model;

/**
 * Class Deposit.
 */
class Deposit extends Model
{
    /**
     * @var float the amount of money
     */
    public $amount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount'], 'number'],
            [['amount'], 'required'],
            [['amount'], 'compare', 'operator' => '>', 'compareValue' => 0],
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
