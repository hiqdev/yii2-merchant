<?php

use hiqdev\yii2\merchant\widgets\PayButton;

$this->title                   = Yii::t('merchant', 'Select payment method');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Recharge account'), 'url' => ['deposit']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-4">
        <?php foreach (Yii::$app->getModule('merchant')->merchants as $merchant) { ?>
            <?= PayButton::widget(compact('merchant', 'model')) ?>
        <?php } ?>
    </div>
</div>
