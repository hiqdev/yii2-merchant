<?php

/*
 * Payment merchants extension for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (https://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant;

use Yii;
use Closure;
use hiqdev\php\merchant\Merchant;
use yii\base\InvalidParamException;

/**
 * Merchant Module stores all the available merchants.
 *
 * Example application configuration:
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
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['merchant'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath'       => '@hiqdev/yii2/merchant/messages',
            'fileMap'        => [
                'merchant' => 'merchant.php',
            ],
        ];
    }

    protected $_merchants = [];

    /**
     * @param array|Closure $merchants list of merchants or callback
     */
    public function setMerchants($merchants)
    {
        $this->_merchants = $merchants;
    }

    /**
     * @return Merchant[] list of merchants.
     */
    public function getMerchants()
    {
        $this->fetchMerchants();
        foreach ($this->_merchants as $id => $merchant) {
            $this->_loadMerchant($id);
        }

        return $this->_merchants;
    }

    public function fetchMerchants($params = [])
    {
        if ($this->_merchants instanceof Closure) {
            $this->_merchants = call_user_func($this->_merchants, $params);
        }
    }

    /**
     * @param string $id service id.
     *
     * @throws InvalidParamException on non existing merchant request.
     *
     * @return Merchant merchant instance.
     */
    public function getMerchant($id)
    {
        $this->fetchMerchants();
        if (!$this->hasMerchant($id)) {
            throw new InvalidParamException("Unknown merchant '{$id}'.");
        }
        $this->_loadMerchant($id);

        return $this->_merchants[$id];
    }

    /**
     * @param string $id service id.
     */
    protected function _loadMerchant($id)
    {
        if (!is_object($this->_merchants[$id])) {
            $this->_merchants[$id] = $this->createMerchant($id, $this->_merchants[$id]);
        }
    }

    /**
     * Checks if merchant exists in the hub.
     *
     * @param string $id merchant id.
     *
     * @return bool whether merchant exist.
     */
    public function hasMerchant($id)
    {
        return array_key_exists($id, $this->_merchants);
    }

    /**
     * Creates merchant instance from its array configuration.
     *
     * @param string $id     merchant id.
     * @param array  $config merchant instance configuration.
     *
     * @return Merchant merchant instance.
     */
    protected function createMerchant($id, $config)
    {
        $config['id'] = $id;

        return Merchant::create($config);
    }
}
