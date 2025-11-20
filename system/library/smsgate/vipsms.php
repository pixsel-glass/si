<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Vipsms extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');
        try {
            $client = new SoapClient('http://vipsms.net/api/soap.html');

            $auth = $client->auth($this->username, $this->password);

            if ($auth->code != '0') {
                $sms_log->write('(VIPSMS) Auth Error: ' . $this->explainProblem($auth));
            }

            $sessionId = false;

            if ($auth->code == '0') {
                $sessionId = $auth->message;
            }

            $balance_status = false;

            if ($sessionId) {
                $balance = $client->getBalance($sessionId);

                if ($balance->code != '0') {
                    $sms_log->write('(VIPSMS) Balance Error: ' . $this->explainProblem($balance));
                }

                if ($balance->code == '0') {
                    $sms_log->write('(VIPSMS) Balance: ' . $balance->message);

                    if ($this->to) {
                        $phone = $this->prepPhone($this->to);
                    } else {
                        $sms_log->write('(VIPSMS) Error: VIPSMS Phone destination not found!');
                        $phone = false;
                    }
                    if ($this->from) {
                        $sender = $this->from;
                    } else {
                        $sender = '';
                        $sms_log->write('(VIPSMS) Notice: Default Sender set! Please input real Sender');
                    }

                    if ($phone) {
                        $send = $client->sendSmsOne($sessionId, $phone, $sender, $this->message);

                        if ($send->code != '0') {
                            $sms_log->write('(VIPSMS) Send Error: ' . $this->explainProblem($send));
                        }

                        if ($send->code == '0') {
                            $sms_log->write('(VIPSMS) Send, messageId: ' . $send->message);
                        }
                    }

                    if ($this->copy) {
                        $numbers = explode(',', $this->copy);

                        $phones = array();

                        foreach ($numbers as $number) {
                            $phones[] = $this->prepPhone($number);
                        }

                        $phones = implode(',', $phones);

                        $send_copy = $client->sendSmsOne($sessionId, $phones, $sender, $this->message);

                        if ($send_copy->code != '0') {
                            $sms_log->write('(VIPSMS) Send Error: ' . $this->explainProblem($send_copy));
                        }

                        if ($send_copy->code == '0') {
                            $sms_log->write('(VIPSMS) Send, messageId: ' . $send_copy->message);
                        }
                    }
                }
            }
        } catch (SoapFault $fault) {
            $sms_log->write("Ошибка SOAP: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    private function prepPhone($phone) {
        $result = '+' . preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    private function explainProblem($soap_res) {
        $result = 'code: ' . $soap_res->code . '; message: ' . $soap_res->message;

        if ($soap_res->extend && is_array($soap_res->extend)) {
            $result .= '; explain: ' . var_export($soap_res->extend, true);
        }

        return $result;
    }
}