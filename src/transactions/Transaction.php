<?php

namespace hiqdev\yii2\merchant\transactions;

class Transaction
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var boolean
     */
    protected $success;

    /**
     * @var string
     */
    protected $merchant;

    /**
     * Transaction constructor.
     * @param string $id
     * @param $merchant
     */
    public function __construct($id, $merchant)
    {
        $this->id = $id;
        $this->merchant = $merchant;
    }

    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->success !== null;
    }

    public function complete()
    {
        $this->success = true;
    }

    public function cancel($error = null)
    {
        $this->success = false;
        $this->addParameter('error', $error);
    }

    public function isConfirmed()
    {
        return $this->success === true;
    }

    /**
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Adds parameter $parameter with value $value to the parameters
     *
     * @param string $parameter
     * @param mixed $value
     */
    public function addParameter($parameter, $value)
    {
        if (isset($this->parameters[$parameter])) {
            return;
        }

        $this->parameters[$parameter] = $value;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'merchant' => $this->getMerchant(),
            'parameters' => $this->getParameters(),
            'success' => $this->getSuccess()
        ];
    }

    public function getParameter($parameter)
    {
        return isset($this->parameters[$parameter]) ? $this->parameters[$parameter] : null;
    }
}
