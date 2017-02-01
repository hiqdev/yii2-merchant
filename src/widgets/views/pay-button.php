<?php

use hiqdev\php\merchant\AbstractRequest;
use hiqdev\yii2\merchant\widgets\PayButton;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View
 * @var PayButton $widget
 * @var AbstractRequest $request
 */
?>
<?php $form = ActiveForm::begin(['action' => $widget->action]) ?>
<?= Html::hiddenInput('merchant', $widget->getMerchantName()) ?>
<?= Html::hiddenInput('type', $request->getType()) ?>
<?= Html::hiddenInput('data', Json::encode($request->data)) ?>

<button class="btn btn-default btn-block" type="submit" style="text-align:left">
    <i class="pi pi-sm pi-<?= $request->merchant->getSimpleName() ?>" style="float:right"></i>
    <br/>
    <?= Yii::t('merchant', 'Pay {amount} with {merchantLabel}', [
        'amount' => Html::tag('b', $widget->formatMoney($request->getAmount())),
        'merchantLabel' => Html::tag('b', $request->merchant->getLabel()),
    ]); ?>
    <br/>

    <?php if ($request->getTotalFee() > 0) : ?>
        (<?= Yii::t('merchant', 'including commission {commission}', [
            'commission' => Html::tag('b', $widget->formatMoney($request->getTotalFee())),
        ]) ?>)
    <?php endif ?>
    <br/>
    <?php $widget->renderButtonComment() ?>
    <br/>
</button>

<?php $form::end() ?>
