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

use hiqdev\yii2\merchant\models\DepositForm;
use hiqdev\yii2\merchant\controllers\PayController;
use hiqdev\yii2\merchant\models\DepositRequest;
use hiqdev\yii2\merchant\models\PurchaseRequest;
use hiqdev\yii2\merchant\transactions\Transaction;
use hiqdev\yii2\merchant\transactions\TransactionException;
use hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface;
use Yii;
use yii\base\InvalidConfigException;
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
     * @var string|class-string<Collection> merchant collection class name. Defaults to [[Collection]]
     */
    public $purchaseRequestCollectionClass = Collection::class;
    /**
     * @var string currencies collection class name. Defaults to [[Collection]]
     */
    public $currenciesCollectionClass;
    /**
     * @var string Deposit model class name. Defaults to [[DepositForm]]
     */
    public $depositFromClass = DepositForm::class;
    /**
     * @var bool Whether to use payment processing only through Cashew
     */
    public bool $cashewOnly = false;
    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    public function __construct($id, $parent = null, TransactionRepositoryInterface $transactionRepository, array $config = [])
    {
        parent::__construct($id, $parent, $config);

        $this->transactionRepository = $transactionRepository;
    }

    public function setCollection(array $collection)
    {
        $this->_collection = $collection;
    }

    /**
     * @param DepositRequest $depositRequest
     * @return Collection
     * @throws InvalidConfigException
     */
    public function getPurchaseRequestCollection($depositRequest = null)
    {
        return Yii::createObject([
            'class'  => $this->purchaseRequestCollectionClass,
            'module' => $this,
            'depositRequest' => $depositRequest,
        ]);
    }

    /**
     * @return Currencies
     * @throws InvalidConfigException
     */
    public function getAvailableCurrenciesCollection(): Currencies
    {
        return Yii::createObject([
            'class'  => $this->currenciesCollectionClass,
            'module' => $this,
        ]);
    }

    /**
     * @param string $merchant_name merchant id
     * @param DepositRequest $depositRequest
     * @return PurchaseRequest merchant instance
     */
    public function getPurchaseRequest($merchant_name, DepositRequest $depositRequest)
    {
        return $this->getPurchaseRequestCollection($depositRequest)->get($merchant_name);
    }

    /**
     * Checks if merchant exists in the hub.
     *
     * @param string $id merchant id
     * @return bool whether merchant exist
     */
    public function hasPurchaseRequest($id)
    {
        return $this->getPurchaseRequestCollection()->has($id);
    }

    /**
     * Method builds data for merchant request.
     *
     * @param DepositRequest $depositRequest
     */
    public function prepareRequestData($depositRequest): void
    {
        $depositRequest->username = $this->getUsername();
        $depositRequest->notifyUrl = $this->buildUrl('notify', $depositRequest);
        $depositRequest->returnUrl = $this->buildUrl('return', $depositRequest);
        $depositRequest->cancelUrl = $this->buildUrl('cancel', $depositRequest);
        $depositRequest->finishUrl = $this->buildUrl('finish', $depositRequest);
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
     * @param DepositRequest $depositRequest
     * @return string URL
     */
    public function buildUrl($destination, DepositRequest $depositRequest)
    {
        $page = [
            $this->getPage($destination, $depositRequest),
            'username'      => $depositRequest->username,
            'merchant'      => $depositRequest->merchant,
            'transactionId' => $depositRequest->id,
        ];

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

    public function getPage($destination, DepositRequest $depositRequest)
    {
        $property = $destination . 'Url';
        if ($depositRequest->$property) {
            return $depositRequest->$property;
        }

        $name = $destination . 'Page';

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
     * @param DepositForm $form
     * @return \yii\web\Response
     */
    public function renderDeposit($form)
    {
        return $this->getPayController()->renderDeposit($form);
    }

    /**
     * @param Transaction $transaction
     * @return Transaction
     */
    public function saveTransaction($transaction)
    {
        return $this->transactionRepository->save($transaction);
    }

    public function insertTransaction($id, $merchant, $data)
    {
        $transaction = $this->transactionRepository->create($id, $merchant, $data);

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
