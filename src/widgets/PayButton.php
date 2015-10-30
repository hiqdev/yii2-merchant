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

use Yii;

class PayButton extends \yii\base\Widget
{
    public $model;

    public $merchant;

    public function run() {
        parent::run();
        if ($this->model) {
            $this->merchant->mset($this->model);
        }
        print $this->renderButton();
    }

    public function renderButton()
    {
        return $this->render('pay-button', [
            'widget'   => $this,
            'merchant' => $this->merchant,
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

    public function formatMoney($sum)
    {
        return Yii::$app->formatter->format($sum, ['currency', $this->merchant->currency]);
    }
}
