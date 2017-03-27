<?php
/**
 * Yii2 extension for payment processing with Omnipay, Payum and more later.
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

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
