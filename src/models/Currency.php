<?php

namespace hiqdev\yii2\merchant\models;

use NumberFormatter;
use Yii;
use yii\base\Model;

/**
 * Class Currency
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Currency extends Model
{
    /** @var string */
    public $code;

    /**
     * Returns the symbol used for a currency.
     *
     * @param string|null $locale   optional
     *
     * @return string|null the currency symbol or NULL if not found
     * @see https://stackoverflow.com/questions/13897516/get-currency-symbol-in-php
     */
    public function getSymbol($locale = null): string
    {
        if (null === $locale) {
            $locale = Yii::$app->formatter->locale;
        }
        $fmt = new NumberFormatter($locale . "@currency={$this->code}", NumberFormatter::CURRENCY);
        $symbol = $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

        return $symbol;
    }
}
