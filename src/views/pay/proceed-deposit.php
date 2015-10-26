<?php

use hipanel\modules\finance\grid\BillGridView;
use yii\helpers\Html;
use hiqdev\yii2\merchant\widgets\PayButton;

$this->title                   = Yii::t('app', 'Select payment method');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bills'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Recharge account'), 'url' => ['proceed-deposit']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">

<div class="col-md-4">
    HERE
    <?php foreach (Yii::$app->getModule('merchant')->merchants as $merchant) { ?>
        <?= PayButton::widget(compact('merchant', 'model')) ?>
    <?php } ?>
</div>

</div>
