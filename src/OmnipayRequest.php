<?php

/*
 * Yii2 extension for payment processing with Omnipay, Payum and more later
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

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
