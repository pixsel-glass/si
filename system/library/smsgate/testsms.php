<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Testsms extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        $sms_log->write('(TEST SMS) ' . $this->message);
    }
}