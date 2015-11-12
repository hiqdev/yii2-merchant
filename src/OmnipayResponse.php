<?php

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
