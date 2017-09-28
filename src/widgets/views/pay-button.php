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

<button type="submit" class="btn-block"<?= $request->merchant->isActive() ? "" : " disabled" ?>>
    <div class="product-img">
        <i class="pi pi-sm pi-<?= strtolower($request->merchant->getGateway()) ?>" style="float:right"></i>
    </div>
    <div class="product-info">
        <div class="product-title">
            <?= Yii::t('merchant', 'Pay {amount} with {merchantLabel}', [
                'amount' => Html::tag('b', $widget->formatMoney($request->getAmount())),
                'merchantLabel' => Html::tag('b', $request->merchant->getLabel()),
            ]); ?>
            <span class="pull-right" style="font-size: 24px"><?= $widget->formatMoney($request->getAmount()) ?></span>
        </div>
        <span class="product-description">
            <?php if ($request->getTotalFee() > 0) : ?>
                (<?= Yii::t('merchant', 'including commission {commission}', [
                    'commission' => Html::tag('b', $widget->formatMoney($request->getTotalFee())),
                ]) ?>)
            <?php endif ?>
            <?php $widget->renderButtonComment() ?>
        </span>
    </div>
</button>

<?php $form::end() ?>
