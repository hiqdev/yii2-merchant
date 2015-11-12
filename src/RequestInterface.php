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
 * RequestInterface declares basic interface all requests have to follow.
 *
 * All requests have to provide:
 * - payment data: amount, currency
 * - response creation with send()
 */
interface RequestInterface
{
    public function getAmount();

    public function getCurrency();

    public function send();
}
