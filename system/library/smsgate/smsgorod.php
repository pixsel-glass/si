<?php

/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Smsgorod extends SmsGate
{

    private $baseurl = 'https://new.smsgorod.ru/';

    public function send()
    {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username) {
            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'VIRTA';
                $sms_log->write('(SmsGorod.ru) Notice: Default Sender set! Please input real Sender');
            }

            $credentials = [
                'apiKey' => $this->username,
            ];

            $balance = $this->getBalance($credentials);
            if ($balance['status'] == 'success') {

                $sms_log->write('(SmsGorod.ru): Status: ' . $balance['status'] . ' Balance: ' . $balance['data']);

                if ($balance['data']) {
                    $sms_param = [];
                    if ($this->to) {
                        $phone = $this->prepPhone($this->to);

                        $sms_param[] = [
                            'channel' => 'char',
                            'sender' => $sender,
                            'text' => $this->message,
                            'phone' => $phone,
                        ];
                    }

                    if ($this->copy) {
                        $numbers = explode(',', $this->copy);
                        foreach ($numbers as $number) {
                            $phone_multi = $this->prepPhone($number);

                            $sms_param[] = [
                                'channel' => "char",
                                'sender' => $sender,
                                'text' => $this->message,
                                'phone' => $phone_multi,
                            ];
                        }
                    }

                    $sms = [
                        'apiKey'   => $this->username,
                        'sms'      => $sms_param,
                    ];

                    $result = $this->sendSMS($sms);

                    $sms_log->write('(SmsGorod.ru) SEND: Status: ' . $result['status']);

                    if ($result['data']) {
                        foreach ($result['data'] as $data) {
                            $sms_log->write('(SmsGorod.ru) SMS ID: ' . $data['id'] . ' Status: ' . $data['status']);
                        }
                    }

                    if (!$this->to) {
                        $sms_log->write('(SmsGorod.ru) Error: Phone destination not found!');
                    }
                } else {
                    $sms_log->write('(SmsGorod.ru) : Status: ' . $balance['status'] . ', Balance is: ' . $balance['data']);
                }
            } else {
                $sms_log->write('(SmsGorod.ru) : Current Balance is 0 or Authorisation fault');
            }
        } else {
            $sms_log->write('(SmsGorod.ru) Error: Please set correct login/password!');
        }
    }

    public function getBalance($data)
    {
        $result = $this->sendRequest($this->baseurl . 'apiUsers/getUserBalanceInfo?' . http_build_query($data));

        return $result;
    }

    public function sendSms($data)
    {
        $result = $this->sendRequest($this->baseurl . 'apiSms/create', $data);

        return $result;
    }

    public function prepPhone($phone)
    {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function sendRequest($url, $param = [])
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($param) {
            $json_str = json_encode($param);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_str);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'accept: application/json'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $data = curl_exec($ch);
        $result = json_decode($data, true);
        curl_close($ch);

        return $result;
    }
}