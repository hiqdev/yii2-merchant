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
        <div style="width=100%" class="payment-icon-sm <?= $request->merchant->simpleName ?>">
            <br/>
            <?= Yii::t('merchant', 'pay') ?>  <b><?= $widget->formatMoney($request->amount) ?></b>
            <?= Yii::t('merchant', 'with') ?> <b><?= $request->merchant->label ?></b>
            <br/>

            <?php if ($request->fee > 0) : ?>
                (<?= Yii::t('merchant', 'including commission') ?> <b><?= $widget->formatMoney($request->fee) ?></b>)
            <?php endif ?>
            <br/>
            <br/>
        </div>
    </button><br/>

<?php $form::end() ?>
