<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Smstraffic extends SmsGate {

    private $baseurl = 'http://sds.smstraffic.ru/smartdelivery-in/multi.php';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'smsinfo';
                $sms_log->write('(Smstraffic) Notice: Default Sender set! Please input real Sender');
            }

            $balance = $this->getBalance($this->username, $this->password);

            if (isset($balance['account']) && $balance['account'] > 0) {
                $sms_log->write('(Smstraffic) : Balance: ' . $balance['account']);

                if ($this->to) {
                    $phone = $this->prepPhone($this->to);

                    $sms_data = array(
                        'login'      => $this->username,
                        'password'   => $this->password,
                        'phones'     => $phone,
                        'message'    => $this->message,
                        'originator' => $sender,
                        'rus'        => 5,
                    );

                    if ($this->viber['status']) {
                        if($this->viber['ttl']) {
                             $sms_data['route'] = 'viber(' . $this->viber['ttl'] . ')-sms';
                        }else{
                            $sms_data['route'] = 'viber(' . $this->viber['ttl'] . ')-sms';
                        }
                        if($this->viber['image_url']) {
                            $sms_data['image_url'] = $this->viber['image_url'];
                        }
                        if($this->viber['caption']) {
                            $sms_data['btn_name'] = $this->viber['caption'];
                        }
                        if($this->viber['action']) {
                            $sms_data['btn_url'] = $this->viber['action'];
                        }
                    }else{
                        $sms_data['route'] = 'sms';
                    }

                    $response = $this->sendRequest($this->baseurl . '?' . http_build_query($sms_data));
                    if ($response) {
                        $sms_log->write('(Smstraffic) : SmsSend: Result: ' . $response['result'] . ' Code: ' . $response['code'] . ' Desc: ' . $response['description']);
                    }
                } else {
                    $sms_log->write('(Smstraffic) Error: Phone destination not found!');
                }

                if ($this->copy) {
                    $numbers = explode(',', $this->copy);
                    foreach ($numbers as $number) {
                        $phone_multi = $this->prepPhone($number);
                    }
                    $numbers = implode(',', $phone_multi);

                    $sms_multi_data = array(
                        'login'      => $this->username,
                        'password'   => $this->password,
                        'phones'     => $numbers,
                        'message'    => $this->message,
                        'originator' => $sender,
                        'rus'        => 5,
                    );

                    if ($this->viber['status']) {
                        if($this->viber['ttl']) {
                             $sms_multi_dataх['route'] = 'viber(' . $this->viber['ttl'] . ')-sms';
                        }else{
                            $sms_multi_dataх['route'] = 'viber(' . $this->viber['ttl'] . ')-sms';
                        }
                        if($this->viber['image_url']) {
                            $sms_multi_dataх['image_url'] = $this->viber['image_url'];
                        }
                        if($this->viber['caption']) {
                            $sms_multi_dataх['btn_name'] = $this->viber['caption'];
                        }
                        if($this->viber['action']) {
                            $sms_multi_dataх['btn_url'] = $this->viber['action'];
                        }
                    }else{
                        $sms_multi_dataх['route'] = 'sms';
                    }

                    $response = $this->sendRequest($this->baseurl . '?' . http_build_query($sms_multi_data));

                    if ($response) {
                        $sms_log->write('(Smstraffic) : SmsSend: Result: ' . $response['result'] . ' Code: ' . $response['code'] . ' Desc: ' . $response['description']);
                    }
                }
            } else {
                $sms_log->write('(Smstraffic) : Current Balance is ' . $balance['account'] . ', Sms not send!');
            }
        } else {
            $sms_log->write('(Smstraffic) Error: Please set correct login/password!');
        }
    }

    public function getStatus($login, $password, $id) {
        $result = $this->sendRequest($this->baseurl . '?login=' . rawurlencode($login) . '&password=' . rawurlencode($password) . '&sms_id=' . rawurlencode($id) . '&operation=' . rawurlencode('status'));

        return $result;
    }

    public function getBalance($login, $password) {
        $result = $this->sendRequest($this->baseurl . '?login=' . rawurlencode($login) . '&password=' . rawurlencode($password) . '&operation=' . rawurlencode('account'));

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

        $result = curl_exec($ch);
        curl_close($ch);

        $xml = json_encode(simplexml_load_string($result));
        $result = json_decode($xml, true);

        return $result;
    }
}