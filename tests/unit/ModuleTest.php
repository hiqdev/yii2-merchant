<?php

/*
 * Yii2 extension for payment processing with Omnipay, Payum and more later
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\tests\unit;

use hiqdev\php\merchant\OmnipayMerchant;
use hiqdev\yii2\merchant\Collection;
use hiqdev\yii2\merchant\Module;
use Yii;
use yii\web\Application;

/**
 * Module test suite.
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Module
     */
    protected $module;

    protected $gateways = [
        'Stripe' => [
            'purse'     => 'asd',
            'secret'    => '123',
        ],
        'wmusd' => [
            'gateway'   => 'WebMoney',
            'purse'     => 'Z0000000',
            'secret'    => '123',
        ],
        'fake' => [
            'class'     => 'hiqdev\yii2\merchant\tests\unit\FakeMerchant',
            'purse'     => 'SDF',
            'secret'    => '000',
        ],
    ];

    protected function setUp()
    {
        $application = new Application([
            'id'       => 'fake',
            'basePath' => dirname(dirname(__DIR__)),
            'modules'  => [
                'merchant' => [
                    'class'      => Module::class,
                    'username'   => 'fake',
                    'notifyPage' => '/my/notify/page',
                    'returnPage' => '/merchant/pay/return',
                    'cancelPage' => '/merchant/pay/cancel',
                    'collection' => $this->gateways,
                ],
            ],
        ]);
        $this->module = Yii::$app->getModule('merchant');
    }

    protected function tearDown()
    {
    }

    public function testGetCollection()
    {
        $this->assertInstanceOf(Collection::class, $this->module->collection);
    }

    public function testGetMerchant()
    {
        $this->assertInstanceOf(FakeMerchant::class, $this->module->collection->fake);
        $this->assertInstanceOf(OmnipayMerchant::class, $this->module->collection->wmusd);
        $this->assertSame($this->gateways['wmusd']['purse'], $this->module->collection->wmusd->data['purse']);
        $this->assertSame($this->gateways['fake']['secret'], $this->module->collection->fake->data['secret']);
    }

    public function testGetWorker()
    {
        $this->assertInstanceOf(FakeGateway::class, $this->module->collection->fake->getWorker());
        $this->assertInstanceOf('Omnipay\Stripe\Gateway', $this->module->collection->Stripe->getWorker());
        $this->assertInstanceOf('Omnipay\WebMoney\Gateway', $this->module->collection->wmusd->getWorker());
    }
}
