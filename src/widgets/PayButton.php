<?php

/*
 * Yii2 extension for payment processing with Omnipay, Payum and more later
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\widgets;

use hiqdev\paymenticons\yii2\PaymentIconsAsset;
use hiqdev\php\merchant\AbstractRequest;
use Yii;
use yii\base\Event;

/**
 * Class PayButton.
 */
class PayButton extends \yii\base\Widget
{
    const EVENT_RENDER_COMMENT = 'renderComment';

    /**
     * @var AbstractRequest
     */
    public $request;

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
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        parent::run();
        echo $this->renderButton();
    }

    /**
     * Renders the payment button.
     *
     * @return string
     */
    public function renderButton()
    {
        return $this->render('pay-button', [
            'widget'  => $this,
            'request' => $this->request,
        ]);
    }

    /**
     * Extracts merchant name from the [[request]]
     * @return string
     */
    public function getMerchantName()
    {
        return $this->request->merchant->id;
    }

    /**
     * Renders the button comment. Normally triggers [[EVENT_RENDER_COMMENT]] event
     */
    public function renderButtonComment()
    {
        $this->trigger(self::EVENT_RENDER_COMMENT);
    }

    public function formatMoney($sum)
    {
        return Yii::$app->formatter->format($sum, ['currency', $this->request->getCurrency()]);
    }
}
