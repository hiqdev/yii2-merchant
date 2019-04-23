<?php

namespace hiqdev\yii2\merchant;

use hiqdev\yii2\merchant\models\Currency;

/**
 * Class Currencies
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
abstract class Currencies
{
    /**
     * @return Currency[]
     */
    abstract public function getList(): array;
}
