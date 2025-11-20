<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Websms extends SmsGate {

    private $baseurl = 'http://cab.websms.ru/';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $balance = $this->getBalance($this->username, $this->password);

            if ($balance) {
                if ($this->to) {
                    $numbers = $this->prepPhone($this->to);
                } else {
                    $sms_log->write('(WebSMS) Error: Phone destination not found!');
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

                $result = $this->sendSMS($this->username, $this->password, $numbers, $this->message, $this->from);

                if ($result['error'] && $result['error'] !== 'OK') {
                    $sms_log->write('(WebSMS) :' . $result['error'] . ' Error #' . $result['err_num']);
                } else {
                    $sms_log->write('(WebSMS) : Result ' . $result['error'] . ', Current Balance: ' . $result['balance_after']);
                }
            } else {
                $sms_log->write('(WebSMS) : Current Balance is 0, Sms not send');
            }
        } else {
            $sms_log->write('(WebSMS) Error: Please set correct login/password!');
        }
    }

    public function sendSMS($login, $password, $phone, $text, $sender) {
        $post = array(
            'http_username' => rawurlencode($login),
            'http_password' => rawurlencode($password),
            'message'       => rawurlencode($text),
            'phone_list'    => rawurlencode($phone),
            'fromPhone'     => rawurlencode($sender),
        );

        $post = json_encode($post);

        $result = @file_get_contents($this->baseurl . 'json_in5.asp?json=' . $post);

        $result = json_decode($result, true);

        return $result;
    }

    public function getBalance($login, $password) {
        $result = @file_get_contents($this->baseurl . 'http_credit.asp?http_username=' . rawurlencode($login) . '&http_password=' . rawurlencode($password));

        return $result;
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }
}