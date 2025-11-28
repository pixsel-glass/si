<?php

namespace Bankart\Client\Transaction;

use Bankart\Client\Transaction\Base\AbstractTransactionWithReference;
use Bankart\Client\Transaction\Base\AmountableInterface;
use Bankart\Client\Transaction\Base\AmountableTrait;
use Bankart\Client\Transaction\Base\CustomerInterface;
use Bankart\Client\Transaction\Base\CustomerTrait;
use Bankart\Client\Transaction\Base\ItemsInterface;
use Bankart\Client\Transaction\Base\ItemsTrait;
use Bankart\Client\Transaction\Base\OffsiteInterface;
use Bankart\Client\Transaction\Base\OffsiteTrait;

/**
 * Payout: Payout a certain amount of money to the customer. (Debits the merchant's account, Credits the customer's account)
 *
 * @package Bankart\Client\Transaction
 */
class Payout extends AbstractTransactionWithReference
             implements AmountableInterface,
                        CustomerInterface,
                        ItemsInterface,
                        OffsiteInterface
{

    use AmountableTrait;
    use CustomerTrait;
    use ItemsTrait;
    use OffsiteTrait;

    /** @var string */
    protected $transactionToken;

    /** @var string */
    protected $language;

    /**
     * @return string
     */
    public function getTransactionToken()
    {
        return $this->transactionToken;
    }

    /**
     * @param string $transactionToken
     */
    public function setTransactionToken($transactionToken)
    {
        $this->transactionToken = $transactionToken;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

}