<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Epochtasms extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            if ($this->to) {
                $phone = $this->prepPhone($this->to);
            } else {
                $phone = false;
                $sms_log->write('(Epochta) Error: Phone destination not found!');
            }

            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = '';
                $sms_log->write('(Epochta) Error: Please input Sender');
            }

            if ($phone) {
                $sms = Array(
                    'user' => $this->username,
                    'pwd'  => $this->password,
                    'sadr' => $sender,
                    'dadr' => $phone,
                    'text' => $this->message,
                );
            }

            $result = $this->sendSms($sms);

            $sms_log->write("(Epochta) SMS" . print_r($result, true));

            if ($this->copy) {
                $numbers = explode(',', $this->copy);
                foreach ($numbers as $number) {
                    if (strlen($number) < 3) {
                        continue;
                    }

                    $sms['dadr'] = $this->prepPhone($number);

                    $multi_result = $this->sendSms($sms);

                    $sms_log->write("(Epochta) SMS" . print_r($multi_result, true));

                    sleep(1);
                }
            }

            return $result;
        } else {
            $sms_log->write('(Epochta) Error: Please enter valid Login or Password!');
        }
    }

    private function sendSms($data = array()) {
        $xml_send = '<?xml version="1.0" encoding="UTF-8"?>
			<SMS>
			  <operations>
			    <operation>SEND</operation>
			  </operations>
			  <authentification>
			    <username>' . $data['user'] . '</username>
			    <password>' . $data['pwd'] . '</password>
			  </authentification>
			  <message>
			    <sender>' . $data['sadr'] . '</sender>
			    <text>' . $data['text'] . '</text>
			  </message>
			  <numbers>
			    <number messageID="msg11">' . $data['dadr'] . '</number>
			  </numbers>
			</SMS>';

        $curl = curl_init();

        $curl_data = array(
            CURLOPT_URL            => 'http://api.myatompark.com/members/sms/xml.php',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_POST           => true,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT        => 100,
            CURLOPT_POSTFIELDS     => array('XML' => $xml_send),
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