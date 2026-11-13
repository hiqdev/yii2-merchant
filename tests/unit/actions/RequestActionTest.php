<?php

namespace hiqdev\yii2\merchant\tests\unit\actions;

use hiqdev\yii2\merchant\actions\RequestAction;
use hiqdev\yii2\merchant\events\TransactionInsertEvent;
use hiqdev\yii2\merchant\tests\support\TestCase;
use hiqdev\yii2\merchant\transactions\Transaction;
use hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface;
use Yii;
use yii\base\Event;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class RequestActionTest extends TestCase
{
    private array $events = [];

    protected function setUp(): void
    {
        foreach ([RequestAction::EVENT_BEFORE_TRANSACTION_INSERT, RequestAction::EVENT_AFTER_TRANSACTION_INSERT] as $name) {
            Event::on(RequestAction::class, $name, function (TransactionInsertEvent $event) {
                $this->events[] = [$event->name, $event->depositRequest->id, $event->transaction];
            });
        }
    }

    protected function tearDown(): void
    {
        Event::offAll();
        parent::tearDown();
    }

    private function runRequest(TransactionRepositoryInterface $repository, array $post)
    {
        $this->mockApplication($repository, ['username' => 'alice']);
        Yii::$app->request->setBodyParams(['DepositRequest' => $post]);

        return $this->getMerchantModule()->getPayController()->runAction('request');
    }

    public function testPostGatewayRendersAutoSubmitForm(): void
    {
        $transaction = new Transaction('tx-1', 'card_usd');
        $repository = $this->createMock(TransactionRepositoryInterface::class);
        $repository->expects($this->once())->method('create')
            ->with('tx-1', 'card_usd', $this->callback(function (array $data) {
                return $data['username'] === 'alice'
                    && $data['currency'] === 'usd'
                    && $data['amount'] === '10.5'
                    && str_starts_with($data['notifyUrl'], 'https://shop.example.com/merchant/pay/notify?');
            }))
            ->willReturn($transaction);
        $repository->expects($this->once())->method('insert')->with($transaction)->willReturn($transaction);

        $output = $this->runRequest($repository, ['id' => 'tx-1', 'amount' => '10.5', 'currency' => 'usd', 'merchant' => 'card_usd']);

        $this->assertIsString($output);
        $this->assertStringContainsString('<form action="https://pay.example.com/card_usd" method="post">', $output);
        $this->assertStringContainsString('<input type="hidden" name="amount" value="10.5" />', $output);
        $this->assertStringContainsString('<input type="hidden" name="transactionId" value="tx-1" />', $output);
        $this->assertSame([
            [RequestAction::EVENT_BEFORE_TRANSACTION_INSERT, 'tx-1', null],
            [RequestAction::EVENT_AFTER_TRANSACTION_INSERT, 'tx-1', $transaction],
        ], $this->events);
    }

    public function testGetGatewayRedirects(): void
    {
        $repository = $this->createStub(TransactionRepositoryInterface::class);
        $repository->method('create')->willReturn(new Transaction('tx-2', 'wallet_eur'));

        $response = $this->runRequest($repository, ['id' => 'tx-2', 'amount' => '5', 'currency' => 'eur', 'merchant' => 'wallet_eur']);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(302, $response->statusCode);
        $this->assertSame('https://pay.example.com/wallet_eur', $response->headers->get('Location'));
    }

    public function testInvalidDepositRequestIsRejectedBeforeAnyTransaction(): void
    {
        $repository = $this->createMock(TransactionRepositoryInterface::class);
        $repository->expects($this->never())->method('create');
        $repository->expects($this->never())->method('insert');

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Deposit request is not loaded');

        $this->runRequest($repository, ['amount' => '5', 'merchant' => 'card_usd']);
    }
}
