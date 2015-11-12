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
 * ResponseInterface declares basic interface all responses have to follow.
 *
 * All responses have to provide:
 * - result info: is successful, is redirect
 * - redirection facility
 */
interface ResponseInterface
{
    public function isRedirect();

    public function isSuccessful();

    public function redirect();
}
