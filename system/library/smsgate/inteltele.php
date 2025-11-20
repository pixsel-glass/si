<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Inteltele extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $credentials = Array(
                'username' => $this->username,
                'api_key'  => $this->password,
            );

            $balance = $this->sendSms("http://api.sms.intel-tele.com/credit/?" . http_build_query($credentials));

            $balance_credit = json_decode($balance);

            $balance_result = preg_replace('/[^0-9.]/', '', $balance_credit->credit);

            $sms_log->write('(Sms.intel-tele.com) Balance: ' . $balance_result);

            if ($balance_result !== 0) {
                if ($this->to) {
                    $numbers = $this->prepPhone($this->to);
                } else {
                    $sms_log->write('(Sms.intel-tele.com) Error: Phone destination not found!');
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
                    $sender = '';
                    $sms_log->write('(Sms.intel-tele.com) Notice: Default Sender set! Please input real Sender');
                }

                if ($numbers) {
                    $sms = Array(
                        'username' => $this->username,
                        'api_key'  => $this->password,
                        'to'       => $numbers,
                        'from'     => $sender,
                        'message'  => $this->message
                    );

                    $result = $this->sendSms("http://api.sms.intel-tele.com/message/send/?" . http_build_query($sms));

                    $send_result = json_decode($result);

                    if ($send_result->reply->status == 'OK') {
                        $sms_log->write('(Sms.intel-tele.com) SMS send - ' . $send_result->reply);
                    }

                    return $result;
                }
            } else {
                $sms_log->write('(Sms.intel-tele.com) Error Balance null!' . $balance);
            }
        } else {
            $sms_log->write('(Sms.intel-tele.com) Error: Please enter valid api_id in login(username) field!');
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