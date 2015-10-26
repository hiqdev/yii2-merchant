<?php

/*
 * Payment merchants extension for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (https://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\widgets;

use yii\helpers\Html;

class PayButton extends \yii\base\Widget
{
    public $model;

    public $merchant;

    public function run() {
        parent::run();
        $this->merchant->mset($this->model);
        print $this->renderButton();
    }

    public function renderButton()
    {
        return Html::tag('button', $this->renderLabel() . $this->renderForm(), [
            'class' => 'btn-block',
            'type'  => 'submit',
            'form'  => $this->merchant->formId,
        ]);
    }

    public function renderLabel()
    {
        return $this->merchant->label;
    }

    public function renderForm()
    {
        return $this->merchant->renderForm();
    }
}
