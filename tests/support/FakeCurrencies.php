<?php

namespace hiqdev\yii2\merchant\tests\support;

use hiqdev\yii2\merchant\Currencies;
use hiqdev\yii2\merchant\models\Currency;

class FakeCurrencies extends Currencies
{
    public $module;

    public function getList(): array
    {
        return [
            new Currency(['code' => 'USD']),
            new Currency(['code' => 'EUR']),
        ];
    }
}
