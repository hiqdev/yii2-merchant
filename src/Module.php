<?php
/**
 * Yii2 extension for payment processing with Omnipay, Payum and more later.
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant;

use Closure;
use hiqdev\php\merchant\AbstractMerchant;
use hiqdev\php\merchant\Helper;
use hiqdev\yii2\merchant\Collection;
use hiqdev\yii2\merchant\models\Deposit;
use hiqdev\yii2\merchant\controllers\PayController;
use hiqdev\yii2\merchant\transactions\Transaction;
use hiqdev\yii2\merchant\transactions\TransactionException;
use hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
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
     * @var string merchant collection class name. Defaults to [[Collection]]
     */
    public $collectionClass = Collection::class;

    /**
     * @var string Deposit model class name. Defaults to [[Deposit]]
     */
    public $depositClass = Deposit::class;

    /**
     * @var array|Closure list of merchants
     */
    protected $_collection = [];
    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    public function __construct($id, $parent = null, TransactionRepositoryInterface $transactionRepository, array $config = [])
    {
        parent::__construct($id, $parent, $config);
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @param array $params parameters for collection
     * @return AbstractMerchant[] list of merchants
     */
    public function getCollection(array $params = [])
    {
        return Yii::createObject(array_merge([
            'class'  => $this->collectionClass,
            'module' => $this,
            'params' => $params,
        ], (array) $this->_collection));
    }

    /**
     * @param string $id merchant id
     * @param array $params parameters for collection
     * @return AbstractMerchant merchant instance
     */
    public function getMerchant($id, array $params = [])
    {
        return $this->getCollection($params)->get($id);
    }

    /**
     * Checks if merchant exists in the hub.
     *
     * @param string $id merchant id
     * @return bool whether merchant exist
     */
    public function hasMerchant($id)
    {
        return $this->getCollection()->has($id);
    }

    /**
     * Creates merchant instance from its array configuration.
     *
     * @param string $id     ID
     * @param array  $config merchant instance configuration
     * @return AbstractMerchant merchant instance
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

    /**
     * @param Transaction $transaction
     * @return Transaction
     */
    public function saveTransaction($transaction)
    {
        if ($transaction->isCompleted()) {
            return $transaction;
        }

        return $this->transactionRepository->save($transaction);
    }

    public function createTransaction($data)
    {
        $id = ArrayHelper::remove($data, 'transactionId', uniqid());
        $merchant = ArrayHelper::remove($data, 'merchant');
        if (empty($merchant)) {
            throw new InvalidConfigException('Merchant is required to create a transaction');
        }

        return $this->transactionRepository->create($id, $merchant, $data);
    }

    public function insertTransaction($data)
    {
        $transaction = $this->createTransaction($data);

        return $this->transactionRepository->insert($transaction);
    }

    /**
     * @param string $id transaction ID
     * @return Transaction|null
     */
    public function findTransaction($id)
    {
        try {
            return $this->transactionRepository->findById($id);
        } catch (TransactionException $e) {
            return null;
        }
    }
}
