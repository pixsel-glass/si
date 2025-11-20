<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class SmsClub extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $credentials = Array(
                'username' => $this->username,
                'token'    => $this->password,
            );

            $balance = $this->sendSms("http://gate.smsclub.mobi/token/getbalance.php?" . http_build_query($credentials));

            $balance_result = preg_replace("/[^,.0-9]/", '', $balance);

            if ($balance_result) {
                $sms_log->write('(Smsclub) Balance: ' . $this->resultStr($balance));

                if ($this->to) {
                    $numbers = $this->prepPhone($this->to);
                } else {
                    $sms_log->write('(Smsclub) Error: Phone destination not found!');
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
                    $sms_log->write('(Smsclub) Notice: Default Sender set! Please input real Sender');
                }

                $message = iconv('utf-8','windows-1251//IGNORE', $this->message);

                if ($numbers) {
                    $sms = Array(
                        'username' => $this->username,
                        'token'    => $this->password,
                        'from'     => $sender,
                        'to'       => $numbers,
                        'text'     => $message
                    );

                    $result = $this->sendSms("http://gate.smsclub.mobi/token/?" . http_build_query($sms));

                    $balance_after = $this->sendSms("http://gate.smsclub.mobi/token/getbalance.php?" . http_build_query($credentials));

                    $sms_log->write('(Smsclub) SMS send: ' . $this->resultStr($result) . ' Balance: ' . $this->resultStr($balance_after));

                    return true;
                }
            } else {
                $sms_log->write('(Smsclub) Error: Smsclub Authentication failed!');
            }
        } else {
            $sms_log->write('(Smsclub) Error: Please enter valid api_id in login(username) field!');
        }
    }

    public function resultStr($str) {
        $result = str_replace('<br/>', ' / ', $str);

        return $result;
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function sendSms($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}