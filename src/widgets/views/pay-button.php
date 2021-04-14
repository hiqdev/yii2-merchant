<?php

use hiqdev\yii2\merchant\widgets\PayButton;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var PayButton $widget
 * @var \hiqdev\yii2\merchant\models\PurchaseRequest $request
 * @var \hiqdev\yii2\merchant\models\DepositRequest $depositRequest
 */
?>
<?php $form = ActiveForm::begin(['action' => $widget->action]) ?>
<?= Html::activeHiddenInput($depositRequest, 'id') ?>
<?= Html::activeHiddenInput($depositRequest, 'amount') ?>
<?= Html::activeHiddenInput($depositRequest, 'merchant') ?>
<?= Html::activeHiddenInput($depositRequest, 'currency') ?>
<?php if ($depositRequest->finishUrl) : ?>
    <?= Html::activeHiddenInput($depositRequest, 'finishUrl') ?>
<?php endif ?>

<button type="submit" class="btn-block" <?= $widget->isDisabled() ? 'disabled' : ''?>>
    <div class="product-img">
        <i class="pi pi-sm pi-<?= strtolower($request->system) ?>" style="float:right"></i>
    </div>
    <div class="product-info">
        <div class="product-title">
            <?= Yii::t('merchant', 'Pay {amount} with {merchantLabel}', [
                'amount' => Html::tag('b', $widget->formatMoney($request->amount)),
                'merchantLabel' => Html::tag('b', $request->label),
            ]); ?>
            <?php if ($request->getPaymentMethod()) : ?>
                <?= Yii::t('merchant', 'via {method}', [
                    'method' => Html::tag('b', $request->getPaymentMethod()),
                ]) ?>
            <?php endif ?>
            <span class="pull-right" style="font-size: 24px"><?= $widget->formatMoney($request->amount) ?></span>
        </div>
        <span class="product-description">
            <?php if ($widget->getTotalCommission() > 0 || $widget->includesVat()) {
                $comments = [];
                if ($widget->getTotalCommission() > 0) {
                    $comments[] = Yii::t('merchant', 'including commission {commission}', [
                        'commission' => Html::tag('b', $widget->formatMoney($widget->getTotalCommission())),
                    ]);
                }
                if ($widget->includesVat()) {
                    $comments[] = Yii::t('merchant', 'including VAT {rate}% &mdash; {sum}', [
                        'rate' => $widget->getVatRate()*100,
                        'sum' => Html::tag('b', $widget->formatMoney($widget->getVatSum())),
                    ]);
                }

                echo '(' . implode(', ', $comments) . ')';
            } ?>
            <?php $widget->renderButtonComment() ?>
        </span>
    </div>
</button>

<?php $form::end() ?>
