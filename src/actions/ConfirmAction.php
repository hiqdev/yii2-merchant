<?php

namespace hiqdev\yii2\merchant\actions;

use Yii;

/**
 * Class ConfirmAction
 */
class ConfirmAction extends \yii\base\Action
{
    public function run()
    {
        /// log all given data
        Yii::info($_REQUEST);

        /// create necessary merchant

        /// validate confirmation

        /// run transaction registration callback

        /// return confirmText for payment system
        die('test');
    }
}
