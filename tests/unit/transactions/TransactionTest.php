<?php

namespace hiqdev\yii2\merchant\tests\unit\transactions;

use hiqdev\yii2\merchant\transactions\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testNewTransactionIsPending(): void
    {
        $transaction = new Transaction('tx-1', 'card_usd');

        $this->assertSame('tx-1', $transaction->getId());
        $this->assertSame('card_usd', $transaction->getMerchant());
        $this->assertNull($transaction->getSuccess());
        $this->assertFalse($transaction->isCompleted());
        $this->assertFalse($transaction->isConfirmed());
    }

    public function testComplete(): void
    {
        $transaction = new Transaction('tx-1', 'card_usd');
        $transaction->complete();

        $this->assertTrue($transaction->isCompleted());
        $this->assertTrue($transaction->isConfirmed());
        $this->assertTrue($transaction->getSuccess());
    }

    public function testCancelStoresError(): void
    {
        $transaction = new Transaction('tx-1', 'card_usd');
        $transaction->cancel('Insufficient funds');

        $this->assertTrue($transaction->isCompleted());
        $this->assertFalse($transaction->isConfirmed());
        $this->assertFalse($transaction->getSuccess());
        $this->assertSame('Insufficient funds', $transaction->getParameter('error'));
    }

    public function testAddParameterDoesNotOverwrite(): void
    {
        $transaction = new Transaction('tx-1', 'card_usd');
        $transaction->setParameters(['username' => 'alice']);

        $transaction->addParameter('username', 'mallory');
        $transaction->addParameter('currency', 'usd');

        $this->assertSame(['username' => 'alice', 'currency' => 'usd'], $transaction->getParameters());
        $this->assertNull($transaction->getParameter('missing'));
    }

    public function testToArray(): void
    {
        $transaction = new Transaction('tx-1', 'card_usd');
        $transaction->setParameters(['amount' => '10']);
        $transaction->complete();

        $this->assertSame([
            'id' => 'tx-1',
            'merchant' => 'card_usd',
            'parameters' => ['amount' => '10'],
            'success' => true,
        ], $transaction->toArray());
    }
}
