<?php

namespace hiqdev\yii2\merchant;

/**
 * MerchantInterface declares basic interface all merchants have to follow.
 *
 * All merchants have to provide:
 * - label: user friendly payment gateway description
 * - request creation
 */
interface MerchantInterface
{
    public function getLabel();

    public function request($type, $data);
}
