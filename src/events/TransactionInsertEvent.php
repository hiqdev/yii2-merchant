<?php

namespace hiqdev\yii2\merchant\events;

use hiqdev\yii2\merchant\models\DepositRequest;
use hiqdev\yii2\merchant\transactions\Transaction;
use yii\base\Event;

class TransactionInsertEvent extends Event
{
    /**
     * @var DepositRequest
     */
    public $depositRequest;
    /**
     * @var Transaction
     */
    public $transaction;
}
