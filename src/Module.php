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
 *         'class'         => 'hiqdev\merchant\Module',
 *         'confirmPage'   => '/my/confirm/page',
 *         'collection'    => [
 *             'PayPal' => [
 *                 'purse'     => $params['paypal_purse'],
 *                 'secret'    => $params['paypal_secret'],   /// NEVER keep secret in source control
 *             ],
 *             'webmoney_usd' => [
 *                 'gateway'   => 'WebMoney',
 *                 'purse'     => $params['webmoney_purse'],
 *                 'secret'    => $params['webmoney_secret'], /// NEVER keep secret in source control
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

    /**
     * Default merchant to use is OmnipayMerchant.
     */
    public $merchantClass = 'hiqdev\yii2\merchant\OmnipayMerchant';

    /**
     * Deposit model class.
     */
    public $depositClass = 'hiqdev\yii2\merchant\models\Deposit';

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

    protected $_collection = [];

    /**
     * @param array|Closure $collection list of merchants or callback
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
    }

    /**
     * @return Merchant[] list of merchants.
     * @param array $params parameters for collection
     */
    public function getCollection(array $params = [])
    {
        if (!is_object($this->_collection)) {
            $this->_collection = Yii::createObject(array_merge([
                'class'  => $this->collectionClass,
                'module' => $this,
                'params' => $params,
            ], (array)$this->_collection));
        }

        return $this->_collection;
    }

    /**
     * @param string $id service id.
     * @param array $params parameters for collection
     *
     * @return Merchant merchant instance.
     */
    public function getMerchant($id, array $params = [])
    {
        return $this->getCollection($params)->get($id);
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
        $this->getCollection()->has($id);
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
    public function createMerchant($id, $config)
    {
        return Yii::createObject(array_merge([
            'class'     => $this->merchantClass,
            'module'    => $this,
            'id'        => $id,
        ], (array)$this->defaults, $config));
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

    public $notifyPage = ['notify'];
    public $returnPage = ['return'];
    public $cancelPage = ['cancel'];

    public function buildUrl($dest)
    {
        $name = $dest . 'Page';
        $page = array_merge([
            'merchant' => $this->id,
            'username' => Yii::$app->user->identity->username,
        ], (array)($this->hasProperty($name) ? $this->{$name} : [$dest]));
        return Url::to($page, true);
    }
}
