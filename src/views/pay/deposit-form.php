<?php

use hiqdev\paymenticons\yii2\PaymentIconsAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var \hiqdev\yii2\merchant\models\PurchaseRequest[] $availableMerchants
 * @var \hiqdev\yii2\merchant\models\Currency[] $availableCurrencies
 * @var \hiqdev\yii2\merchant\models\DepositForm $model
 */

$this->title = Yii::t('merchant', 'Account recharging');
$this->params['breadcrumbs'][] = $this->title;
PaymentIconsAsset::register(Yii::$app->getView());

$merchantNames = [];
foreach ($availableMerchants as $merchant) {
    $merchantNames[$merchant->system] = $merchant->label;
}

$primaryCurrency = reset($availableCurrencies);
$primaryCurrencySymbol = $primaryCurrency->getSymbol();

?>

<div class="row deposit-form">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?php $form = ActiveForm::begin() ?>
	            <?= Html::activeHiddenInput($model, 'currency', [
                    'data-attribute' => 'currency',
                    'value' => $model->currency ?? $primaryCurrency->code,
	            ]) ?>
                <?= Html::tag('label', $model->getAttributeLabel('amount'), ['for' => 'deposit-amount']) ?>

	            <?php if (count($availableCurrencies) > 1) : ?>
                    <?= $form->field($model, 'amount', [
                        'template' => (function () use ($availableCurrencies, $primaryCurrencySymbol) {
                            foreach ($availableCurrencies as $currency) {
                                $currencyDropdownOptions[] =
                                    '<li>' .
                                        Html::a($currency->getSymbol(), 'javascript:void(0);', [
                                            'data-value' => $currency->code,
                                            'data-label' => $currency->getSymbol(),
                                        ]) .
                                    '</li>';
                            }

                            $currencyDropdownHtml = implode("\n", $currencyDropdownOptions ?? []);
                            return <<<HTML
                                <div class="input-group">
                                    <div class="input-group-btn currency-selector">
                                        <button type="button" data-toggle="dropdown" class="btn btn-default iwd-label dropdown-toggle">
                                            <span class="currency">{$primaryCurrencySymbol}</span>
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            {$currencyDropdownHtml}
                                        </ul>
                                    </div>
                                    {input}
                                </div>
                                {hint}{error}
HTML
                            ;
                        })()
                    ]) ?>
                    <div class="well well-sm">
                        <?= Yii::t('merchant', 'Choose the purse currency  you want to replenish and enter the payment amount.') ?>
                    </div>
	            <?php else: ?>
                    <?= $form->field($model, 'amount', [
                        'template' => "<div class='input-group'><span class=\"input-group-addon\">{$primaryCurrencySymbol}</span>{input}</div>{hint}{error}",
                    ]) ?>
                    <div class="well well-sm">
                        <?= Yii::t('merchant', 'Enter the amount of the replenishment in {currency}. For example: 8.79', [
                            'currency' => $primaryCurrency->code,
                        ]) ?>
                    </div>
                <?php endif ?>

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
                            'merchants' => implode(',&nbsp; ', $merchantNames)
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
                    <?php if (Yii::$app->params['merchant.paxum.disabled']) : ?>
                        <p><?= Yii::t('merchant', 'Due to technical issues Paxum payment system has been disabled for some period of time. In case of any questions please contact directly to our support team.') ?></p>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<JS
$('.currency-selector a').click(function(e) {
    let item = $(this);
    let form = $('.deposit-form');
    form.find('.iwd-label .currency').text(item.data('label'));
    form.find('input[data-attribute=currency]').val(item.data('value')).trigger('change');
});
JS
) ?>
