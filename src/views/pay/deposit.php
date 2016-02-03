<?php

use hiqdev\yii2\merchant\widgets\PayButton;
use yii\helpers\Html;

$this->title                   = Yii::t('merchant', 'Select payment method');
$this->params['breadcrumbs'][] = ['label' => Yii::t('merchant', 'Recharge account'), 'url' => ['deposit']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-4">
        <?php if (empty($requests)) {
            echo Html::tag('div', Yii::t('merchant', 'There are no payments methods available'), ['class' => 'alert alert-danger']);
        } else {
            foreach ($requests as $request) {
                echo PayButton::widget(compact('request'));
                echo "<br/>";
            }
        } ?>
    </div>
</div>
