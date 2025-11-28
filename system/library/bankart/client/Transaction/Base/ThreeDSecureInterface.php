<?php

namespace Bankart\Client\Transaction\Base;
use Bankart\Client\Data\ThreeDSecureData;

/**
 * Interface ThreeDSecureInterface
 *
 * @package Bankart\Client\Transaction\Base
 */
interface ThreeDSecureInterface {

    /**
     * @return ThreeDSecureData
     */
    public function getThreeDSecureData();

    /**
     * @param ThreeDSecureData $threeDSecureData
     *
     * @return mixed
     */
    public function setThreeDSecureData($threeDSecureData);

}