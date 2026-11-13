<?php

namespace hiqdev\yii2\merchant\tests\unit\models;

use hiqdev\yii2\merchant\models\DepositRequest;
use PHPUnit\Framework\TestCase;

class DepositRequestTest extends TestCase
{
    public function testValidRequest(): void
    {
        $request = new DepositRequest();
        $loaded = $request->load(['DepositRequest' => [
            'id' => 'tx-1',
            'amount' => '10',
            'currency' => 'usd',
            'merchant' => 'card_usd',
            'finishUrl' => '/done',
            'username' => 'mallory',
        ]]);

        $this->assertTrue($loaded);
        $this->assertTrue($request->validate());
        $this->assertSame('/done', $request->finishUrl);
        $this->assertNull($request->username, 'username is not a safe attribute');
    }

    public function testRequiredAttributes(): void
    {
        $request = new DepositRequest();

        $this->assertFalse($request->validate());
        $this->assertSame(['id', 'amount', 'merchant'], array_keys($request->getErrors()));
    }

    public function testAmountMustBeNumber(): void
    {
        $request = new DepositRequest(['id' => 'tx-1', 'merchant' => 'card_usd', 'amount' => 'ten']);

        $this->assertFalse($request->validate());
        $this->assertTrue($request->hasErrors('amount'));
    }
}
