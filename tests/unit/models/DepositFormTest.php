<?php

namespace hiqdev\yii2\merchant\tests\unit\models;

use hiqdev\yii2\merchant\models\DepositForm;
use hiqdev\yii2\merchant\tests\support\TestCase;
use hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;

class DepositFormTest extends TestCase
{
    protected function setUp(): void
    {
        $this->mockApplication($this->createStub(TransactionRepositoryInterface::class));
    }

    public function testValidAmountDefaultsCurrencyToUsd(): void
    {
        $form = new DepositForm(['amount' => '25.5']);

        $this->assertTrue($form->validate());
        $this->assertSame('usd', $form->currency);
    }

    public static function invalidAmountProvider(): array
    {
        return [
            'missing' => [null],
            'zero' => ['0'],
            'negative' => ['-5'],
            'not a number' => ['abc'],
            'too large' => ['99999999999999'],
        ];
    }

    #[DataProvider('invalidAmountProvider')]
    public function testInvalidAmount(?string $amount): void
    {
        $form = new DepositForm(['amount' => $amount, 'currency' => 'eur']);

        $this->assertFalse($form->validate());
        $this->assertTrue($form->hasErrors('amount'));
    }

    public function testLoadIgnoresUnsafeAttributes(): void
    {
        $form = new DepositForm();

        $this->assertTrue($form->load(['DepositForm' => ['amount' => '10', 'currency' => 'eur', 'finishUrl' => '/x']]));
        $this->assertSame('10', $form->amount);
        $this->assertSame('eur', $form->currency);
        $this->assertNull($form->finishUrl);
        $this->assertSame('Amount', $form->getAttributeLabel('amount'));
    }
}
