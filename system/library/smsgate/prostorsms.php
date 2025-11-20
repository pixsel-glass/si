<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Prostorsms extends SmsGate {

    private $baseurl = 'http://api.prostor-sms.ru/messages/v2/';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'TEST';
                $sms_log->write('(Prostorsms) Notice: Default Sender set! Please input real Sender');
            }

            $balance = $this->getBalance($this->username, $this->password);

            if (isset($balance[1])) {
                $sms_log->write('(Prostorsms) : Balance: ' . $balance[1]);

                if ($this->to) {
                    $phone = $this->prepPhone($this->to);
                    $sms = array(
                        'login'    => $this->username,
                        'password' => $this->password,
                        'phone'    => $phone,
                        'text'     => $this->message,
                        'sender'   => $sender
                    );

                    $response = $this->sendSMS($this->baseurl . 'send/?' . http_build_query($sms));

                    $sms_log->write('(Prostorsms) : SmsSend: Response: ' . $response[0] . ' ID: ' . $response[1]);

                    $status = $this->getStatus($this->username, $this->password, $response[1]);

                    $sms_log->write('(Prostorsms) : SmsSend: ID: ' . $status[0] . ' Status: ' . $status[1]);
                } else {
                    $sms_log->write('(Prostorsms) Error: Phone destination not found!');
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

                        $response = $this->sendSMS($this->baseurl . 'send/?' . http_build_query($sms_multi));

                        $sms_log->write('(Prostorsms) : SmsSend: Response: ' . $response[0] . ' ID: ' . $response[1]);

                        $status = $this->getStatus($this->username, $this->password, $response[1]);

                        $sms_log->write('(Prostorsms) : SmsSend: ID: ' . $status[0] . ' Status: ' . $status[1]);
                    }
                }
            } else {
                $sms_log->write('(Prostorsms) : Current Balance is 0, Sms not send or Authorisation fault: ' . $balance[0]);
            }
        } else {
            $sms_log->write('(Prostorsms) Error: Please set correct login/password!');
        }
    }

    public function getStatus($login, $password, $id) {
        $result = $this->sendSms($this->baseurl . 'status/?login=' . rawurlencode($login) . '&password=' . rawurlencode($password) . '&id=' . rawurlencode($id));

        return $result;
    }

    public function getBalance($login, $password) {
        $result = $this->sendSms($this->baseurl . 'balance/?login=' . rawurlencode($login) . '&password=' . rawurlencode($password));

        return $result;
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
        $result = explode(';', $data);

        return $result;
    }
}