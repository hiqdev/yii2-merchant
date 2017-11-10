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
use yii\base\Model;

/**
 * Class Deposit.
 */
class DepositForm extends Model
{
    /**
     * @var float the amount of money
     */
    public $amount;

    /**
     * @var string the route that will be passed to merchant
     * in order to redirect user to a custom page
     */
    public $finishUrl;

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
