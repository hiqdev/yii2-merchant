<?php

namespace hiqdev\yii2\merchant;

class OmnipayRequest extends AbstractRequest
{
    /**
     * Omnipay Request object.
     */
    protected $_worker;

    public function getWorker()
    {
        if ($this->_worker === null) {
            $this->_worker = $this->merchant->getWorker()->{$this->getType()}($this->getData());
        }

        return $this->_worker;
    }
}
