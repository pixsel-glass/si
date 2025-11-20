<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Unisender extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username) {
            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = '';
                $sms_log->write('(Unisender) Notice: Default Sender set! Please input real Sender');
            }

            if ($this->to) {
                $numbers = $this->prepPhone($this->to);
            } else {
                $sms_log->write('(Unisender) Error: Phone destination not found!');
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

            if ($numbers) {
                $sms = Array(
                    'format'  => 'json',
                    'api_key' => $this->username,
                    'phone'   => $numbers,
                    'sender'  => $sender,
                    'text'    => $this->message,
                );

                $smsSend = $this->sendSms("https://api.unisender.com/ru/api/sendSms?" . http_build_query($sms));

                $sms_log->write(print_r($smsSend, true));

                if ($smsSend) {
                    $smsSend = json_decode($smsSend);

                    if (null === $smsSend) {
                        $sms_log->write('(Unisender) Send error: API-server return wrong JSON');
                    } elseif (!empty($smsSend->error)) {
                        $sms_log->write('(Unisender) Send error: ' . $smsSend->error . ' (code: ' . $smsSend->code . ')');
                    } else {
                        $sms_log->write('(Unisender) Sms send complete: Message_id: ' . $smsSend->result->sms_id . ' Sms cost: ' . $smsSend->result->price . ' ' . $smsSend->result->currency);
                    }
                } else {
                    $sms_log->write('(Unisender) Send error: API-server is not responding');
                }

                return true;
            }
        } else {
            $sms_log->write('(Unisender) Error: Please enter valid api_id in login(username) field!');
        }
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

        return $data;
    }
}