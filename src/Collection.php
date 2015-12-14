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
 * Merchants Collection.
 */
class Collection extends \hiqdev\yii2\collection\Manager
{
    /**
     * @var array
     */
    public $params = [];

    /**
     * @var Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    protected function createItem($id, array $config = [])
    {
        return $this->module->createMerchant($id, $config);
    }
}
