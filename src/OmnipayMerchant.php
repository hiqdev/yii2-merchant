<?php

namespace hiqdev\yii2\merchant;

use Omnipay\Omnipay;

/**
 * OmnipayMerchant class.
 */
class OmnipayMerchant extends AbstractMerchant
{
    public $requestClass = 'hiqdev\yii2\merchant\OmnipayRequest';

    public $responseClass = 'hiqdev\yii2\merchant\OmnipayResponse';

    /**
     * Omnipay Gateway object.
     */
    protected $_worker;

    public function getWorker()
    {
        if ($this->_worker === null) {
            $this->_worker = Omnipay::create($this->gateway)->initialize($this->data);
        }

        return $this->_worker;
    }

    public function getAssetDir()
    {
        return method_exists($this->getWorker(), 'getAssetDir') ? $this->getWorker()->getAssetDir() : null;
    }
}
