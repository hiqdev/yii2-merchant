<?php

use hipanel\widgets\ArraySpoiler;
use hiqdev\paymenticons\yii2\PaymentIconsAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('merchant', 'Recharge account');
$this->params['breadcrumbs'][] = $this->title;
PaymentIconsAsset::register(Yii::$app->getView());

?>

<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <div class="box">
            <div class="box-body">
                <?php $form = ActiveForm::begin() ?>
                <?php foreach ($model->attributes() as $attr): ?>
                    <?= $form->field($model, $attr) ?>
                <?php endforeach ?>
                <?= Html::submitButton(Yii::t('merchant', 'Proceed'), ['class' => 'btn btn-primary']) ?>
                <?php $form->end() ?>
                <div class="text-muted">
                    <hr>
                    <?= Yii::t('merchant', 'This payment systems could be automaticaly processed: {merchants}', [
                        'merchants' => ArraySpoiler::widget([
                            'data' => $availableMerchants,
                            'visibleCount' => count($availableMerchants),
                            'formatter' => function ($merchant) {
                                return $merchant->getLabel();
                            },
                            'delimiter' => ',&nbsp; ',
                        ]),
                    ]) ?>
                    <br>
                    <br>
                    <?php foreach ($availableMerchants as $merchant) : ?>
                        <i class="pi pi-xs pi-<?= $merchant->getSimpleName() ?>"></i>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-6">
        <div class="callout callout-warning">
            <h4><?= Yii::t('merchant', 'Important information') ?></h4>
            <p><?= Yii::t('merchant', 'Once you have paid the items, do not forget to return back to site.') ?></p>
        </div>
    </div>
</div>
