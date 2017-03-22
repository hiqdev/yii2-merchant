<?php

use hiqdev\yii2\merchant\widgets\PayButton;
use yii\helpers\Html;

$this->title = Yii::t('merchant', 'Select payment method');
$this->params['breadcrumbs'][] = ['label' => Yii::t('merchant', 'Recharge account'), 'url' => ['deposit']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCss("
.products-list .product-img {
    margin-right: 15px;
}

.products-list button {
    border: none;
    text-align: left;
    background: none;
    outline: none;
    padding: 1rem;
}

.products-list button:hover {
    background: #f7f7f7;
}

.products-list .product-description {
    overflow: visible;
}
");
?>
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <div class="box <?= empty($requests) ? 'box-danger' : '' ?>">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('merchant', 'Select payment methods') ?></h3>
            </div>
            <div class="box-body">

                <?php if (empty($requests)) : ?>
                    <?= Html::tag('div', Yii::t('merchant', 'There are no payments methods available'), ['class' => 'alert alert-danger']) ?>
                <?php else : ?>
                    <ul class="products-list product-list-in-box">
                        <?php foreach ($requests as $request) : ?>
                            <li class="item">
                                <?= PayButton::widget(compact('request')) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>

            </div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-6">
    </div>
</div>
