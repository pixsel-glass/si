<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Smssimple extends SmsGate {

    private $baseurl = 'http://smsimple.ru/';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $credentials = Array(
                'user' => $this->username,
                'pass' => $this->password,
            );

            $balance = $this->sendSms($this->baseurl . "http_balance.php?" . http_build_query($credentials));

            $balance_result = preg_replace("/[^,.0-9]/", '', $balance);

            $balance_result = floor($balance_result);

            if ($balance_result) {
                $sms_log->write('(SmsSimple) Balance: ' . $balance);

                if ($this->to) {
                    $numbers = $this->prepPhone($this->to);
                } else {
                    $sms_log->write('(SmsSimple) Error: Phone destination not found!');
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
                    $sms_log->write('(SmsSimple) Notice: Default Sender set! Please input real Sender');
                }

                if ($numbers) {
                    $sms = Array(
                        'user'    => $this->username,
                        'pass'    => $this->password,
                        'phone'   => $numbers,
                        'message' => $this->message,
                        'or_id'   => $sender,
                    );

                    $result = $this->sendSms($this->baseurl . "http_send.php?" . http_build_query($sms));

                    $balance_after = $this->sendSms($this->baseurl . "http_balance.php?" . http_build_query($credentials));

                    $sms_log->write('(SmsSimple) SMS send: ' . $result . ' Balance: ' . $balance_after);

                    return true;
                }
            } else {
                $sms_log->write('(SmsSimple) Error: SmsSimple Authentication failed!');
            }
        } else {
            $sms_log->write('(SmsSimple) Error: Please enter valid api_id in login(username) field!');
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