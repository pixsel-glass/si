<?php

namespace Bankart\Client\Transaction;

use Bankart\Client\Transaction\Base\AbstractTransactionWithReference;
use Bankart\Client\Transaction\Base\AddToCustomerProfileInterface;
use Bankart\Client\Transaction\Base\AddToCustomerProfileTrait;
use Bankart\Client\Transaction\Base\AmountableInterface;
use Bankart\Client\Transaction\Base\AmountableTrait;
use Bankart\Client\Transaction\Base\CustomerInterface;
use Bankart\Client\Transaction\Base\CustomerTrait;
use Bankart\Client\Transaction\Base\ItemsInterface;
use Bankart\Client\Transaction\Base\ItemsTrait;
use Bankart\Client\Transaction\Base\OffsiteInterface;
use Bankart\Client\Transaction\Base\OffsiteTrait;
use Bankart\Client\Transaction\Base\ScheduleInterface;
use Bankart\Client\Transaction\Base\ScheduleTrait;
use Bankart\Client\Transaction\Base\ThreeDSecureInterface;
use Bankart\Client\Transaction\Base\ThreeDSecureTrait;

/**
 * Preauthorize: Reserve a certain amount, which can be captured (=charging) or voided (=revert) later on.
 *
 * @package Bankart\Client\Transaction
 */
class Preauthorize extends AbstractTransactionWithReference
                   implements AddToCustomerProfileInterface,
                              AmountableInterface,
                              CustomerInterface,
                              ItemsInterface,
                              OffsiteInterface,
                              ScheduleInterface,
                              ThreeDSecureInterface
{

    use AddToCustomerProfileTrait;
    use AmountableTrait;
    use CustomerTrait;
    use ItemsTrait;
    use OffsiteTrait;
    use ScheduleTrait;
    use ThreeDSecureTrait;
    
    const TRANSACTION_INDICATOR_SINGLE = 'SINGLE';
    const TRANSACTION_INDICATOR_INITIAL = 'INITIAL';
    const TRANSACTION_INDICATOR_RECURRING = 'RECURRING';
    const TRANSACTION_INDICATOR_CARDONFILE = 'CARDONFILE';
    const TRANSACTION_INDICATOR_CARDONFILE_MERCHANT = 'CARDONFILE_MERCHANT';

    /** @var string */
    protected $transactionToken;

    /** @var bool */
    protected $withRegister = false;

    /** @var string */
    protected $transactionIndicator;

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
     * @return boolean
     */
    public function isWithRegister() {
        return $this->withRegister;
    }

    /**
     * set true if you want to register a user vault together with the debit
     *
     * @param boolean $withRegister
     *
     * @return $this
     */
    public function setWithRegister($withRegister) {
        $this->withRegister = $withRegister;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionIndicator() {
        return $this->transactionIndicator;
    }

    /**
     * @param string $transactionIndicator
     *
     * @return $this
     */
    public function setTransactionIndicator($transactionIndicator) {
        $this->transactionIndicator = $transactionIndicator;
        return $this;
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