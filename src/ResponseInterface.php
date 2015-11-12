<?php

namespace hiqdev\yii2\merchant;

/**
 * ResponseInterface declares basic interface all responses have to follow.
 *
 * All responses have to provide:
 * - result info: is successful, is redirect
 * - redirection facility
 */
interface ResponseInterface
{
    public function isRedirect();

    public function isSuccessful();

    public function redirect();
}
