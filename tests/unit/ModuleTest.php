<?php
/**
 * Yii2 extension for payment processing with Omnipay, Payum and more later.
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\tests\unit;

use hiqdev\yii2\merchant\controllers\PayController;
use hiqdev\yii2\merchant\models\DepositRequest;
use hiqdev\yii2\merchant\models\PurchaseRequest;
use hiqdev\yii2\merchant\Module;
use hiqdev\yii2\merchant\tests\support\FakeCurrencies;
use hiqdev\yii2\merchant\tests\support\FakeIdentity;
use hiqdev\yii2\merchant\tests\support\FakeIdentityWithUsername;
use hiqdev\yii2\merchant\tests\support\FakePurchaseRequestCollection;
use hiqdev\yii2\merchant\tests\support\TestCase;
use hiqdev\yii2\merchant\transactions\Transaction;
use hiqdev\yii2\merchant\transactions\TransactionException;
use hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Yii;
use yii\base\InvalidConfigException;

class ModuleTest extends TestCase
{
    private function createModule(array $moduleConfig = [], ?TransactionRepositoryInterface $repository = null): Module
    {
        $this->mockApplication($repository ?? $this->createStub(TransactionRepositoryInterface::class), $moduleConfig);

        return $this->getMerchantModule();
    }

    private function depositRequest(array $attributes = []): DepositRequest
    {
        return new DepositRequest(array_merge([
            'id' => 'tx-1',
            'amount' => '10.50',
            'currency' => 'usd',
            'merchant' => 'card_usd',
        ], $attributes));
    }

    public function testModuleIsCreatedWithRepositoryFromContainer(): void
    {
        $repository = $this->createMock(TransactionRepositoryInterface::class);
        $transaction = new Transaction('tx-1', 'card_usd');
        $repository->expects($this->once())->method('findById')->with('tx-1')->willReturn($transaction);

        $module = $this->createModule([], $repository);

        $this->assertInstanceOf(Module::class, $module);
        $this->assertSame('merchant', $module->id);
        $this->assertSame($transaction, $module->findTransaction('tx-1'));
    }

    public function testDefaults(): void
    {
        $module = new Module('merchant', null, $this->createStub(TransactionRepositoryInterface::class));

        $this->assertSame('notify', $module->notifyPage);
        $this->assertSame('return', $module->returnPage);
        $this->assertSame('cancel', $module->cancelPage);
        $this->assertSame('finish', $module->finishPage);
        $this->assertFalse($module->cashewOnly);
        $this->assertSame(\hiqdev\yii2\merchant\Collection::class, $module->purchaseRequestCollectionClass);
        $this->assertSame(\hiqdev\yii2\merchant\models\DepositForm::class, $module->depositFromClass);
    }

    public function testGetPurchaseRequestCollection(): void
    {
        $module = $this->createModule();
        $depositRequest = $this->depositRequest();

        $collection = $module->getPurchaseRequestCollection($depositRequest);

        $this->assertInstanceOf(FakePurchaseRequestCollection::class, $collection);
        $this->assertSame($module, $collection->module);
        $this->assertSame($depositRequest, $collection->depositRequest);
        $this->assertSame(['card_usd', 'wallet_eur'], array_keys($collection->getItems()));
        $this->assertContainsOnlyInstancesOf(PurchaseRequest::class, $collection->getItems());
    }

    public function testGetPurchaseRequest(): void
    {
        $module = $this->createModule();

        $request = $module->getPurchaseRequest('wallet_eur', $this->depositRequest(['id' => 'tx-7', 'amount' => '42']));

        $this->assertInstanceOf(PurchaseRequest::class, $request);
        $this->assertSame('wallet_eur', $request->merchant_name);
        $this->assertSame('wallet', $request->system);
        $this->assertSame('42', $request->amount);
        $this->assertSame('GET', $request->getFormMethod());
        $this->assertSame(['amount' => '42', 'transactionId' => 'tx-7'], $request->getFormInputs());
    }

    public function testHasPurchaseRequest(): void
    {
        $module = $this->createModule();

        $this->assertTrue($module->hasPurchaseRequest('card_usd'));
        $this->assertTrue($module->hasPurchaseRequest('wallet_eur'));
        $this->assertFalse($module->hasPurchaseRequest('unknown'));
    }

    public function testGetAvailableCurrenciesCollection(): void
    {
        $module = $this->createModule();

        $currencies = $module->getAvailableCurrenciesCollection();

        $this->assertInstanceOf(FakeCurrencies::class, $currencies);
        $this->assertSame($module, $currencies->module);
        $this->assertSame(['USD', 'EUR'], array_map(fn ($currency) => $currency->code, $currencies->getList()));
    }

    public function testExplicitUsername(): void
    {
        $module = $this->createModule(['username' => 'alice']);

        $this->assertSame('alice', $module->getUsername());
    }

    public function testUsernameFromIdentityProperty(): void
    {
        $module = $this->createModule();
        Yii::$app->user->setIdentity(new FakeIdentityWithUsername(['id' => 5, 'username' => 'bob']));

        $this->assertSame('bob', $module->getUsername());
    }

    public function testUsernameFallsBackToIdentityId(): void
    {
        $module = $this->createModule();
        Yii::$app->user->setIdentity(new FakeIdentity(['id' => 17]));

        $this->assertSame(17, $module->getUsername());
    }

    public function testUsernameIsRememberedAfterFirstLookup(): void
    {
        $module = $this->createModule();
        Yii::$app->user->setIdentity(new FakeIdentityWithUsername(['id' => 5, 'username' => 'bob']));
        $module->getUsername();
        Yii::$app->user->setIdentity(new FakeIdentityWithUsername(['id' => 6, 'username' => 'carol']));

        $this->assertSame('bob', $module->getUsername());
    }

    public function testUsernameWithoutIdentityThrows(): void
    {
        $module = $this->createModule();

        $this->expectException(InvalidConfigException::class);
        $module->getUsername();
    }

    public static function localizePageProvider(): array
    {
        return [
            'relative page goes to the module pay controller' => ['notify', '/merchant/pay/notify'],
            'absolute route is kept' => ['/my/notify/page', '/my/notify/page'],
            'array route is kept' => [['/site/index'], ['/site/index']],
        ];
    }

    #[DataProvider('localizePageProvider')]
    public function testLocalizePage(string|array $page, string|array $expected): void
    {
        $this->assertSame($expected, $this->createModule()->localizePage($page));
    }

    public function testGetPagePrefersUrlFromDepositRequest(): void
    {
        $module = $this->createModule(['returnPage' => '/configured/return']);

        $this->assertSame('/custom/return', $module->getPage('return', $this->depositRequest(['returnUrl' => '/custom/return'])));
        $this->assertSame('/configured/return', $module->getPage('return', $this->depositRequest()));
        $this->assertSame('cancel', $module->getPage('cancel', $this->depositRequest()));
    }

    public function testBuildUrl(): void
    {
        $module = $this->createModule(['notifyPage' => '/my/notify/page']);
        $depositRequest = $this->depositRequest(['username' => 'alice']);

        $this->assertSame(
            'https://shop.example.com/my/notify/page?username=alice&merchant=card_usd&transactionId=tx-1',
            $module->buildUrl('notify', $depositRequest)
        );
        $this->assertSame(
            'https://shop.example.com/merchant/pay/return?username=alice&merchant=card_usd&transactionId=tx-1',
            $module->buildUrl('return', $depositRequest)
        );
    }

    public function testPrepareRequestData(): void
    {
        $module = $this->createModule(['username' => 'alice', 'cancelPage' => '/my/cancel']);
        $depositRequest = $this->depositRequest(['finishUrl' => '/my/finish']);

        $module->prepareRequestData($depositRequest);

        $query = '?username=alice&merchant=card_usd&transactionId=tx-1';
        $this->assertSame('alice', $depositRequest->username);
        $this->assertSame('https://shop.example.com/merchant/pay/notify' . $query, $depositRequest->notifyUrl);
        $this->assertSame('https://shop.example.com/merchant/pay/return' . $query, $depositRequest->returnUrl);
        $this->assertSame('https://shop.example.com/my/cancel' . $query, $depositRequest->cancelUrl);
        $this->assertSame('https://shop.example.com/my/finish' . $query, $depositRequest->finishUrl);
    }

    public function testRememberAndPreviousUrl(): void
    {
        $module = $this->createModule();

        $module->rememberUrl('/billing/deposit');
        $module->rememberUrl('/somewhere/else', 'other');

        $this->assertSame('/billing/deposit', $module->previousUrl());
        $this->assertSame('/somewhere/else', $module->previousUrl('other'));
        $this->assertSame('/billing/deposit', Yii::$app->session->get(Module::URL_PREFIX . 'back'));
    }

    public function testGetPayController(): void
    {
        $module = $this->createModule();

        $controller = $module->getPayController();

        $this->assertInstanceOf(PayController::class, $controller);
        $this->assertSame($module, $controller->module);
        $this->assertSame($controller, $module->getPayController());
    }

    public function testSaveTransaction(): void
    {
        $transaction = new Transaction('tx-1', 'card_usd');
        $repository = $this->createMock(TransactionRepositoryInterface::class);
        $repository->expects($this->once())->method('save')->with($transaction)->willReturn($transaction);

        $this->assertSame($transaction, $this->createModule([], $repository)->saveTransaction($transaction));
    }

    public function testInsertTransactionCreatesThenInserts(): void
    {
        $transaction = new Transaction('tx-1', 'card_usd');
        $repository = $this->createMock(TransactionRepositoryInterface::class);
        $repository->expects($this->once())->method('create')
            ->with('tx-1', 'card_usd', ['amount' => 10])
            ->willReturn($transaction);
        $repository->expects($this->once())->method('insert')->with($transaction)->willReturn($transaction);

        $this->assertSame($transaction, $this->createModule([], $repository)->insertTransaction('tx-1', 'card_usd', ['amount' => 10]));
    }

    public function testFindTransactionReturnsNullWhenRepositoryThrows(): void
    {
        $repository = $this->createMock(TransactionRepositoryInterface::class);
        $repository->expects($this->once())->method('findById')->with('missing')
            ->willThrowException(new TransactionException('not found'));

        $this->assertNull($this->createModule([], $repository)->findTransaction('missing'));
    }
}
