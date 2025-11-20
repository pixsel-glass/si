<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Smspilot extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username) {
            if ($this->to) {
                $phone = $this->prepPhone($this->to);
            } else {
                $phone = false;
                $sms_log->write('(Smspilot.ru) Error: Phone destination not found!');
            }

            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = '';
                $sms_log->write('(Smspilot.ru) Error: Please input Sender');
            }

            if ($phone) {
                $sms = array(
                    'send'   => $this->message,
                    'to'     => $phone,
                    'from'   => $sender,
                    'apikey' => $this->username,
                    'format' => 'json',
                );

                $result = $this->sendSms($sms);

                $send_result = json_decode($result);

                if (!isset($send_result->error)) {
                    $sms_log->write('Smspilot.ru - SMS успешно отправлена server_id=' . $send_result->send[0]->server_id);
                } else {
                    $sms_log->write('Smspilot.ru Error: ' . $send_result->description_ru);
                }

                if ($this->copy) {
                    $numbers = explode(',', $this->copy);

                    foreach ($numbers as $number) {
                        $sms['to'] = $this->prepPhone($number);
                        $multi_result = $this->sendSms($sms);

                        $send_multi = json_decode($multi_result);

                        if (!isset($send_multi->error)) {
                            $sms_log->write('Smspilot.ru - SMS успешно отправлена server_id=' . $send_multi->send[0]->server_id);
                        } else {
                            $sms_log->write('Smspilot.ru Error: ' . $send_multi->description_ru);
                        }

                        sleep(1);
                    }
                }

                return $result;
            }
        } else {
            $sms_log->write('(Smspilot.ru) Error: Please enter valid api_id in login(username) field!');
        }
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function sendSms($send_data) {
        $url = 'https://smspilot.ru/api.php?' . http_build_query($send_data);

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