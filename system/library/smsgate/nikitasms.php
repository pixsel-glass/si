<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Nikitasms extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            if ($this->to) {
                $numbers = $this->prepPhone($this->to);
            } else {
                $sms_log->write('(Nikita SMS) Error: Phone destination not found!');
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

            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'SMSPRO.KG';
                $sms_log->write('(Nikita SMS) Notice: Default Sender set! Please input real Sender');
            }

            if ($numbers) {
                $sms = array(
                    'login'  => $this->username,
                    'pwd'    => $this->password,
                    'id'     => token(8),
                    'sender' => $sender,
                    'phones' => $numbers,
                    'text'   => $this->message,
                    'test'   => '1',
                );
            }

            $result = $this->sendSms($sms);

            $result = simplexml_load_string($result);

            $sms_log->write('(Nikita SMS) ' . 'ID: ' . $result->id . ' ' . 'Status: ' . $result->status . ' ' . 'Phones: ' . $result->phones . ' ' . 'CountSMS: ' . $result->smscnt);
        } else {
            $sms_log->write('(Nikita) Error: Please enter valid Login or Password!');
        }
    }

    private function sendSms($data = array()) {
        $xml_send = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml_send .= '<message>';
        $xml_send .= '<login>' . $data['login'] . '</login>';
        $xml_send .= '<pwd>' . $data['pwd'] . '</pwd>';
        $xml_send .= '<id>' . $data['id'] . '</id>';
        $xml_send .= '<sender>' . $data['sender'] . '</sender>';
        $xml_send .= '<text>' . $data['text'] . '</text>';

        $xml_send .= '<phones>';

        $phones = explode(',', $data['phones']);

        foreach ($phones as $phone) {
            if ($phone) {
                $xml_send .= '<phone>' . $phone . '</phone>';
            }
        }

        $xml_send .= '</phones></message>';

        $curl = curl_init();

        $curl_data = array(
            CURLOPT_URL            => 'https://smspro.nikita.kg/api/message',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_POST           => true,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT        => 100,
            CURLOPT_POSTFIELDS     => $xml_send,
        );

        curl_setopt_array($curl, $curl_data);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    private function prepPhone($phone) {
        $result = preg_replace('/\+?\d+,/', '', $phone);

        return $result;
    }
}