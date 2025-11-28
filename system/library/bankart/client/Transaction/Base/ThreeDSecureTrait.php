<?php

namespace Bankart\Client\Transaction\Base;

use Bankart\Client\Data\ThreeDSecureData;

/**
 * Class ThreeDSecureTrait
 *
 * @package Bankart\Client\Transaction\Base
 */
trait ThreeDSecureTrait {

    /** @var ThreeDSecureData */
    protected $threeDSecureData;

    /**
     * @return ThreeDSecureData
     */
    public function getThreeDSecureData()
    {
        return $this->threeDSecureData;
    }

    /**
     * @param ThreeDSecureData $threeDSecureData
     *
     * @return $this
     */
    public function setThreeDSecureData($threeDSecureData)
    {
        $this->threeDSecureData = $threeDSecureData;
        return $this;
    }

}