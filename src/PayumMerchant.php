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

/**
 * PayumMerchant class.
 * To be done... Any help appreciated.
 */
class PayumMerchant extends AbstractMerchant
{
    public $requestClass = 'hiqdev\yii2\merchant\PayumRequest';

    public $responseClass = 'hiqdev\yii2\merchant\PayumResponse';
}
