<?php

namespace hiqdev\yii2\merchant\transactions;

interface TransactionRepositoryInterface
{
    /**
     * @param string $id
     * @return Transaction
     * @throws TransactionException when transaction with the specified ID
     * does not exists
     */
    public function findById($id);

    /**
     * @param Transaction $transaction
     * @return Transaction
     * @throws TransactionException when transaction save failed
     */
    public function save($transaction);

    /**
     * @param string $id
     * @param string $merchant
     * @param array $parameters
     * @return Transaction
     */
    public function create($id, $merchant, $parameters);

    /**
     * @param Transaction $transaction
     * @return Transaction
     * @throws TransactionException when transaction save failed
     */
    public function insert($transaction);
}
