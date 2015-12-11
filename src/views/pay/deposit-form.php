<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title                   = Yii::t('merchant', 'Recharge account');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-4">
        <?php $form = ActiveForm::begin() ?>
            <?php foreach ($model->attributes() as $attr): ?>
                <?= $form->field($model, $attr) ?>
            <?php endforeach ?>
            <?= Html::submitButton(Yii::t('merchant', 'Proceed'), ['class' => 'btn btn-primary']) ?>
        <?php $form->end() ?>
    </div>

</div>
