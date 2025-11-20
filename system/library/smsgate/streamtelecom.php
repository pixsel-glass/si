<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Streamtelecom extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $balance = file_get_contents('http://gateway.api.sc/get/?user=' . $this->username . '&pwd=' . $this->password . '&balance=1');

            $balance_result = preg_replace('/[^0-9,.]?/', '', $balance);

            $sms_log->write('(Stream Telecom) Balance: ' . $balance_result);

            if ($balance_result > 0) {
                if ($this->to) {
                    $phone = $this->prepPhone($this->to);
                } else {
                    $phone = false;
                    $sms_log->write('(Stream Telecom) Error: Phone destination not found!');
                }

                if ($this->from) {
                    $sender = $this->from;
                } else {
                    $sender = '';
                    $sms_log->write('(Stream Telecom) Error: Please input Sender');
                }

                if ($balance_result && $phone) {
                    $sms = Array(
                        'user' => $this->username,
                        'pwd'  => $this->password,
                        'sadr' => $sender,
                        'dadr' => $phone,
                        'text' => $this->message
                    );

                    $result = file_get_contents('http://gateway.api.sc/get/?' . http_build_query($sms));

                    $sms_log->write('(Stream Telecom) SMS send: SMS ID: ' . $result);

                    if ($this->copy) {
                        $numbers = explode(',', $this->copy);
                        foreach ($numbers as $number) {
                            if (strlen($number) < 3) {
                                continue;
                            }

                            $sms['dadr'] = $this->prepPhone($number);

                            $multi_result = file_get_contents('http://gateway.api.sc/get/?' . http_build_query($sms));

                            $sms_log->write('(Stream Telecom) SMS send: SMS ID: ' . $multi_result);

                            sleep(1);
                        }
                    }

                    return $result;
                }
            } else {
                $sms_log->write('(Stream Telecom) Error #:' . $balance_result . 'Check your Balance!');
            }
        } else {
            $sms_log->write('(Stream Telecom) Error: Please enter valid Login or Password!');
        }
    }

    private function prepPhone($phone) {
        $result = preg_replace('/\+?\d+,/', '', $phone);

        return $result;
    }
}