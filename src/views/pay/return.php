<?php

/**
 * @var View $this
 * @var string $transactionId
 */
use hipanel\helpers\Url;
use yii\web\View;

$this->title = Yii::t('merchant', 'Payment result');

?>
<h1 class="text-center">
    <i class="fa fa-refresh fa-spin"></i>
    <?= Yii::t('merchant', 'Waiting for confirmation from the payment system...') ?>
</h1>

<?php
$options = \yii\helpers\Json::encode([
    'url' => Url::to(['@pay/check-return']),
    'data' => [
        'transactionId' => $transactionId,
    ],
]);

$this->registerJs(<<<JS
    function checkPaymentStatus(options) {
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: options.url,
            data: options.data,
            success: function (result) {
                if (result.status !== null) {
                    window.location = result.url;
                } else {
                    setTimeout(function() {
                        checkPaymentStatus(options);
                    }, 1000);
                }
            },
            error: function (result) {
                // TODO: error redirect
                console.log('oops', result);
            }
        });
    }

    var options = $options;
    checkPaymentStatus(options);
JS
);

?>
