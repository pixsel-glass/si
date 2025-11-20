<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Smsassistentby extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $credentials = Array(
                'user'     => $this->username,
                'password' => $this->password
            );

            $balance = file_get_contents('https://userarea.sms-assistent.by/api/v1/credits/plain?' . http_build_query($credentials));

            $sms_log->write('(Sms-assistent) Balance: ' . $balance);

            if ($balance > 0 && $balance !== '0.00') {
                if ($this->to) {
                    $phone = $this->prepPhone($this->to);
                } else {
                    $phone = false;
                    $sms_log->write('(Sms-assistent) Error: Phone destination not found!');
                }

                if ($this->from) {
                    $sender = $this->from;
                } else {
                    $sender = '';
                    $sms_log->write('(Sms-assistent) Error: Please input Sender');
                }

                if ($balance && $phone) {
                    $sms = Array(
                        'user'      => $this->username,
                        'password'  => $this->password,
                        'sender'    => $sender,
                        'recipient' => $phone,
                        'message'   => $this->message
                    );

                    $result = $this->sendSms($sms);

                    $status = Array(
                        'user'     => $this->username,
                        'password' => $this->password,
                        'id'       => $result
                    );

                    $sms_status = file_get_contents('https://userarea.sms-assistent.by/api/v1/statuses/plain?' . http_build_query($status));

                    $sms_log->write('(Sms-assistent) SMS send - ID: ' . $result . 'Status: ' . $sms_status);

                    if ($this->copy) {
                        $numbers = explode(',', $this->copy);
                        foreach ($numbers as $number) {
                            if (strlen($number) < 3) {
                                continue;
                            }
                            $sms['recipient'] = $this->prepPhone($number);

                            $multi_result = $this->sendSms($sms);

                            $status_multi = Array(
                                'user'     => $this->username,
                                'password' => $this->password,
                                'id'       => $multi_result
                            );

                            $sms_multi = file_get_contents('https://userarea.sms-assistent.by/api/v1/statuses/plain?' . http_build_query($status_multi));

                            $sms_log->write('(Sms-assistent) SMS send - ID: ' . $multi_result . 'Status: ' . $sms_multi);

                            sleep(1);
                        }
                    }

                    return $result;
                }
            } else {
                $sms_log->write('(Sms-assistent) Error #: Check your Balance!');
            }
        } else {
            $sms_log->write('(Sms-assistent) Error: Please enter valid Username or Password!');
        }
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function sendSms($data) {
        $result = file_get_contents("https://userarea.sms-assistent.by/api/v1/send_sms/plain?" . http_build_query($data));

        return $result;
    }
}