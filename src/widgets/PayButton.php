<?php
/**
 * Yii2 extension for payment processing with Omnipay, Payum and more later.
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\widgets;

use hiqdev\paymenticons\yii2\PaymentIconsAsset;
use hiqdev\php\merchant\AbstractRequest;
use hiqdev\yii2\merchant\models\DepositForm;
use hiqdev\yii2\merchant\models\DepositRequest;
use hiqdev\yii2\merchant\models\PurchaseRequest;
use Yii;
use yii\base\Event;
use yii\helpers\Json;

/**
 * Class PayButton.
 */
class PayButton extends \yii\base\Widget
{
    const EVENT_RENDER_COMMENT = 'renderComment';

    /**
     * @var PurchaseRequest
     */
    public $request;

    /**
     * @var DepositForm
     */
    public $depositForm;

    /**
     * @var array|string the URL for action
     */
    public $action = ['/merchant/pay/request'];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        PaymentIconsAsset::register(Yii::$app->getView());
        $this->registerMerchantCss();
    }

    protected function registerMerchantCss()
    {
        $pathToCss = Yii::getAlias('@hiqdev/yii2/merchant/assets/css/selectPayment.css');
        if (is_file($pathToCss)) {
            Yii::$app->assetManager->publish($pathToCss);
            $file = Yii::$app->assetManager->getPublishedUrl($pathToCss);
            $this->view->registerCssFile($file);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->renderButton();
    }

    /**
     * Renders the payment button.
     *
     * @return string
     */
    public function renderButton()
    {
        return $this->render('pay-button', [
            'widget' => $this,
            'request' => $this->request,
            'depositRequest' => new DepositRequest([
                'id' => $this->request->id,
                'amount' => $this->depositForm->amount,
                'merchant' => $this->getMerchantName()
            ])
        ]);
    }

    /**
     * Extracts merchant name from the [[request]].
     * @return string
     */
    public function getMerchantName()
    {
        return $this->request->merchant_name;
    }

    public function getTotalCommission()
    {
        return $this->request->fee + $this->request->commission_fee;
    }

    public function getButtonData()
    {
        return Json::encode($this->request->getFormInputs());
    }

    /**
     * Renders the button comment. Normally triggers [[EVENT_RENDER_COMMENT]] event.
     */
    public function renderButtonComment()
    {
        $this->trigger(self::EVENT_RENDER_COMMENT);
    }

    public function formatMoney($sum)
    {
        return Yii::$app->formatter->format($sum, ['currency', $this->request->currency]);
    }

    public function isDisabled()
    {
        return $this->request->disableReason !== null;
    }
}
