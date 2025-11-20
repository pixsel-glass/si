<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email   support@ocdev.pro
 * @license Commercial
 */

final class Smsuslugi extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $credentials = Array(
                'login'    => $this->username,
                'password' => $this->password,
            );

            $auth = file_get_contents("https://lcab.sms-uslugi.ru/lcabApi/info.php?" . http_build_query($credentials));

            $auth_result = json_decode($auth);

            $sms_log->write('(SMS-uslugi.ru) Authentication: Status = ' . $auth_result->descr);

            if ($auth_result->code == '1') {
                $balance = file_get_contents("https://lcab.sms-uslugi.ru/lcabApi/info.php?" . http_build_query($credentials));

                $balance_result = json_decode($balance);

                $sms_log->write('(SMS-uslugi.ru) Balance: Code = ' . $balance_result->code . ' Balance: ' . $balance_result->account);

                if ($this->to) {
                    $numbers = $this->prepPhone($this->to);
                } else {
                    $sms_log->write('(SMS-uslugi.ru) Error: Phone destination not found!');
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
                    $sms_log->write('(SMS-uslugi.ru) Notice: Default Sender set! Please input real Sender');
                }

                if ($balance_result->account && $numbers) {
                    $sms = Array(
                        'login'    => $this->username,
                        'password' => $this->password,
                        'to'       => $numbers,
                        'from'     => $sender,
                        'txt'      => $this->message,
                        'check'    => '0',
                    );

                    $result = file_get_contents("https://lcab.sms-uslugi.ru/lcabApi/sendSms.php?" . http_build_query($sms));

                    $send_result = json_decode($result);

                    if ($send_result->code == '1') {
                        $sms_log->write('(SMS-uslugi.ru) SMS send - Count: ' . $send_result->colsmsOfSending . ' Cost: ' . $send_result->priceOfSending);
                    }

                    return $result;
                }
            } else {
                $sms_log->write('(SMS-uslugi.ru) Error: SMS-uslugi.ru Authentication failed!' . ' Code: ' . $auth_result->code . ' - ' . $auth_result->descr);
            }
        } else {
            $sms_log->write('(SMS-uslugi.ru) Error: Please enter valid api_id in login(username) field!');
        }
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }
}