<?php

namespace hiqdev\yii2\merchant\tests\support;

use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\yii2\merchant\Collection;
use hiqdev\yii2\merchant\models\PurchaseRequest;

/**
 * Purchase request collection with two fixed payment systems, built without any real gateway.
 */
class FakePurchaseRequestCollection extends Collection
{
    public function init()
    {
        parent::init();
        $this->setItems([
            'card_usd' => ['system' => 'card', 'currency' => 'usd', 'method' => 'POST'],
            'wallet_eur' => ['system' => 'wallet', 'currency' => 'eur', 'method' => 'GET'],
        ]);
    }

    protected function createItem($id, array $config = [])
    {
        $request = new PurchaseRequest();
        $request->id = $id;
        $request->merchant_name = $id;
        $request->system = $config['system'] ?? null;
        $request->currency = $config['currency'] ?? null;
        $request->amount = $this->depositRequest->amount ?? null;

        $form = new RedirectPurchaseResponse('https://pay.example.com/' . $id, [
            'amount' => $request->amount,
            'transactionId' => $this->depositRequest->id ?? null,
        ]);
        $request->setForm($form->setMethod($config['method'] ?? 'POST'));

        return $request;
    }
}
