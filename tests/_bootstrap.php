<?php

/*
 * Yii2 extension for payment processing with Omnipay, Payum and more later
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

error_reporting(E_ALL & ~E_NOTICE);

require_once __DIR__ . '/../vendor/autoload.php';
if (!class_exists('Yii')) {
    require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
}
Yii::setAlias('@hiqdev/yii2/merchant', dirname(__DIR__));
Yii::setAlias('@hiqdev/yii2/merchant/tests', __DIR__);
