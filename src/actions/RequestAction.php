<?php

namespace hiqdev\yii2\merchant\actions;

use hiqdev\yii2\merchant\events\TransactionInsertEvent;
use hiqdev\yii2\merchant\models\DepositRequest;
use hiqdev\yii2\merchant\models\PurchaseRequest;
use hiqdev\yii2\merchant\Module;
use Yii;
use yii\base\Action;
use yii\base\Event;
use yii\web\BadRequestHttpException;

class RequestAction extends Action
{
    const EVENT_BEFORE_TRANSACTION_INSERT = 'before_transaction_insert';
    const EVENT_AFTER_TRANSACTION_INSERT = 'after_transaction_insert';

    /**
     * @return Module
     */
    protected function getMerchantModule()
    {
        return $this->controller->getMerchantModule();
    }

    public function run()
    {
        $depositRequest = $this->loadDepositRequest();
        $this->registerTransaction($depositRequest);
        $request = $this->createPurchaseRequest($depositRequest);

        return $this->handlePurchaseRequest($request);
    }

    /**
     * @return DepositRequest
     * @throws BadRequestHttpException
     */
    protected function loadDepositRequest()
    {
        $depositRequest = new DepositRequest();
        $depositRequest->load(Yii::$app->request->post());
        if (!$depositRequest->validate()) {
            throw new BadRequestHttpException('Deposit request is not loaded: ' . reset($depositRequest->getFirstErrors()));
        }

        $this->getMerchantModule()->prepareRequestData($depositRequest);

        return $depositRequest;
    }

    /**
     * @param DepositRequest $depositRequest
     * @return \hiqdev\yii2\merchant\models\PurchaseRequest
     */
    protected function createPurchaseRequest($depositRequest)
    {
        $request = $this->getMerchantModule()->getPurchaseRequest($depositRequest->merchant, $depositRequest);

        return $request;
    }

    /**
     * @param DepositRequest $depositRequest
     */
    protected function registerTransaction($depositRequest)
    {
        $this->trigger(self::EVENT_BEFORE_TRANSACTION_INSERT, new TransactionInsertEvent(['depositRequest' => $depositRequest]));

        $transaction = $this->getMerchantModule()->insertTransaction($depositRequest->id, $depositRequest->merchant, array_merge([
            'username' => $depositRequest->username,
        ], $depositRequest->toArray()));

        $this->trigger(self::EVENT_AFTER_TRANSACTION_INSERT, new TransactionInsertEvent([
            'depositRequest' => $depositRequest,
            'transaction' => $transaction
        ]));

    }

    /**
     * @param PurchaseRequest $response
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     */
    protected function handlePurchaseRequest($response)
    {
        if ('GET' === $response->getFormMethod()) {
            return $this->controller->redirect($response->getFormAction());
        } elseif ('POST' === $response->getFormMethod()) {
            $hiddenFields = '';
            foreach ($response->getFormInputs() as $key => $value) {
                $hiddenFields .= sprintf(
                        '<input type="hidden" name="%1$s" value="%2$s" />',
                        htmlentities($key, ENT_QUOTES, 'UTF-8', false),
                        htmlentities($value, ENT_QUOTES, 'UTF-8', false)
                    )."\n";
            }

            $output = '<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Redirecting...</title>
    </head>
    <body onload="document.forms[0].submit();">
        <form action="%1$s" method="post">
            <p>Redirecting to payment page...</p>
            <p>
                %2$s
                <input type="submit" value="Continue" />
            </p>
        </form>
    </body>
</html>';
            $output = sprintf(
                $output,
                htmlentities($response->getFormAction(), ENT_QUOTES, 'UTF-8', false),
                $hiddenFields
            );

            return $output;
        }

        throw new BadRequestHttpException();
    }
}
