<?php

namespace hiqdev\yii2\merchant;

use Yii;

abstract class AbstractMerchant extends \yii\base\Object implements MerchantInterface
{
    public $module;

    /**
     * Unique merchant identificator. E.g. paypal, webmoney_usd, webmoney_rub
     */
    public $id;

    /**
     * Gateway name, corresponding to Omnipay namespace. E.g. PayPal, WebMoney, YandexMoney
     */
    public $gateway;

    public $data = [];

    public function getLabel()
    {
        return $this->gateway;
    }

    public function getAssetDir()
    {
        return null;
    }

    public function request($type, $data)
    {
        return Yii::createObject([
            'class'     => $this->requestClass,
            'merchant'  => $this,
            'type'      => $type,
            'data'      => array_merge((array)$this->data, (array)$data),
        ]);
    }

    public function response(RequestInterface $request)
    {
        return Yii::createObject([
            'class'     => $this->responseClass,
            'merchant'  => $this,
            'request'   => $request,
        ]);
    }
}
