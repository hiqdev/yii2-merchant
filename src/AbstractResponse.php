<?php

namespace hiqdev\yii2\merchant;

/**
 * Abstract Response class.
 */
abstract class AbstractResponse extends \yii\base\Object implements ResponseInterface
{
    public $merchant;

    public $request;

    abstract public function redirect();

    abstract public function isRedirect();

    abstract public function isSuccessful();
}
