<?php

namespace Bankart\Client\Transaction\Base;

use Bankart\Client\Schedule\ScheduleData;
use Bankart\Client\Schedule\ScheduleWithTransaction;

interface ScheduleInterface {

    /**
     * @return ScheduleData|ScheduleWithTransaction
     */
    public function getSchedule();

    /**
     * @param ScheduleData|ScheduleWithTransaction $schedule |null
     *
     * @return $this
     */
    public function setSchedule($schedule = null);
}