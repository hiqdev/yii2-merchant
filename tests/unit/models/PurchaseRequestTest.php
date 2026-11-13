<?php

namespace hiqdev\yii2\merchant\tests\unit\models;

use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\yii2\merchant\models\PurchaseRequest;
use hiqdev\yii2\merchant\tests\support\TestCase;
use hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use yii\base\InvalidConfigException;

class PurchaseRequestTest extends TestCase
{
    public function testWithoutForm(): void
    {
        $request = new PurchaseRequest();

        $this->assertSame([], $request->getFormInputs());
        $this->assertSame('POST', $request->getFormMethod());

        $this->expectException(InvalidConfigException::class);
        $request->getFormAction();
    }

    public function testWithForm(): void
    {
        $request = new PurchaseRequest();
        $request->setForm((new RedirectPurchaseResponse('https://pay.example.com/go', ['sum' => '10']))->setMethod('GET'));

        $this->assertSame(['sum' => '10'], $request->getFormInputs());
        $this->assertSame('https://pay.example.com/go', $request->getFormAction());
        $this->assertSame('GET', $request->getFormMethod());
    }

    public function testEmptyFormActionThrows(): void
    {
        $request = new PurchaseRequest();
        $request->setForm(new RedirectPurchaseResponse(''));

        $this->expectException(InvalidConfigException::class);
        $request->getFormAction();
    }

    public static function paymentMethodProvider(): array
    {
        return [
            'other system' => ['card', 'AC', null],
            'no method' => ['yandexmoney', null, null],
            'bank card' => ['yandexmoney', 'AC', 'via bank card'],
            'account balance' => ['yandexmoney', 'PC', 'via account balance'],
            'anything else' => ['yandexmoney', 'MC', 'via phone'],
        ];
    }

    #[DataProvider('paymentMethodProvider')]
    public function testPaymentMethodLabel(string $system, ?string $method, ?string $expected): void
    {
        $this->mockApplication($this->createStub(TransactionRepositoryInterface::class));
        $request = new PurchaseRequest();
        $request->system = $system;
        $request->paymentMethod = $method;

        $this->assertSame($expected, $request->getPaymentMethodLabel());
    }
}
