<?php

use hiqdev\yii2\merchant\widgets\PayButton;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \hiqdev\yii2\merchant\models\PurchaseRequest[] $requests
 * @var \hiqdev\yii2\merchant\models\DepositForm $depositForm
 */

$this->title = Yii::t('merchant', 'Select payment method');
$this->params['breadcrumbs'][] = ['label' => Yii::t('merchant', 'Recharge account'), 'url' => ['deposit']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-8">
        <div class="box <?= empty($requests) ? 'box-danger' : '' ?>">
            <div class="box-body">

                <?php if (empty($requests)) : ?>
                    <?= Html::tag('div', Yii::t('merchant', 'There are no payments methods available'), ['class' => 'alert alert-danger']) ?>
                <?php else : ?>
                    <ul class="products-list product-list-in-box">
                        <?php foreach ($requests as $request) : ?>
                            <li class="item">
                                <?php
                                /** @var PayButton $payButton */
                                $payButton = Yii::createObject([
                                    'class' => PayButton::class,
                                    'request' => $request,
                                    'depositForm' => $depositForm
                                ]); ?>
                                <?= $payButton->run() ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>

            </div>
        </div>
    </div>
</div>
