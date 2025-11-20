<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Rocketsms extends SmsGate {

    private $baseurl = 'https://api.rocketsms.by/json/';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'RSMS.BY';
                $sms_log->write('(Rocketsms) Notice: Default Sender set! Please input real Sender');
            }

            $credentials = array(
                'username' => $this->username,
                'password' => $this->password,
            );

            $balance = $this->getBalance($credentials);

            if ($balance) {
                $sms_log->write('(Rocketsms) : Balance: ' . $balance);

                if ($this->to) {
                    $phone = $this->prepPhone($this->to);

                    $sms = array(
                        'username' => $this->username,
                        'password' => $this->password,
                        'phone'    => $phone,
                        'text'     => $this->message,
                        'sender'   => $sender,
                    );

                    $response = $this->sendSMS($sms);

                    $sms_log->write('(Rocketsms) : ' . print_r($response, true));
                } else {
                    $sms_log->write('(Rocketsms) Error: Phone destination not found!');
                }

                if ($this->copy) {
                    $numbers = explode(',', $this->copy);
                    foreach ($numbers as $number) {
                        $phone_multi = $this->prepPhone($number);

                        $sms_multi = array(
                            'username' => $this->username,
                            'password' => $this->password,
                            'phone'    => $phone_multi,
                            'text'     => $this->message,
                            'sender'   => $sender,
                        );

                        $response = $this->sendSMS($sms_multi);

                        $sms_log->write('(Rocketsms) : ' . print_r($response, true));
                    }
                }
            } else {
                $sms_log->write('(Rocketsms) : Current Balance is 0, Sms not send or Authorisation fault');
            }
        } else {
            $sms_log->write('(Rocketsms) Error: Please set correct login/password!');
        }
    }

    public function getBalance($data) {
        $result = $this->sendRequest($this->baseurl . 'balance?' . http_build_query($data));

        return $result;
    }

    public function sendSms($data) {
        $result = $this->sendRequest($this->baseurl . 'send?' . http_build_query($data));

        return $result;
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function sendRequest($url) {
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