<?php

namespace hiqdev\yii2\merchant\tests\support;

use hiqdev\yii2\merchant\Module;
use hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface;
use Yii;
use yii\di\Container;
use yii\web\Application;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function mockApplication(TransactionRepositoryInterface $repository, array $moduleConfig = [], array $config = []): Application
    {
        Yii::$container->setSingleton(TransactionRepositoryInterface::class, $repository);
        Yii::setAlias('@pay', '/merchant/pay');

        return new Application(array_replace_recursive([
            'id' => 'merchant-test',
            'basePath' => dirname(__DIR__, 2),
            'vendorPath' => dirname(__DIR__, 2) . '/vendor',
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'test',
                    'enableCsrfValidation' => false,
                    'hostInfo' => 'https://shop.example.com',
                    'scriptUrl' => '/index.php',
                    'url' => '/',
                ],
                'urlManager' => [
                    'enablePrettyUrl' => true,
                    'showScriptName' => false,
                ],
                'session' => ['class' => ArraySession::class],
                'user' => [
                    'identityClass' => FakeIdentity::class,
                    'enableSession' => false,
                ],
                'i18n' => [
                    'translations' => [
                        'merchant' => [
                            'class' => \yii\i18n\PhpMessageSource::class,
                            'sourceLanguage' => 'en-US',
                            'basePath' => dirname(__DIR__, 2) . '/src/messages',
                        ],
                    ],
                ],
            ],
            'modules' => [
                'merchant' => array_merge([
                    'class' => Module::class,
                    'purchaseRequestCollectionClass' => FakePurchaseRequestCollection::class,
                    'currenciesCollectionClass' => FakeCurrencies::class,
                ], $moduleConfig),
            ],
        ], $config));
    }

    protected function getMerchantModule(): Module
    {
        return Yii::$app->getModule('merchant');
    }

    protected function tearDown(): void
    {
        Yii::$app = null;
        Yii::$container = new Container();
        parent::tearDown();
    }
}
