<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Smscab extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        try {
            $client = new SoapClient('http://my.smscab.ru/sys/soap.php?wsdl');

            if ($this->username && $this->password) {
                $credentials = Array(
                    'login' => $this->username,
                    'psw'   => $this->password,
                );

                $balance = $client->get_balance($credentials);

                $sms_log->write('(SMSCab) Balance: ' . $balance->balanceresult->balance . ' Error: ' . $balance->balanceresult->error);

                if ($this->to) {
                    $numbers = $this->prepPhone($this->to);
                } else {
                    $sms_log->write('(SMSCab) Error: Phone destination not found!');
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
                    $sms_log->write('(SMSCab) Notice: Default Sender set! Please input real Sender');
                }

                if ($balance->balanceresult->balance && $numbers) {
                    $sms = Array(
                        'login'  => $this->username,
                        'psw'    => $this->password,
                        'phones' => $numbers,
                        'mes'    => $this->message,
                        'sender' => $sender,
                        'time'   => 0,
                    );

                    $result = $client->send_sms($sms);

                    if ($result->sendresult->cnt) {
                        $sms_log->write('(SMSCab) SMS send: ' . $result->sendresult->cnt . ' Cost: ' . $result->sendresult->cost);
                    }

                    return $result;
                }
            } else {
                $sms_log->write('(SMSCab) Error: SMSCab Authentication failed!');
            }
        } catch (SoapFault $fault) {
            $sms_log->write("Ошибка SOAP: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }
}