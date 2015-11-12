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

/**
 * Omnipay Response class.
 */
class OmnipayResponse extends AbstractResponse
{
    protected $_worker;

    public function getWorker()
    {
        if ($this->_worker === null) {
            $this->_worker = $this->request->getWorker()->send();
        }

        return $this->_worker;
    }

    public function redirect()
    {
        return $this->getWorker()->redirect();
    }

    public function isRedirect()
    {
        return $this->getWorker()->isRedirect();
    }

    public function isSuccessful()
    {
        return $this->getWorker()->isSuccessful();
    }
}
