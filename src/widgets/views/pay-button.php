<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

$asset = Yii::createObject([
    'class'      => 'hiqdev\yii2\merchant\MerchantAsset',
    'sourcePath' => $request->merchant->getAssetDir(),
]);

$asset->publish($this->getAssetManager());
$logo = $asset->getPublishedUrl('logo.png');

?>
<?php $form = ActiveForm::begin(['action' => $widget->action]) ?>
    <?= Html::hiddenInput('merchant',   $request->merchant->id) ?>
    <?= Html::hiddenInput('type',       $request->type) ?>
    <?= Html::hiddenInput('data',       Json::encode($request->data)) ?>

    <button class="btn btn-default btn-block" type="submit" style="text-align:left;background: url(<?= $logo ?>) no-repeat right">
        <?= Yii::t('merchant', 'pay') ?>  <b><?= $widget->formatMoney($request->amount) ?></b>
        <?= Yii::t('merchant', 'with') ?> <b><?= $request->merchant->label ?></b>
        <br/>

        <?php if ($request->fee > 0) { ?>
            (<?= Yii::t('merchant', 'including commission') ?> <b><?= $widget->formatMoney($request->fee) ?></b>)
        <?php } ?>
        <br/>
    </button><br/>

<?php $form::end() ?>
