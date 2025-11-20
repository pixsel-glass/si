<?php

/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class LetsAds extends SmsGate
{
    private $baseurl = 'https://letsads.com/api';

    public function send()
    {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {

            $balance = $this->getBalance($this->username, $this->password);

            $result_balance = $this->decodeXMl($balance);
            
            if ($result_balance) {
                $sms_log->write("(LetsAds) Balance: Name: " . $result_balance['name'] . " Desc: " . $result_balance['description']);
            }

            if ($result_balance['name'] !== 'Error' && $result_balance['description']) {
                if ($this->to) {
                    $phone = $this->prepPhone($this->to);
                } else {
                    $phone = false;
                    $sms_log->write('(LetsAds) Error: Phone destination not found!');
                }

                if ($this->from) {
                    $sender = $this->from;
                } else {
                    $sender = '';
                    $sms_log->write('(LetsAds) Error: Please input Sender');
                }

                if ($phone) {
                    $xml = '<?xml version="1.0" encoding="utf-8"?>';
                    $xml .= '<request>';
                    $xml .= '<auth>';
                    $xml .= '<login>' . $this->username . '</login>';
                    $xml .= '<password>' . $this->password . '</password>';
                    $xml .= '</auth>';
                    $xml .= '<message>';
                    $xml .= '<from>' . $sender . '</from>';
                    $xml .= '<text>'. $this->message . '</text>';
                    $xml .= '<recipient>' . $phone . '</recipient>';

                    if ($this->copy) {
                        $numbers = explode(',', $this->copy);
                        foreach ($numbers as $number) {
                            $xml .= '<recipient>' . $this->prepPhone($number) . '</recipient>';
                        }
                    }
                    $xml .= '</message>';
                    $xml .= '</request>';

                    $sms = $this->sendRequest($xml);

                    if ($sms) {
                        $result_sms = $this->decodeXMl($sms);

                        if ($result_sms && $result_sms['name'] !== 'Error') {
                            if ($result_sms['sms_id'] && !is_array($result_sms['sms_id'])) {
                                $sms_log->write("(LetsAds) SMS send: Status: " . $result_sms['name'] . " Desc: " . $result_sms['description'] . " Sms_id: " . $result_sms['sms_id']);

                                $status = $this->checkStatus($this->username, $this->password, $result_sms['sms_id']);
                                $result_status = $this->decodeXMl($status);

                                $sms_log->write("(LetsAds) SMS Send Status: Sms_id: " . $result_sms['sms_id'] . " Status: " . $result_status['description']);
                            }

                            if ($result_sms['sms_id'] && is_array($result_sms['sms_id'])) {
                                foreach ($result_sms['sms_id'] as $sms_id) {
                                    $sms_log->write("(LetsAds) SMS send: Status: " . $result_sms['name'] . " Desc: " . $result_sms['description'] . " Sms_id: " . $sms_id);

                                    $status = $this->checkStatus($this->username, $this->password, $sms_id);
                                    $result_status = $this->decodeXMl($status);

                                    $sms_log->write("(LetsAds) SMS Send Status: Sms_id: " . $sms_id . " Status: " . $result_status['description']);
                                }
                            }
                        }else{
                            $sms_log->write("(LetsAds) SMS send: Status: " . $result_sms['name'] . " Desc: " . $result_sms['description']);
                        }
                    }
                }
            } else {
                $sms_log->write("(LetsAds) Error: Невозможно получить баланс!");
            }
        } else {
            $sms_log->write('(LetsAds) Error: Please enter valid Login or Password!');
        }
    }

    private function getBalance($login, $password)
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml  .= '<request><auth><login>' . $login . '</login>';
        $xml  .= '<password>' . $password . '</password>';
        $xml  .= '</auth><balance /></request>';

        return $this->sendRequest($xml);
    }


    private function checkStatus($login, $password, $sms_id)
    {
        if ($sms_id) {
            $xml = '<?xml version="1.0" encoding="utf-8"?>';
            $xml .= '<request>';
            $xml .= '<auth>';
            $xml .= '<login>' . $login . '</login>';
            $xml .= '<password>' . $password . '</password>';
            $xml .= '</auth>';
            $xml .= '<sms_id>' . $sms_id . '</sms_id>';
            $xml .= '</request>';

            return $this->sendRequest($xml);
        }
    }

    private function sendRequest($xml)
    {

        $ch = curl_init($this->baseurl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function prepPhone($phone)
    {
        $result = preg_replace('/\+?\d+,/', '', $phone);

        return $result;
    }

    private function decodeXMl($xml)
    {
        $result = simplexml_load_string($xml);
        $result = json_decode(json_encode($result), true);

        return $result;
    }
}
