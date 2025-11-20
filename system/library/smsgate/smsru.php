<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Smsru extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username) {
            $credentials = Array(
                'api_id' => $this->username,
                'json'   => '1',
            );

            $auth = $this->sendSms("https://sms.ru/auth/check?" . http_build_query($credentials));

            $auth_result = json_decode($auth);

            $sms_log->write('(SMS.ru) Authentication: Status = ' . $auth_result->status);

            if ($auth_result->status == 'OK') {
                $balance = $this->sendSms("https://sms.ru/my/balance?" . http_build_query($credentials));

                $balance_result = json_decode($balance);

                $sms_log->write('(SMS.ru) Balance: Status = ' . $balance_result->status . ' Balance: ' . $balance_result->balance);

                if ($this->to) {
                    $numbers = $this->prepPhone($this->to);
                } else {
                    $sms_log->write('(SMS.ru) Error: Phone destination not found!');
                    $numbers = false;
                }

                if ($this->copy) {
                    $copyes = explode(',', $this->copy);

                    $phones = array();

                    foreach ($copyes as $phone) {
                        $phones[] = $this->prepPhone($phone);
                    }

                    $phones = implode(',', $phones);

                    $numbers = $this->prepPhone($this->to) . ',' . $phones;
                }

                if ($this->from) {
                    $sender = $this->from;
                } else {
                    $sender = '';
                    $sms_log->write('(SMS.ru) Notice: Default Sender set! Please input real Sender');
                }

                if ($balance_result->balance && $numbers) {
                    $sms = Array(
                        'api_id' => $this->username,
                        'to'     => $numbers,
                        'msg'    => $this->message,
                        'from'   => $sender,
                        'time'   => 0,
                        'json'   => '1'
                    );

                    $result = $this->sendSms("https://sms.ru/sms/send?" . http_build_query($sms));

                    $send_result = json_decode($result);

                    if ($send_result->status == 'OK') {
                        $sms_log->write('(SMS.ru) SMS send: ' . $send_result->status . ' Balance: ' . $send_result->balance);
                    }

                    return $result;
                }
            } else {
                $sms_log->write('(SMS.ru) Error: SMS.ru Authentication failed!');
            }
        } else {
            $sms_log->write('(SMS.ru) Error: Please enter valid api_id in login(username) field!');
        }
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function sendSms($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}