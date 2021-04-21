<?php
/**
 * Yii2 extension for payment processing with Omnipay, Payum and more later.
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\models;

use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Class PurchaseRequest
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PurchaseRequest
{
    public $id;

    public $merchant_name;
    public $system;
    public $label;

    public $amount;
    public $fee;
    public $commission_fee;

    public $vat_rate;
    public $vat_sum;

    public $currency;
    public $disableReason;

    public $paymentMethod;

    /** @var RedirectPurchaseResponse */
    public $form;

    public function setForm(RedirectPurchaseResponse $response)
    {
        $this->form = $response;
    }

    public function getFormInputs()
    {
        if (!isset($this->form)) {
            return [];
        }

        return $this->form->getRedirectData();
    }

    public function getFormAction()
    {
        if (!isset($this->form) || empty($this->form->getRedirectUrl())) {
            throw new InvalidConfigException('Form action for purchase request is missing');
        }

        return $this->form->getRedirectUrl();
    }

    public function getFormMethod()
    {
        if (!isset($this->form)) {
            return 'POST';
        }

        return $this->form->getMethod();
    }

    public function getPaymentMethodLabel(): ?string
    {
        if ($this->system !== 'yandexmoney') {
            return null;
        }

        if ($this->paymentMethod === null) {
            return null;
        }

        if ($this->paymentMethod === 'AC') {
            return Yii::t('merchant', 'via bank card');
        }

        if ($this->paymentMethod === 'PC') {
            return Yii::t('merchant', 'via account balance');
        }

        return Yii::t('merchant', 'via phone');
    }
}
