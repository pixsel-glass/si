<?php

namespace Bankart\Client\CustomerProfile;

use Bankart\Client\Data\PaymentData\PaymentData;
use Bankart\Client\Json\DataObject;

/**
 * Class PaymentInstrument
 *
 * @package Bankart\Client\CustomerProfile
 *
 * @property string $method
 * @property string $paymentToken
 * @property \DateTime $createdAt
 * @property PaymentData $paymentData
 * @property bool $isPreferred
 */
class PaymentInstrument extends DataObject {

    const METHOD_CARD = 'card';
    const METHOD_IBAN = 'iban';
    const METHOD_WALLET = 'wallet';


    /**
     * @param \DateTime|string $createdAt
     *
     * @return PaymentInstrument
     * @throws \Exception
     */
    public function setCreatedAt($createdAt) {
        if (!empty($createdAt) && is_string($createdAt)) {
            $createdAt = new \DateTime($createdAt);
        }
        $this->createdAt = $createdAt;
        return $this;
    }


}