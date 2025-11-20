<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Bytehand extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $credentials = Array(
                'id'  => $this->username,
                'key' => $this->password
            );

            $balance = file_get_contents('http://api.bytehand.com/v1/balance?' . http_build_query($credentials));

            $balance_result = json_decode($balance);

            $sms_log->write('(Bytehand) Balance: ' . $balance_result->description);

            if ($balance_result->description > 0) {
                if ($this->to) {
                    $phone = $this->prepPhone($this->to);
                } else {
                    $phone = false;
                    $sms_log->write('(Bytehand) Error: Phone destination not found!');
                }

                if ($this->from) {
                    $sender = $this->from;
                } else {
                    $sender = '';
                    $sms_log->write('(Bytehand) Error: Please input Sender');
                }

                if ($balance_result->description && $phone) {
                    $sms = Array(
                        'id'   => $this->username,
                        'key'  => $this->password,
                        'from' => $sender,
                        'to'   => $phone,
                        'text' => $this->message
                    );

                    $result = $this->sendSms($sms);

                    $send_result = json_decode($result);

                    $sms_log->write('(Bytehand) SMS send: SMS ID: ' . $send_result->description);

                    if ($this->copy) {
                        $numbers = explode(',', $this->copy);
                        foreach ($numbers as $number) {
                            if (strlen($number) < 3) {
                                continue;
                            }
                            $sms['to'] = $this->prepPhone($number);
                            $multi_result = $this->sendSms($sms);

                            $send_multi = json_decode($multi_result);

                            $sms_log->write('(Bytehand) SMS send: SMS ID: ' . $send_multi->description);

                            sleep(1);
                        }
                    }

                    return $result;
                }
            } else {
                $sms_log->write('(Bytehand) Error #:' . $balance_result->status . 'Check your Balance!');
            }
        } else {
            $sms_log->write('(Bytehand) Error: Please enter valid ID or KEY!');
        }
    }

    private function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    private function sendSms($data) {
        $result = file_get_contents("http://api.bytehand.com/v1/send?" . http_build_query($data));

        return $result;
    }
}