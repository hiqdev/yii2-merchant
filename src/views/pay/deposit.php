<?php

use hiqdev\yii2\merchant\widgets\PayButton;

$this->title                   = Yii::t('merchant', 'Select payment method');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Recharge account'), 'url' => ['deposit']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-4">
        <?php foreach ($requests as $request): ?>
            <?= PayButton::widget(compact('request')) ?>
        <?php endforeach ?>
    </div>
</div>
