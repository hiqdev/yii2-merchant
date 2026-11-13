<?php

namespace hiqdev\yii2\merchant\tests\unit\controllers;

use hiqdev\yii2\merchant\controllers\PayController;
use hiqdev\yii2\merchant\tests\support\FakeIdentity;
use hiqdev\yii2\merchant\tests\support\FakePayController;
use hiqdev\yii2\merchant\tests\support\TestCase;
use hiqdev\yii2\merchant\transactions\Transaction;
use hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PayControllerTest extends TestCase
{
    private function createController(?TransactionRepositoryInterface $repository = null): FakePayController
    {
        $this->mockApplication($repository ?? $this->createStub(TransactionRepositoryInterface::class), ['username' => 'alice']);

        return new FakePayController('pay', $this->getMerchantModule());
    }

    private function transaction(string $username): Transaction
    {
        $transaction = new Transaction('tx-1', 'card_usd');
        $transaction->setParameters(['username' => $username]);

        return $transaction;
    }

    public function testCheckReturnReportsConfirmedTransaction(): void
    {
        $transaction = $this->transaction('alice');
        $transaction->complete();
        $repository = $this->createStub(TransactionRepositoryInterface::class);
        $repository->method('findById')->willReturn($transaction);

        $result = $this->createController($repository)->actionCheckReturn('tx-1');

        $this->assertSame(['status' => true, 'url' => '/merchant/pay/deposit'], $result);
        $this->assertSame(Response::FORMAT_JSON, Yii::$app->response->format);
    }

    public function testCheckReturnUsesFailedRouteForCancelledTransaction(): void
    {
        $transaction = $this->transaction('alice');
        $transaction->cancel('declined');
        $repository = $this->createStub(TransactionRepositoryInterface::class);
        $repository->method('findById')->willReturn($transaction);
        $controller = $this->createController($repository);
        $controller->failedPaymentRedirectRoute = ['/billing/failed'];

        $this->assertSame(['status' => false, 'url' => '/billing/failed'], $controller->actionCheckReturn('tx-1'));
    }

    public function testCheckReturnRejectsOtherUsersTransaction(): void
    {
        $repository = $this->createStub(TransactionRepositoryInterface::class);
        $repository->method('findById')->willReturn($this->transaction('mallory'));

        $this->expectException(BadRequestHttpException::class);
        $this->createController($repository)->actionCheckReturn('tx-1');
    }

    public function testCheckReturnUnknownTransaction(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->createController()->actionCheckReturn('missing');
    }

    public function testNotifyAnswersOkForConfirmedTransaction(): void
    {
        $controller = $this->createController();
        $controller->notifiedTransaction = new Transaction('tx-1', 'card_usd');
        $controller->notifiedTransaction->complete();

        $this->assertSame('OK', $controller->actionNotify('tx-1'));
        $this->assertSame('tx-1', $controller->checkedTransactionId);
        $this->assertSame(Response::FORMAT_RAW, Yii::$app->response->format);
    }

    public function testNotifyAnswersErrorForCancelledTransaction(): void
    {
        $controller = $this->createController();
        $controller->notifiedTransaction = new Transaction('tx-1', 'card_usd');
        $controller->notifiedTransaction->cancel('Signature mismatch');

        $this->assertSame('Signature mismatch', $controller->actionNotify());
    }

    public function testNotifyUnknownTransaction(): void
    {
        $this->assertSame('Unknown transaction', $this->createController()->actionNotify('tx-1'));
    }

    public function testCancelFlashesErrorAndRedirectsToRememberedUrl(): void
    {
        $controller = $this->createController();
        $this->getMerchantModule()->rememberUrl('/billing/deposit');

        $response = $controller->actionCancel();

        $this->assertSame('https://shop.example.com/billing/deposit', $response->headers->get('Location'));
        $this->assertSame(['Payment failed or cancelled'], Yii::$app->session->getFlash('error'));
    }

    public function testReturnWithUnknownTransactionActsAsCancel(): void
    {
        $controller = $this->createController();
        Yii::$app->controller = $controller;

        $response = $controller->actionReturn('tx-1');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('https://shop.example.com/merchant/pay/deposit', $response->headers->get('Location'));
    }

    public function testDepositIsForbiddenWithoutPermission(): void
    {
        $controller = $this->createController();
        Yii::$app->user->setIdentity(new FakeIdentity(['id' => 1]));

        $this->expectException(ForbiddenHttpException::class);
        $controller->actionDeposit();
    }

    public function testBaseControllerRequiresNotifyCheckImplementation(): void
    {
        $this->mockApplication($this->createStub(TransactionRepositoryInterface::class));
        $controller = new PayController('pay', $this->getMerchantModule());

        $this->expectException(InvalidConfigException::class);
        $controller->checkNotify('tx-1');
    }
}
