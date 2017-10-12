<?php

namespace hiqdev\yii2\merchant\models;

use yii\base\Model;

class DepositRequest extends Model
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $amount;

    /**
     * @var string
     */
    public $merchant;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $returnUrl;

    /**
     * @var string
     */
    public $notifyUrl;

    /**
     * @var string
     */
    public $cancelUrl;

    /**
     * @var string
     */
    public $finishUrl;

    public function rules()
    {
        return [
            [['id', 'merchant'], 'string'],
            [['amount'], 'number'],
            [['id', 'amount'], 'required'],
            [['merchant'], 'required'],
        ];
    }
}
