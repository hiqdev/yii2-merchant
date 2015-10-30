<?php

use hipanel\modules\finance\grid\BillGridView;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title                   = Yii::t('app', 'Recharge account');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">

    <div class="col-md-4">
        <?php $form = ActiveForm::begin() ?>
            <?= $form->field($model, 'sum') ?>
            <?= Html::submitButton('Proceed', ['class' => 'btn btn-primary']) ?>
        <?php $form::end() ?>
    </div>

</div>
