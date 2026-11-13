<?php

namespace hiqdev\yii2\merchant\tests\support;

use hiqdev\yii2\merchant\controllers\PayController;
use hiqdev\yii2\merchant\transactions\Transaction;

/**
 * Pay controller whose notification check returns a preset transaction.
 */
class FakePayController extends PayController
{
    public ?Transaction $notifiedTransaction = null;

    public ?string $checkedTransactionId = null;

    public function checkNotify(?string $transactionId = null): ?Transaction
    {
        $this->checkedTransactionId = $transactionId;

        return $this->notifiedTransaction;
    }
}
