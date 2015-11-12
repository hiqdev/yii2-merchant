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
 * MerchantInterface declares basic interface all merchants have to follow.
 *
 * All merchants have to provide:
 * - label: user friendly payment gateway description
 * - request creation
 */
interface MerchantInterface
{
    public function getLabel();

    public function request($type, $data);
}
