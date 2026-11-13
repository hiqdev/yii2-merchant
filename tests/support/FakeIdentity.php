<?php

namespace hiqdev\yii2\merchant\tests\support;

use yii\base\BaseObject;
use yii\web\IdentityInterface;

class FakeIdentity extends BaseObject implements IdentityInterface
{
    public $id;

    public static function findIdentity($id)
    {
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }
}
