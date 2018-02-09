<?php
/**
 * Yii2 extension for payment processing with Omnipay, Payum and more later.
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\tests\unit;

use yii\base\BaseObject;
use hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface;

class FakeTransactionRepository extends BaseObject implements TransactionRepositoryInterface
{
    public function findById($id)
    {
        return null;
    }

    public function save($transaction)
    {
        return null;
    }

    public function create($id, $merchant, $parameters)
    {
        return null;
    }

    public function insert($transaction)
    {
        return null;
    }
}
