<?php

/*
 * Payment merchants extension for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant;

use Yii;
use Closure;
use yii\base\InvalidParamException;
use yii\helpers\Url;

/**
 * Merchant Module.
 *
 * Example application configuration:
 *
 * ```php
 * 'modules' => [
 *     'merchant' => [
 *         'class'     => 'hiqdev\merchant\Module',
 *         'defaults'  => [
 *             'confirmPage' => '/my/confirm/page',
 *         ],
 *         'merchants' => [
 *             'PayPal' => [
 *                 'purse'  => $params['paypal_purse'],    /// DON'T keep this info in source control
 *                 'secret' => $params['paypal_secret'],   /// DON'T keep this info in source control
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
    /**
     * Default merchant collection to use. Other can be specified.
     */
    public $collectionClass = 'hiqdev\yii2\merchant\Collection';
    public $merchantClass = 'hiqdev\yii2\merchant\OmnipayMerchant';

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

    const URL_PREFIX = 'merchant_url_';

    public function rememberUrl($url, $name = 'back')
    {
        Url::remember($url, URL_PREFIX . $name);
    }

    public function previousUrl($name = 'back')
    {
        return Url::previous(URL_PREFIX . $name);
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
        if (!is_object($this->_merchants)) {
            $this->fetchMerchants();
            Yii::createObject([
                'class' => $this->collectionClass,
                'items' => $this->_merchants,
            ]);
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
     * @return Merchant merchant instance.
     */
    public function getMerchant($id)
    {
        return $this->getMerchants->get($id);
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
        $this->getMerchants()->has($id);
    }


    /**
     * Defaults for all merchants.
     */
    public $defaults = [];

    /**
     * Creates merchant instance from its array configuration.
     *
     * @param array  $config merchant instance configuration.
     *
     * @return Merchant merchant instance.
     */
    protected function createMerchant($config)
    {
        return Yii::createObject(array_merge(['class' => $this->merchantClass], (array)$this->defaults, $config));
    }
}
