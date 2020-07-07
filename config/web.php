<?php
/**
 * Yii2 extension for payment processing with Omnipay, Payum and more later.
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

return [
    'components' => [
        'themeManager' => [
            'class' => \hiqdev\thememanager\ThemeManager::class,
            'pathMap' => [
                dirname(__DIR__) . '/src/views' => '$themedViewPaths',
            ],
        ],
        'i18n' => [
            'translations' => [
                'merchant' => [
                    'class'          => \yii\i18n\PhpMessageSource::class,
                    'sourceLanguage' => 'en-US',
                    'basePath'       => dirname(__DIR__) . '/src/messages',
                ],
            ],
        ],
    ],
];
