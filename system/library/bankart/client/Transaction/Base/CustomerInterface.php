<?php

namespace Bankart\Client\Transaction\Base;
use Bankart\Client\Data\Customer;

/**
 * Interface CustomerInterface
 *
 * @package Bankart\Client\Transaction\Base
 */
interface CustomerInterface {

    /**
     * @return Customer
     */
    public function getCustomer();

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer);

}