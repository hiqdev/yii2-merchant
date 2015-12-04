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

use yii\helpers\Inflector;

/**
 * AbstractRequest class.
 */
class AbstractRequest extends \yii\base\Object implements RequestInterface
{
    public $merchant;

    public $type;

    public $data = [];

    public function getAmount()
    {
        return $this->data['amount'];
    }

    public function getFee()
    {
        return $this->data['fee'] ?: 0;
    }

    public function getSum()
    {
        return $this->data['sum'] ?: $this->getAmount() - $this->getFee();
    }

    public function getCurrency()
    {
        return $this->data['currency'];
    }

    public function send()
    {
        return $this->merchant->response($this);
    }

    /**
     * Concrete requests can build type in other way.
     */
    public function getType()
    {
        return Inflector::id2camel($this->type);
    }

    public function getData()
    {
        return array_merge([
            'notifyUrl' => $this->merchant->buildUrl('notify'),
            'returnUrl' => $this->merchant->buildUrl('return'),
            'cancelUrl' => $this->merchant->buildUrl('cancel'),
        ], (array) $this->data);
    }
}
