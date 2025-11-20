<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Smsfly extends SmsGate {

    public function send() {
        $start_time = "AUTO";
        $end_time = "AUTO";
        $rate = 1;
        $lifetime = 4;

        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $balance = $this->getBalance($this->username, $this->password);
            if ($balance) {
                $sms_log->write("(SMSfly) Balance: " . print_r($balance, true));

                if ($this->to) {
                    $phone = $this->prepPhone($this->to);
                } else {
                    $phone = false;
                    $sms_log->write('(SMSfly) Error: Phone destination not found!');
                }

                if ($this->from) {
                    $sender = $this->from;
                } else {
                    $sender = '';
                    $sms_log->write('(SMSfly) Error: Please input Sender');
                }

                if ($phone) {
                    $myXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
                    $myXML .= "<request>";
                    $myXML .= "<operation>SENDSMS</operation>";
                    $myXML .= '<message start_time="' . $start_time . '" end_time="' . $end_time . '" lifetime="' . $lifetime . '" rate="' . $rate . '" desc="' . $sender . '" source="' . $sender . '">';
                    $myXML .= "<body>" . $this->message . "</body>";
                    $myXML .= "<recipient>" . $phone . "</recipient>";
                    $myXML .= "</message>";
                    $myXML .= "</request>";

                    $sms = $this->sendSms($this->username, $this->password, $myXML);

                    $sms_log->write("(SMSfly) SMS: " . print_r($sms, true));
                }

                if ($this->copy) {
                    $numbers = explode(',', $this->copy);

                    $myXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
                    $myXML .= "<request>";
                    $myXML .= "<operation>SENDSMS</operation>";
                    $myXML .= '<message start_time="' . $start_time . '" end_time="' . $end_time . '" lifetime="' . $lifetime . '" rate="' . $rate . '" desc="' . $sender . '" source="' . $sender . '">';
                    $myXML .= "<body>" . $this->message . "</body>";
                    foreach ($numbers as $number) {
                        $myXML .= "<recipient>" . $this->prepPhone($number) . "</recipient>";
                    }
                    $myXML .= "</message>";
                    $myXML .= "</request>";

                    $multi_result = $this->sendSms($this->username, $this->password, $myXML);

                    $sms_log->write("(SMSfly) SMS copy: " . print_r($multi_result, true));
                }
            } else {
                $sms_log->write("(SMSfly) Error: Невозможно получить баланс!");
            }
        } else {
            $sms_log->write('(SMSfly) Error: Please enter valid Login or Password!');
        }
    }

    private function getBalance($login, $password) {
        $myXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $myXML .= "<request>";
        $myXML .= "<operation>GETBALANCE</operation>";
        $myXML .= "</request>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, $login . ':' . $password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, 'http://sms-fly.com/api/api.php');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Accept: text/xml"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myXML);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function sendSms($login, $password, $myXML) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, $login . ':' . $password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, 'http://sms-fly.com/api/api.php');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Accept: text/xml"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myXML);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function prepPhone($phone) {
        $result = preg_replace('/\+?\d+,/', '', $phone);

        return $result;
    }
}