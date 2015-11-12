<?php

/*
 * Payment merchants extension for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\yii2\merchant\widgets;

use Yii;

class PayButton extends \yii\base\Widget
{
    public $request;

    public function run()
    {
        parent::run();
        print $this->renderButton();
    }

    public function renderButton()
    {
        return $this->render('pay-button', [
            'widget'  => $this,
            'request' => $this->request,
        ]);
    }

    public $action = ['request'];

    public function formatMoney($sum)
    {
        return Yii::$app->formatter->format($sum, ['currency', $this->request->currency]);
    }
}
