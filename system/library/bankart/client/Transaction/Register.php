<?php

namespace Bankart\Client\Transaction;

use Bankart\Client\Transaction\Base\AbstractTransaction;
use Bankart\Client\Transaction\Base\AddToCustomerProfileInterface;
use Bankart\Client\Transaction\Base\AddToCustomerProfileTrait;
use Bankart\Client\Transaction\Base\CustomerInterface;
use Bankart\Client\Transaction\Base\CustomerTrait;
use Bankart\Client\Transaction\Base\OffsiteInterface;
use Bankart\Client\Transaction\Base\OffsiteTrait;
use Bankart\Client\Transaction\Base\ScheduleInterface;
use Bankart\Client\Transaction\Base\ScheduleTrait;
use Bankart\Client\Transaction\Base\ThreeDSecureInterface;
use Bankart\Client\Transaction\Base\ThreeDSecureTrait;

/**
 * Register: Register the customer's payment data for recurring charges.
 *
 * The registered customer payment data will be available for recurring transaction without user interaction.
 *
 * @package Bankart\Client\Transaction
 */
class Register extends AbstractTransaction
               implements AddToCustomerProfileInterface,
                          CustomerInterface,
                          OffsiteInterface,
                          ScheduleInterface,
                          ThreeDSecureInterface
{

    use AddToCustomerProfileTrait;
    use CustomerTrait;
    use OffsiteTrait;
    use ScheduleTrait;
    use ThreeDSecureTrait;

    /** @var string */
    protected $language;

    /** @var string */
    protected $transactionToken;

    /**
     * @var string
     */
    protected $transactionIndicator;

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

    
    /**
     * @return string
     */
    public function getTransactionIndicator() {
        return $this->transactionIndicator;
    }

    /**
     * @param string $transactionIndicator
     */
    public function setTransactionIndicator($transactionIndicator) {
        $this->transactionIndicator = $transactionIndicator;
    }
}