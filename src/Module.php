<?php

namespace hiqdev\yii2\merchant;

use Yii;
use hiqdev\php\merchant;

/**
 * For example,
 *
 * ```php
 * 'modules' => [
 *     'merchant' => [
 *         'class'     => 'hiqdev\merchant\Module',
 *         'merchants' => [
 *             'paypal' => [
 *                 'purse'  => $params['paypal_purse'],  /// DON'T keep this info in source control
 *                 'secret' => $params['paypal_secret'], /// DON'T keep this info in source control
 *             ],
 *             'webmoney' => [
 *                 'purse'  => $params['webmoney_purse'],  /// DON'T keep this info in source control
 *                 'secret' => $params['webmoney_secret'], /// DON'T keep this info in source control
 *             ],
 *         ],
 *     ],
 * ],
 * ```
 */
class Module extends \yii\base\Module
{
    protected $_merchants = [];

    /**
     * @param array $merchants list of merchants
     */
    public function setMerchants(array $merchants)
    {
        $this->_merchants = $merchants;
    }

    /**
     * @return Merchant[] list of merchants.
     */
    public function getMerchants()
    {
        $merchants = [];
        foreach ($this->_merchants as $id => $merchant) {
            $merchants[$id] = $this->getMerchant($id);
        }

        return $merchants;
    }

    /**
     * @param string $id service id.
     * @return Merchant merchant instance.
     * @throws InvalidParamException on non existing merchant request.
     */
    public function getMerchant($id)
    {
        if (!array_key_exists($id, $this->_merchants)) {
            throw new InvalidParamException("Unknown merchant '{$id}'.");
        }
        if (!is_object($this->_merchants[$id])) {
            $this->_merchants[$id] = $this->createMerchant($id, $this->_merchants[$id]);
        }

        return $this->_merchants[$id];
    }

    /**
     * Checks if merchant exists in the hub.
     * @param string $id merchant id.
     * @return boolean whether merchant exist.
     */
    public function hasMerchant($id)
    {
        return array_key_exists($id, $this->_merchants);
    }

    /**
     * Creates merchant instance from its array configuration.
     * @param string $id merchant id.
     * @param array $config merchant instance configuration.
     * @return Merchant merchant instance.
     */
    protected function createMerchant($id, $config)
    {
        $config['id'] = $id;

        return Merchant::create($config);
    }
}
