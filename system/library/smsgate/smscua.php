<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Smscua extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');
        try {
            $client = new SoapClient('https://smsc.ua/sys/soap.php?wsdl');

            if ($this->username && $this->password) {
                $credentials = Array(
                    'login' => $this->username,
                    'psw'   => $this->password,
                );

                $balance = $client->get_balance($credentials);

                if (!isset($result->sendresult->error) && $balance->balanceresult->balance) {
                    $sms_log->write('(SMSC.ua) Balance: ' . $balance->balanceresult->balance . ' Error: ' . $balance->balanceresult->error);

                    if ($this->from) {
                        $sender = $this->from;
                    } else {
                        $sender = '';
                        $sms_log->write('(SMSC.ua) Notice: Default Sender set! Please input real Sender');
                    }

                    if ($this->to) {
                        $phone = $this->prepPhone($this->to);
                    } else {
                        $phone = false;
                        $sms_log->write('(SMSC.ua) Error: Phone destination not found!');
                    }

                    if ($phone) {
                        $sms = Array(
                            'login'  => $this->username,
                            'psw'    => $this->password,
                            'phones' => $phone,
                            'mes'    => $this->message,
                            'sender' => $sender,
                            'time'   => 0,
                        );

                        $result = $client->send_sms($sms);

                        if (!$result->sendresult->error && $result->sendresult->cnt) {
                            $sms_log->write('(SMSC.ua) SMS send: ' . $result->sendresult->cnt . ' Cost: ' . $result->sendresult->cost);
                        } else {
                            $sms_log->write('(SMSC.ua) SMS Error: ' . $result->sendresult->error);
                        }
                    }

                    if ($this->copy) {
                        $numbers = explode(',', $this->copy);

                        $phones = array();

                        foreach ($numbers as $number) {
                            $phones[] = $this->prepPhone($number);
                        }

                        $phones = implode(',', $phones);

                        $sms = Array(
                            'login'  => $this->username,
                            'psw'    => $this->password,
                            'phones' => $phones,
                            'mes'    => $this->message,
                            'sender' => $sender,
                            'time'   => 0,
                        );

                        $result = $client->send_sms($sms);

                        if (!$result->sendresult->error && $result->sendresult->cnt) {
                            $sms_log->write('(SMSC.ua) SMS send: ' . $result->sendresult->cnt . ' Cost: ' . $result->sendresult->cost);
                        } else {
                            $sms_log->write('(SMSC.ua) SMS Error: ' . $result->sendresult->error);
                        }
                    }
                } else {
                    $sms_log->write('(SMSC.ua) Balance: ' . $balance->balanceresult->balance . ' Error: ' . $balance->balanceresult->error);
                }
            } else {
                $sms_log->write('(SMSC.ua) Error: SMSC.ua Authentication failed!');
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