<?php

namespace hiqdev\yii2\merchant;

/**
 * RequestInterface declares basic interface all requests have to follow.
 *
 * All requests have to provide:
 * - payment data: amount, currency
 * - response creation with send()
 */
interface RequestInterface
{
    public function getAmount();

    public function getCurrency();

    public function send();
}
