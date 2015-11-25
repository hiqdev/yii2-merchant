<?php

/*
 * Yii2 extension for payment processing with Omnipay, Payum and more later
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\tests\unit;

use hiqdev\yii2\merchant\Collection;
use hiqdev\yii2\merchant\Module;
use hiqdev\yii2\merchant\OmnipayMerchant;
use Yii;

/**
 * Module test suite.
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Module
     */
    protected $object;

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
        $this->object = Yii::createObject([
            'class'         => Module::className(),
            'notifyPage'    => '/my/notify/page',
            'collection'    => $this->gateways,
        ], ['fake']);
    }

    protected function tearDown()
    {
    }

    public function testInit()
    {
        $collection = $this->object->collection;
        $this->assertInstanceOf(Collection::className(), $collection);
        $this->assertInstanceOf(FakeMerchant::className(), $collection->fake);
        $this->assertInstanceOf(OmnipayMerchant::className(), $collection->wmusd);
        $this->assertSame($this->gateways['wmusd']['purse'], $collection->wmusd->data['purse']);
        $this->assertSame($this->gateways['fake']['secret'], $collection->fake->data['secret']);
        $this->assertInstanceOf(FakeGateway::className(), $collection->fake->worker);
        $this->assertInstanceOf('Omnipay\Stripe\Gateway', $collection->Stripe->worker);
        $this->assertInstanceOf('Omnipay\WebMoney\Gateway', $collection->wmusd->worker);
    }
}
