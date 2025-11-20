<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Devinotel extends SmsGate {

    private $baseurl = 'https://integrationapi.net/rest/';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'TestMC';
                $sms_log->write('(Devinotel) Notice: TestMC Sender set! Please input real Sender');
            }

            $credentials = [
                'login'    => $this->username,
                'password' => $this->password,
            ];

            $session = $this->Auth($credentials);

            $session_id = str_replace('"', '', $session);

            $balance = $this->getBalance($session_id);

            $balance = round($balance);

            if ($balance) {
                $sms_log->write('(Devinotel) : Balance: ' . $balance);

                if ($this->to) {
                    $phone = $this->prepPhone($this->to);
                    $sms = array(
                        'sessionId'          => $session_id,
                        'sourceAddress'      => $sender,
                        'data'               => $this->message,
                        'destinationAddress' => $phone,
                    );

                    $result = $this->sendSMS($sms);

                    if ($result) {
                        $sms_log->write('(Devinotel) : SMS send result: ' . $result);
                    }
                } else {
                    $sms_log->write('(Devinotel) Error: Phone destination not found!');
                }

                if ($this->copy) {
                    $numbers = explode(',', $this->copy);
                    $phones = [];

                    foreach ($numbers as $number) {
                        $phones[] = $this->prepPhone($number);
                    }

                    $sms_multi = array(
                        'sessionId'            => $session_id,
                        'DestinationAddresses' => $phones,
                        'SourceAddress'        => $sender,
                        'Data'                 => $this->message,
                    );

                    $result = $this->sendBulkSMS($sms_multi);

                    if ($result) {
                        $sms_log->write('(Devinotel) : SMS send result: ' . $result);
                    }
                }
            } else {
                $sms_log->write('(Devinotel) : Authorisation fault/Current Balance is 0, Sms not send');
            }
        } else {
            $sms_log->write('(Devinotel) Error: Please set correct login/password!');
        }
    }

    public function Auth($credentials) {
        $result = $this->getRequest($this->baseurl . 'user/sessionid?' . http_build_query($credentials));

        return $result;
    }

    public function getBalance($session_id) {
        $result = $this->getRequest($this->baseurl . 'User/Balance?sessionId=' . $session_id);

        return $result;
    }

    public function sendSMS($data) {
        $result = $this->postRequest($this->baseurl . 'Sms/Send?', http_build_query($data));

        return $result;
    }

    public function sendBulkSMS($data) {
        $result = $this->postRequest($this->baseurl . 'Sms/SendBulk?', http_build_query($data));

        return $result;
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function getRequest($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function postRequest($url, $data) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $headers[] = 'Content-Length: ' . strlen($data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}