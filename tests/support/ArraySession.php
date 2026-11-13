<?php

namespace hiqdev\yii2\merchant\tests\support;

use yii\web\Session;

/**
 * In-memory session, so tests never touch PHP's native session handling.
 */
class ArraySession extends Session
{
    private array $data = [];

    public function open()
    {
    }

    public function get($key, $defaultValue = null)
    {
        return $this->data[$key] ?? $defaultValue;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function addFlash($key, $value = true, $removeAfterAccess = true)
    {
        $this->data['__flash'][$key][] = $value;
    }

    public function getFlash($key, $defaultValue = null, $delete = false)
    {
        return $this->data['__flash'][$key] ?? $defaultValue;
    }
}
