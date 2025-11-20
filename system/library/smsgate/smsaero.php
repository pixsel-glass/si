<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Smsaero extends SmsGate {

    private $email = '';

    private $api_key = '';

    private $baseurl = 'https://gate.smsaero.ru/v2/';

    private $channel = 'DIRECT';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $this->email = $this->username;
            $this->api_key = $this->password;

            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'SMS_60294';
                $sms_log->write('(Smsaero) Notice: Default Sender set! Please input real Sender');
            }

            $auth = $this->getRequest($method = 'auth');

            if (is_array($auth) && $auth['success']) {
                $balance = $this->getRequest($method = 'balance');

                if (is_array($balance) && $balance['success'] && isset($balance['data']['balance'])) {
                    $sms_log->write('(Smsaero) : Balance Result: ' . $balance['data']['balance'] . $balance['message']);
                } else {
                    $sms_log->write('(Smsaero) : Balance Result - ' . $balance['message']);
                }

                if ($this->to) {
                    $number = $this->prepPhone($this->to);

                    $sms = array(
                        'number'  => $number,
                        'sign'    => $sender,
                        'text'    => $this->message,
                        'channel' => $this->channel,
                    );

                    $sms_result = $this->getRequest($method = 'sms/send/', $sms);

                    if (is_array($sms_result) && $sms_result['success']) {
                        $sms_log->write('(Smsaero) : Sms Result: Status:' . $sms_result['data']['extendStatus'] . 'Cost:' . $sms_result['data']['cost']);
                    } else {
                        $sms_log->write('(Smsaero) : Sms Result: ' . $sms_result['message']);
                    }
                } else {
                    $sms_log->write('(Smsaero) Error: Phone destination not found!');
                }

                if ($this->copy) {
                    $phones = explode(',', $this->copy);

                    $numbers = [];

                    foreach ($phones as $phone) {
                        $numbers[] = $this->prepPhone($phone);
                    }

                    $sms_bulk = array(
                        'numbers' => $numbers,
                        'sign'    => $sender,
                        'text'    => $this->message,
                        'channel' => $this->channel,
                    );

                    $sms_bulk_result = $this->getRequest($method = 'sms/send/', $sms_bulk);

                    if (is_array($sms_bulk_result) && $sms_bulk_result['success']) {
                        $sms_log->write('(Smsaero) : Sms Result: Status:' . $sms_bulk_result['data']['extendStatus'] . 'Cost:' . $sms_bulk_result['data']['cost']);
                    } else {
                        $sms_log->write('(Smsaero) : Sms Result: ' . $sms_bulk_result['message']);
                    }
                }
            } else {
                $sms_log->write('(Smsaero) : Auth Result - ' . $auth['message']);
            }
        } else {
            $sms_log->write('(Smsaero) Error: Please set correct login/password!');
        }
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    private function getRequest($method, $post = []) {
        $url = $this->baseurl . $method;

        $options = array(
            CURLOPT_POST           => 1,
            CURLOPT_HEADER         => 0,
            CURLOPT_URL            => $url,
            CURLOPT_FRESH_CONNECT  => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE   => 1,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_POSTFIELDS     => http_build_query($post),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERPWD        => $this->email . ":" . $this->api_key,
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        if (!$result = curl_exec($ch)) {
            $result = curl_error($ch);

            $sms_log = new Log('sms_log.log');
            $sms_log->write(print_r($result, true));

            return false;
        }

        curl_close($ch);

        $response = json_decode($result, true);

        return $response;
    }
}