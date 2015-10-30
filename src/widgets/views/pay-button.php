<?php

use yii\helpers\Html;

use hiqdev\yii2\merchant\MerchantAsset;

$asset = Yii::createObject([
    'class'      => 'hiqdev\yii2\merchant\MerchantAsset',
    'sourcePath' => '@vendor/hiqdev/php-merchant-' . $merchant->system . '/src/assets',
]);

$asset->publish($this->getAssetManager());
$logo = $asset->getPublishedUrl('logo.png');

?>

<?= $widget->renderForm() ?>
<button class="btn btn-default btn-block" type="submit" form="<?= $merchant->formId ?>" style="text-align:left;background: url(<?= $logo ?>) no-repeat right">

    <?= Yii::t('merchant', 'pay') ?>  <b><?= $widget->formatMoney($merchant->total) ?></b>
    <? /*
        <?= Yii::t('merchant', 'with') ?> <b><?= $merchant->label ?></b>
    */ ?>
    <br/>

    <?php if ($merchant->fee > 0) { ?>
        (<?= Yii::t('merchant', 'including commission') ?> <b><?= $widget->formatMoney($merchant->fee) ?></b>)
    <?php } ?>
    <br/>
</button><br/>
