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

use Closure;
use hiqdev\php\merchant\Helper;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Merchant Module.
 *
 * Example application configuration:
 *
 * ```php
 * 'modules' => [
 *     'merchant' => [
 *         'class'         => 'hiqdev\yii2\merchant\Module',
 *         'notifyPage'    => '/my/notify/page',
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
     * Default merchant library to use is Omnipay.
     */
    public $merchantLibrary = 'Omnipay';

    /**
     * Default merchant collection to use. Other can be specified.
     */
    public $collectionClass = 'hiqdev\yii2\merchant\Collection';

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
     * @param array $params parameters for collection
     *
     * @return Merchant[] list of merchants.
     */
    public function getCollection(array $params = [])
    {
        if (!is_object($this->_collection)) {
            $this->_collection = Yii::createObject(array_merge([
                'class'  => $this->collectionClass,
                'module' => $this,
                'params' => $params,
            ], (array) $this->_collection));
        }

        return $this->_collection;
    }

    /**
     * @param string $id     merchant id.
     * @param array  $params parameters for collection
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
        return $this->getCollection()->has($id);
    }

    /**
     * Creates merchant instance from its array configuration.
     *
     * @param array $config merchant instance configuration.
     *
     * @return Merchant merchant instance.
     */
    public function createMerchant($id, array $config)
    {
        return Helper::create(array_merge([
            'library'   => $this->merchantLibrary,
            'gateway'   => $id,
            'id'        => $id,
        ], $config));
    }

    public function prepareRequestData($merchant, $data)
    {
        $data = array_merge([
            'merchant'      => $merchant,
            'description'   => Yii::$app->request->getServerName() . ' deposit: ' . $this->username,
            'transactionId' => uniqid(),
        ], $data);

        return array_merge([
            'notifyUrl'     => $this->buildUrl('notify', $data),
            'returnUrl'     => $this->buildUrl('return', $data),
            'cancelUrl'     => $this->buildUrl('cancel', $data),
        ], $data);
    }

    protected $_username;

    public function setUsername($value)
    {
        $this->_username = $value;
    }

    public function getUsername()
    {
        return isset($this->_username) ? $this->_username : Yii::$app->user->identity->username;
    }

    public $notifyPage = 'notify';
    public $returnPage = 'return';
    public $cancelPage = 'cancel';

    public function buildUrl($dest, $data)
    {
        $name = $dest . 'Page';
        $page = array_merge([
            'username'      => $this->username,
            'merchant'      => $data['merchant'],
            'transactionId' => $data['transactionId'],
        ], (array) ($this->hasProperty($name) ? $this->{$name} : $dest));

        return Url::to($page, true);
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

    protected $_payController;

    public function getPayController()
    {
        if ($this->_payController === null) {
            $this->_payController = $this->createControllerById('pay');
        }

        return $this->_payController;
    }

    public function renderNotify(array $params)
    {
        return $this->getPayController()->renderNotify($params);
    }

    public function renderDeposit(array $params)
    {
        return $this->getPayController()->renderDeposit($params);
    }

    public function updateHistory($transactionId, array $data)
    {
        $this->writeHistory($transactionId, array_merge($this->readHistory($transactionId), $data));
    }

    public function writeHistory($transactionId, array $data)
    {
        $path = $this->getHistoryPath($transactionId);
        FileHelper::createDirectory(dirname($path));
        file_put_contents($path, Json::encode($data));
    }

    public function readHistory($transactionId)
    {
        $path = $this->getHistoryPath($transactionId);
        return file_exists($path) ? Json::decode(file_get_contents($path)) : [];
    }

    public function getHistoryPath($transactionId)
    {
        return Yii::getAlias('@runtime/merchant/') . substr($transactionId, -2) . DIRECTORY_SEPARATOR . $transactionId . '.json';
    }
}
