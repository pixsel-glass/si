<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Iqsms extends SmsGate {

    private $baseurl = 'http://api.iqsms.ru/messages/v2/';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'TEST';
                $sms_log->write('(IQSMS) Notice: Default Sender set! Please input real Sender');
            }

            $credentials = array(
                'login'    => $this->username,
                'password' => $this->password,
            );

            $balance = $this->sendRequest('balance/?' . http_build_query($credentials));

            if ($balance) {
                $sms_log->write('(IQSMS) : Balance: ' . $balance);

                if ($this->to) {
                    $phone = $this->prepPhone($this->to);
                    $sms = array(
                        'login'    => $this->username,
                        'password' => $this->password,
                        'phone'    => $phone,
                        'text'     => $this->message,
                        'sender'   => $sender,
                    );

                    $result = $this->sendRequest('send/?' . http_build_query($sms));

                    $accepted = preg_replace('/;(.*)/', '', $result);

                    if ($accepted == 'accepted') {
                        $sms_log->write('(IQSMS) SMS send, Status: ' . $result);
                    } else {
                        $sms_log->write('(IQSMS) : Result: ' . $result);
                    }
                } else {
                    $sms_log->write('(IQSMS) Error: Phone destination not found!');
                }

                if ($this->copy) {
                    $numbers = explode(',', $this->copy);
                    foreach ($numbers as $number) {
                        $phone_multi = $this->prepPhone($number);

                        $sms_multi = array(
                            'login'    => $this->username,
                            'password' => $this->password,
                            'phone'    => $phone_multi,
                            'text'     => $this->message,
                            'sender'   => $sender,
                        );

                        $result = $this->sendRequest('send/?' . http_build_query($sms_multi));

                        $accepted = preg_replace('/;(.*)/', '', $result);

                        if ($accepted == 'accepted') {
                            $sms_log->write('(IQSMS) SMS: ' . $result);
                        } else {
                            $sms_log->write('(IQSMS) : Result: ' . $result);
                        }
                    }
                }
            } else {
                $sms_log->write('(IQSMS) : Current Balance is 0, Sms not send or Authorisation fault');
            }
        } else {
            $sms_log->write('(IQSMS) Error: Please set correct login/password!');
        }
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function sendRequest($data) {
        $url = $this->baseurl . $data;

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