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
 * Abstract Response class.
 */
abstract class AbstractResponse extends \yii\base\Object implements ResponseInterface
{
    public $merchant;

    public $request;

    abstract public function redirect();

    abstract public function isRedirect();

    abstract public function isSuccessful();
}
