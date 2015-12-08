<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin(['action' => $widget->action]) ?>
    <?= Html::hiddenInput('merchant',   $request->merchant->id) ?>
    <?= Html::hiddenInput('type',       $request->getType()) ?>
    <?= Html::hiddenInput('data',       Json::encode($request->data)) ?>

    <button class="btn btn-default btn-block" type="submit" style="text-align:left">
        <i class="pi pi-sm pi-<?= $request->merchant->getSimpleName() ?>" style="float:right"></i>
        <br/>
        <?= Yii::t('merchant', 'pay') ?>  <b><?= $widget->formatMoney($request->getAmount()) ?></b>
        <?= Yii::t('merchant', 'with') ?> <b><?= $request->merchant->getLabel() ?></b>
        <br/>

        <?php if ($request->getFee() > 0) : ?>
            (<?= Yii::t('merchant', 'including commission') ?> <b><?= $widget->formatMoney($request->getFee()) ?></b>)
        <?php endif ?>
        <br/>
        <br/>
    </button>

<?php $form::end() ?>
