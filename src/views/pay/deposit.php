<?php

use hiqdev\yii2\merchant\models\DepositForm;
use hiqdev\yii2\merchant\models\PurchaseRequest;
use hiqdev\yii2\merchant\widgets\PayButton;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var PurchaseRequest[] $requests
 * @var DepositForm $depositForm
 */

$this->title = Yii::t('merchant', 'Select payment method');
$this->params['breadcrumbs'][] = ['label' => Yii::t('merchant', 'Recharge account'), 'url' => ['deposit']];
$this->params['breadcrumbs'][] = $this->title;
$cashewKey = sprintf('cashew_%s', strtolower($depositForm->currency));
?>

<?php if (empty($requests)) : ?>
    <?= Html::tag('div', Yii::t('merchant', 'There are no payments methods available'), [
        'class' => 'alert alert-danger text-center',
        'style' => 'width: 50rem; margin: 0 auto;',
    ]) ?>
<?php elseif ($this->context->module->cashewOnly && array_key_exists($cashewKey, $requests)) : ?>
    <?= Html::tag('iframe', null, [
        'src' => $requests[$cashewKey]->form->getRedirectUrl(),
        'style' => 'border: none; width: 100%; height: 100vh; overflow: hidden;',
    ]) ?>
<?php else : ?>
    <div class="row">
        <div class="col-md-8">
            <div class="box">
                <div class="box-body">
                    <ul class="products-list product-list-in-box">
                        <?php foreach ($requests as $request) : ?>
                            <li class="item">
                                <?= PayButton::widget([
                                    'request' => $request,
                                    'depositForm' => $depositForm,
                                ]) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
