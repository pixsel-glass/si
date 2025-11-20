<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Turbosms extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        try {
            $client = new SoapClient('http://turbosms.in.ua/api/wsdl.html');

            $credentials = Array(
                'login'    => $this->username,
                'password' => $this->password,
            );

            $auth = $client->Auth($credentials);

            if ($auth) {
                $sms_log->write('(TurboSMS) Auth: ' . $auth->AuthResult);

                $balance = $client->GetCreditBalance();

                $sms_log->write('(TurboSMS) Balance: ' . $balance->GetCreditBalanceResult);

                $balance_result = round($balance->GetCreditBalanceResult);

                if ($balance_result > '0') {
                    if ($this->from) {
                        $sender = $this->from;
                    } else {
                        $sender = 'Market';
                        $sms_log->write('(TurboSMS) Notice: Default Sender set! Please input real Sender');
                    }

                    if ($this->to) {
                        $phone = $this->prepPhone($this->to);
                    } else {
                        $phone = false;
                        $sms_log->write('(TurboSMS) Error: Phone destination not found!');
                    }

                    if ($phone) {
                        $sms = Array(
                            'sender'      => $sender,
                            'destination' => $phone,
                            'text'        => $this->message,
                        );

                        $result = $client->SendSMS($sms);

                        if ($result) {
                            $sms_log->write('(TurboSMS) ' . $result->SendSMSResult->ResultArray[0]);
                        }
                    }

                    if ($this->copy) {
                        $numbers = explode(',', $this->copy);
                        $phones = array();

                        foreach ($numbers as $number) {
                            $phones[] = $this->prepPhone($number);
                        }

                        $phones = implode(',', $phones);

                        $sms_bulk = Array(
                            'sender'      => $sender,
                            'destination' => $phones,
                            'text'        => $this->message,
                        );

                        $result_bulk = $client->SendSMS($sms_bulk);

                        if ($result_bulk) {
                            $sms_log->write('(TurboSMS) ' . $result_bulk->SendSMSResult->ResultArray[0]);
                        }
                    }
                } else {
                    $sms_log->write('(TurboSMS) Error: TurboSMS Balance is: ' . $balance->GetCreditBalanceResult);
                }
            } else {
                $sms_log->write('(TurboSMS) Error: TurboSMS Authentication failed!');
            }
        } catch (SoapFault $fault) {
            $sms_log->write("Ошибка SOAP: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }

    private function prepPhone($phone) {
        $result = preg_replace('/\+?\d+,/', '', $phone);

        return $result;
    }
}