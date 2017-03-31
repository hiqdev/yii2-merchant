<?php

use hiqdev\paymenticons\yii2\PaymentIconsAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var \hiqdev\php\merchant\AbstractMerchant[] $availableMerchants
 */

$this->title = Yii::t('merchant', 'Account recharging');
$this->params['breadcrumbs'][] = $this->title;
PaymentIconsAsset::register(Yii::$app->getView());

$merchantNames = [];
foreach ($availableMerchants as $merchant) {
    if (isset($merchantNames[$merchant->getGateway()])) {
        continue;
    }
    $merchantNames[$merchant->getGateway()] = $merchant->getLabel();
}

?>

<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?php $form = ActiveForm::begin() ?>
                <?= Html::tag('label', $model->getAttributeLabel('sum'), ['for' => 'deposit-sum']) ?>
                <?= $form->field($model, 'sum', [
                    'template' => "<div class='input-group'><span class=\"input-group-addon\">$</span>{input}</div>{hint}{error}",
                ]) ?>
                <div class="well well-sm">
                    <?= Yii::t('merchant', 'Enter the amount of the replenishment in dollars. For example: 8.79') ?>
                </div>
                <?= Html::submitButton(Yii::t('merchant', 'Proceed'), ['class' => 'btn btn-primary']) ?>
                <?php $form->end() ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <?= Html::tag('h3', Yii::t('merchant', 'Available payment methods'), ['class' => 'box-title']) ?>
                    </div>
                    <div class="box-body text-muted">
                        <?= Yii::t('merchant', 'We support fully automatic account depositing with the following payment systems: {merchants}', [
                            'merchants' => implode(',&nbsp; ', array_values($merchantNames))
                        ]) ?>
                        <br>
                        <br>
                        <?php foreach ($merchantNames as $name => $label) : ?>
                            <i class="pi pi-xs pi-<?= $name ?>"></i>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="callout callout-warning">
                    <h4><?= Yii::t('merchant', 'Important information') ?></h4>
                    <p><?= Yii::t('merchant', 'Remember to return to the site after successful payment!') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
