<?php

/*
 * Yii2 extension for payment processing with Omnipay, Payum and more later
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant;

use Closure;
use hipanel\base\Err;
use hiqdev\php\merchant\AbstractMerchant;
use hiqdev\php\merchant\Helper;
use hiqdev\yii2\merchant\controllers\PayController;
use Yii;
use yii\base\InvalidConfigException;
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
 *
 * @var string returns username for usage in merchant
 */
class Module extends \yii\base\Module
{
    /**
     * The URL prefix that will be used as a key to save current URL in the session.
     *
     * @see rememberUrl()
     * @see previousUrl()
     * @see \yii\helpers\BaseUrl::remember()
     * @see \yii\helpers\BaseUrl::previous()
     */
    const URL_PREFIX = 'merchant_url_';

    /**
     * @var string merchant library name. Defaults to `Omnipay`
     */
    public $merchantLibrary = 'Omnipay';

    /**
     * @var string merchant collection class name. Defaults to [[hiqdev\yii2\merchant\Collection]]
     */
    public $collectionClass = 'hiqdev\yii2\merchant\Collection';

    /**
     * @var string Deposit model class name. Defaults to [[hiqdev\yii2\merchant\models\Deposit]]
     */
    public $depositClass = 'hiqdev\yii2\merchant\models\Deposit';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    /**
     * Registers message sources for Merchant module.
     *
     * @void
     */
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

    /**
     * @var array|Closure list of merchants
     */
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
     * @return AbstractMerchant[] list of merchants.
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
     * @param string $id merchant id.
     * @param array $params parameters for collection
     * @return AbstractMerchant merchant instance.
     */
    public function getMerchant($id, array $params = [])
    {
        return $this->getCollection($params)->get($id);
    }

    /**
     * Checks if merchant exists in the hub.
     *
     * @param string $id merchant id.
     * @return bool whether merchant exist.
     */
    public function hasMerchant($id)
    {
        return $this->getCollection()->has($id);
    }

    /**
     * Creates merchant instance from its array configuration.
     *
     * @param string $id     ID
     * @param array  $config merchant instance configuration.
     * @return AbstractMerchant merchant instance.
     */
    public function createMerchant($id, array $config)
    {
        return Helper::create(array_merge([
            'library'   => $this->merchantLibrary,
            'gateway'   => $id,
            'id'        => $id,
        ], $config));
    }

    /**
     * Method builds data for merchant request.
     *
     * @param string $merchant
     * @param array  $data     request data
     * @return array
     */
    public function prepareRequestData($merchant, array $data)
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
            'finishUrl'     => $this->buildUrl('finish', $data),
            'returnMethod'  => 'POST',
            'cancelMethod'  => 'POST',
        ], $data);
    }

    /**
     * @var string client login
     */
    protected $_username;

    /**
     * Sets [[_username]].
     *
     * @param $username
     */
    public function setUsername($username)
    {
        $this->_username = $username;
    }

    /**
     * Gets [[_username]] when defined, otherwise - `Yii::$app->user->identity->username`,
     * otherwise `Yii::$app->user->identity->getId()`.
     * @throws InvalidConfigException
     * @return string
     */
    public function getUsername()
    {
        if (isset($this->_username)) {
            return $this->_username;
        } elseif (($identity = Yii::$app->user->identity) !== null) {
            if ($identity->hasProperty('username')) {
                $this->_username = $identity->username;
            } else {
                $this->_username = $identity->getId();
            }
            return $this->_username;
        }
        throw new InvalidConfigException('Unable to determine username');
    }

    /**
     * @var string|array the URL that will be used for payment system notifications. Will be passed through [[Url::to()]]
     */
    public $notifyPage = 'notify';
    /**
     * @var string|array the URL that will be used to redirect client from the merchant after the success payment.
     * Will be passed through [[Url::to()]]
     */
    public $returnPage = 'return';
    /**
     * @var string|array the URL that will be used to redirect client from the merchant after the failed payment.
     * Will be passed through [[Url::to()]]
     */
    public $cancelPage = 'cancel';
    /**
     * @var string|array the URL that might be used to redirect used from the success or error page to the finish page.
     * Will be passed through [[Url::to()]]
     */
    public $finishPage = 'finish';

    /**
     * Builds URLs that will be passed in the request to the merchant.
     *
     * @param string $destination `notify`, `return`, `cancel`
     * @param array  $data data, that will be used to build URL. Only `merchant` and `transactionId` keys
     * will be used from the array
     * @return string URL
     */
    public function buildUrl($destination, array $data)
    {
        $page = array_merge([
            'username'      => $this->username,
            'merchant'      => $data['merchant'],
            'transactionId' => $data['transactionId'],
        ], (array) $this->getPage($destination, $data));

        if (is_array($page)) {
            $page[0] = $this->localizePage($page[0]);
        } else {
            $page = $this->localizePage($page);
        }

        return Url::to($page, true);
    }

    /**
     * Builds url to `this_module/pay/$page` if page is not /full/page.
     * @param mixed $page
     * @return mixed
     */
    public function localizePage($page)
    {
        return is_string($page) && $page[0] !== '/' ? ('/' . $this->id . '/pay/' . $page) : $page;
    }

    public function getPage($destination, array $data)
    {
        $name = $destination . 'Page';
        if (isset($data[$name])) {
            return $data[$name];
        }

        return $this->hasProperty($name) ? $this->{$name} : $destination;
    }

    /**
     * Saves the $url to session with [[URL_PREFIX]] key, trailed with $name.
     *
     * @param array|string $url
     * @param string $name the trailing part for the URL save key. Defaults to `back`
     * @void
     */
    public function rememberUrl($url, $name = 'back')
    {
        Url::remember($url, static::URL_PREFIX . $name);
    }

    /**
     * Extracts the URL from session storage, saved with [[URL_PREFIX]] key, trailed with $name.
     *
     * @param string $name the trailing part for the URL save key. Defaults to `back`
     * @return string
     */
    public function previousUrl($name = 'back')
    {
        return Url::previous(static::URL_PREFIX . $name);
    }

    /**
     * @var PayController The Payment controller
     */
    protected $_payController;

    /**
     * @throws InvalidConfigException
     *
     * @return PayController
     */
    public function getPayController()
    {
        if ($this->_payController === null) {
            $this->_payController = $this->createControllerById('pay');
        }

        return $this->_payController;
    }

    /**
     * Renders page, that contains list of payment systems, that might be choosen by user.
     * Should be implemented in `PayController`.
     *
     * @param array $params
     * @return \yii\web\Response
     */
    public function renderDeposit(array $params)
    {
        return $this->getPayController()->renderDeposit($params);
    }

    public function completeHistory(array $data)
    {
        $history = $this->readHistory($data);
        if (isset($history['_isCompleted']) && $history['_isCompleted']) {
            return $history;
        }
        $data['_isCompleted'] = Err::not($data) && $data['id'];

        return $this->updateHistory($data);
    }

    /**
     * Merges the existing history with the given $data.
     * @param array $data
     * @throws \yii\base\Exception
     * @return int data that were written to the file, or false on failure.
     */
    public function updateHistory(array $data)
    {
        return $this->writeHistory(array_merge($this->readHistory($data), $data));
    }

    /**
     * Writes given data to history.
     * @param array $data
     * @throws \yii\base\Exception
     * @return int data that were written to the file, or false on failure.
     */
    public function writeHistory(array $data)
    {
        $path = $this->getHistoryPath($data);
        FileHelper::createDirectory(dirname($path));
        $res = file_put_contents($path, Json::encode($data));

        return $res === false ? $res : $data;
    }

    /**
     * Reads history.
     *
     * @param string|array $data
     * @return array
     */
    public function readHistory($data)
    {
        $path = $this->getHistoryPath($data);
        return file_exists($path) ? Json::decode(file_get_contents($path)) : [];
    }

    /**
     * Returns path for the transaction log depending on $transactionId.
     *
     * @param string|array $data transactionId or array containing transactionId
     * @return bool|string Path to the transaction log
     */
    protected function getHistoryPath($data)
    {
        $transactionId = is_array($data) ? $data['transactionId'] : $data;
        return Yii::getAlias('@runtime/merchant/' . substr(md5($transactionId), 0, 2) . '/' . $transactionId . '.json');
    }
}
