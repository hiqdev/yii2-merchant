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

use Yii;

class FakeMerchant extends \hiqdev\php\merchant\AbstractMerchant
{
    public $requestClass = 'hiqdev\yii2\merchant\tests\unit\FakeRequest';

    public $responseClass = 'hiqdev\yii2\merchant\tests\unit\FakeResponse';

    /**
     * Fake Gateway object.
     */
    protected $_worker;

    public function getWorker()
    {
        if ($this->_worker === null) {
            $this->_worker = Yii::createObject([
                'class'   => FakeGateway::class,
                'gateway' => $this->gateway,
                'data'    => $this->data,
            ]);
        }

        return $this->_worker;
    }
}
