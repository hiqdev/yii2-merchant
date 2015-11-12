<?php

/*
 * Yii2 extension for payment processing with Omnipay, Payum and more later
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\actions;

use Yii;

/**
 * Class ConfirmAction.
 */
class ConfirmAction extends \yii\base\Action
{
    public function run()
    {
        /// log all given data
        Yii::info($_REQUEST);

        /// create necessary merchant

        /// validate confirmation

        /// run transaction registration callback

        /// return confirmText for payment system
        die('test');
    }
}
