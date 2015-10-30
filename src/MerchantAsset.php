<?php

namespace hiqdev\yii2\merchant;

use Yii;

class MerchantAsset extends \yii\web\AssetBundle
{
    public function getPublishedUrl($asset)
    {
        $path = Yii::$app->getAssetManager()->getPublishedUrl($this->sourcePath);
        return $path . '/' . $asset;
    }
}
