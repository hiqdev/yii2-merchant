<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin(['action' => $widget->action]) ?>
    <?= Html::hiddenInput('merchant',   $request->merchant->id) ?>
    <?= Html::hiddenInput('type',       $request->type) ?>
    <?= Html::hiddenInput('data',       Json::encode($request->data)) ?>

    <button class="btn btn-default btn-block" type="submit" style="text-align:left">
        <i class="pi pi-sm pi-<?= $request->merchant->simpleName ?>" style="float:right"></i>
        <br/>
        <?= Yii::t('merchant', 'pay') ?>  <b><?= $widget->formatMoney($request->amount) ?></b>
        <?= Yii::t('merchant', 'with') ?> <b><?= $request->merchant->label ?></b>
        <br/>

        <?php if ($request->fee > 0) : ?>
            (<?= Yii::t('merchant', 'including commission') ?> <b><?= $widget->formatMoney($request->fee) ?></b>)
        <?php endif ?>
        <br/>
        <br/>
    </button><br/>

<?php $form::end() ?>
