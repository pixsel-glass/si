<?php
//  @copyright  OC-HELP.com
//  @website    https://oc-help.com
//  @support    support@oc-help.com
//  @developer  Alexander Vakhovskiy

final class Smssending extends SmsGate {

    private $baseurl = 'http://lcab.sms-sending.ru/API/XML/';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'TEST';
                $sms_log->write('(Smssending) Notice: Default Sender set! Please input real Sender');
            }

            $balance = $this->getBalance($this->username, $this->password);

            $balance_result = $this->decodeResult($balance);

            if ($balance_result->code == '1' && (int)$balance_result->account > '1') {
                $sms_log->write('(Smssending) : Balance: ' . $balance_result->account);

                if ($this->to) {
                    $phone = $this->prepPhone($this->to);

                    $xml = "<?xml version='1.0' encoding='UTF-8'?>";
                    $xml .= "<data>";
                    $xml .= "<login>" . $this->username . "</login>";
                    $xml .= "<password>" . $this->password . "</password>";
                    $xml .= "<source>" . $sender . "</source>";
                    $xml .= "<text>" . $this->message . "</text>";
                    $xml .= "<to number='" . $phone . "'></to>";
                    $xml .= "</data>";

                    $response = $this->sendSMS($xml);

                    $sms_result = $this->decodeResult($response);

                    $result_code = $this->getResultCode($sms_result->code);
                    $sms_log->write('(Smssending) : Sms send result: ' . $result_code);

                    if ($sms_result->code == '1') {
                        $sms_log->write('(Smssending) : Sms send desc: ' . $sms_result->descr . ' Sms Count: ' . $sms_result->colsmsOfSending . ' Cost: ' . $sms_result->priceOfSending);

                        $status = $this->getStatus($this->username, $this->password, $sms_result->smsid);

                        $status_result = $this->decodeResult($status);

                        if ($status_result->code) {
                            $this->writeSmsResult($status_result);
                        } else {
                            $sms_log->write('(Smssending) : SmsSend Status: ID: ' . $status_result->descr);
                        }
                    }
                } else {
                    $sms_log->write('(Smssending) Error: Phone destination not found!');
                }
                
                if ($this->copy) {
                    $numbers = explode(',', $this->copy);

                    $xml_multi = "<?xml version='1.0' encoding='UTF-8'?>";
                    $xml_multi .= "<data>";
                    $xml_multi .= "<login>" . $this->username . "</login>";
                    $xml_multi .= "<password>" . $this->password . "</password>";
                    $xml_multi .= "<source>" . $sender . "</source>";
                    $xml_multi .= "<text>" . $this->message . "</text>";
                    foreach ($numbers as $number) {
                        $phone_multi = $this->prepPhone($number);
                        $xml_multi .= "<to number='" . $phone_multi . "'></to>";
                    }
                    $xml_multi .= "</data>";

                    $response_multi = $this->sendSMS($xml_multi);

                    $sms_result_multi = $this->decodeResult($response_multi);

                    if ($sms_result_multi->code) {
                        $sms_log->write('(Smssending) : Sms send desc: ' . $sms_result_multi->descr . 'Sms Count: ' . $sms_result_multi->colsmsOfSending . ' Cost: ' . $sms_result_multi->priceOfSending);

                        $status_multi = $this->getStatus($this->username, $this->password, $sms_result_multi->smsid);

                        $status_result_multi = $this->decodeResult($status_multi);

                        if ($status_result_multi->code) {
                            $this->writeSmsResult($status_result_multi);
                        } else {
                            $sms_log->write('(Smssending) : SmsSend Status: ID: ' . $status_result_multi->descr);
                        }
                    }
                }
            } else {
                $result_code = $this->getResultCode($sms_result->code);
                $sms_log->write('(Smssending) : Sms send result: ' . $result_code);
                $sms_log->write('(Smssending) : Current Balance is LOW - ' . $balance_result->account . ' Sms not send or Authorisation fault: ');
            }
        } else {
            $sms_log->write('(Smssending) Error: Please set correct login/password!');
        }
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function sendSms($xml) {
        $result = $this->sendRequest($this->baseurl . 'send.php', $xml);

        return $result;
    }

    public function getStatus($login, $password, $id) {
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= "<data>";
        $xml .= "<login>" . $login . "</login>";
        $xml .= "<password>" . $password . "</password>";
        $xml .= "<smsid >" . $id . "</smsid>";
        $xml .= "</data>";

        $result = $this->sendRequest($this->baseurl . 'report.php', $xml);

        return $result;
    }

    public function getBalance($login, $password) {
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= "<data>";
        $xml .= "<login>" . $login . "</login>";
        $xml .= "<password>" . $password . "</password>";
        $xml .= "</data>";

        $result = $this->sendRequest($this->baseurl . 'balance.php', $xml);

        return $result;
    }

    private function writeSmsResult($data) {
        $sms_log = new Log('sms_log.log');

        if ($data->detail->delivered && isset($data->detail->delivered->number)) {
            $sms_log->write('(Smssending) : SmsSend Delivered For: ' . $data->detail->delivered->number);
        }
        if ($data->detail->notDelivered && isset($data->detail->notDelivered->number)) {
            $sms_log->write('(Smssending) : SmsSend notDelivered For: ' . $data->detail->notDelivered->number);
        }
        if ($data->detail->waiting && isset($data->detail->waiting->number)) {
            $sms_log->write('(Smssending) : SmsSend Waiting For: ' . $data->detail->waiting->number);
        }
        if ($data->detail->enqueued && isset($data->detail->enqueued->number)) {
            $sms_log->write('(Smssending) : SmsSend Enqueued For: ' . $data->detail->enqueued->number);
        }
        if ($data->detail->cancel && isset($data->detail->cancel->number)) {
            $sms_log->write('(Smssending) : SmsSend Cancel For: ' . $data->detail->cancel->number);
        }
        if ($data->detail->onModer && isset($data->detail->onModer->number)) {
            $sms_log->write('(Smssending) : SmsSend Enqueued For: ' . $data->detail->onModer->number);
        }
    }

    private function getResultCode($data) {
        $codes = array(
            '1'   => 'Успешно завершенная операция',
            '500' => 'Недостаточно переданных параметров',
            '501' => 'Неверная пара логин/пароль',
            '502' => 'Превышен размер smsid. Максимальный размер 21 символ',
            '503' => 'Неверный формат datetime. Верный: yyyy-mm-dd HH:MM:SS',
            '504' => 'Недопустимое значение Адреса отправителя',
            '520' => 'Получатели смс отсутствуют',
            '70'  => 'Ошибка парсера XML документа (х – цифры 0..9)'
        );

        $code_error = substr($data, '0', '2');

        if ($code_error == '70') {
            $data = '70';
        }

        return $codes[$data];
    }

    private function decodeResult($data) {
        $json = json_encode(simplexml_load_string($data));
        $result = json_decode($json);

        return $result;
    }

    private function sendRequest($url, $xml) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}