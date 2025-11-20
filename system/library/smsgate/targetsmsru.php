<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email   support@ocdev.pro
 * @license Commercial
 */

final class Targetsmsru extends SmsGate {

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            $credentials = array(
                'security' => array(
                    'login'    => $this->username,
                    'password' => $this->password,
                ),
                'type'     => 'balance',
            );

            $balance_result = $this->sendSms($credentials);

            if ($balance_result) {
                $balance = $balance_result['money']['value'];
                $sms_log->write('(Targetsms): Balance: ' . print_r($balance_result['money'], true));
            } else {
                $balance = 0;
            }

            if ($balance > 0) {
                $phones = explode(",", $this->to);
                $abonent = array();
                $number_sms = 0;
                $telephones = array();

                foreach ($phones as $key => $phone) {
                    $phone = $this->validatePhone($phone);

                    if ($phone && !in_array($phone, $telephones)) {
                        $number_sms++;
                        $telephones[] = $phone;
                        $abonent[] = array(
                            'phone'      => $phone,
                            'number_sms' => $number_sms,
                        );
                    } else {
                        $sms_log->write('(Targetsms): Phone number is not valid!');
                    }
                }

                if ($abonent) {
                    $param = array(
                        'security' => array(
                            'login'    => $this->username,
                            'password' => $this->password,
                        ),
                        'type'     => 'sms',
                        'message'  => array(
                            array(
                                'type'          => 'sms',
                                'name_delivery' => $_SERVER['HTTP_HOST'],
                                'sender'        => $this->from,
                                'text'          => $this->message,
                                'abonent'       => $abonent,
                            ),
                        ),
                    );

                    $result = $this->sendSms($param);

                    $sms_log->write('(Targetsms): ' . print_r($result, true));
                }
            } else {
                $sms_log->write('(Targetsms) Error: Check your Balance!');
            }
        } else {
            $sms_log->write('(Targetsms) Error: Please enter valid Login or Password!');
        }
    }

    private function validatePhone($phone) {
        $phone = preg_replace('/\+?\d+,/', '', $phone);
        $first_3 = substr($phone, 0, 3);
        $first_1 = substr($phone, 0, 1);

        $error = 0;

        if (($first_3 == '380') || ($first_3 == '375')) {
            $last = substr($phone, 3);
            if (strlen($last) == 9) {
                $phone = "+" . $phone;
            } else {
                $error = 1;
            }
        } elseif ($first_1 == '7') {
            $last = substr($phone, 1);
            if (strlen($last) == 10) {
                $phone = "+" . $phone;
            } else {
                $error = 1;
            }
        } elseif ($first_1 == '8') {
            $last = substr($phone, 1);
            if (strlen($last) == 10) {
                $phone = "+7" . $last;
            } else {
                $error = 1;
            }
        } else {
            $error = 1;
        }

        if (!$error) {
            return $phone;
        } else {
            return false;
        }
    }

    private function sendSms($data) {
        $param_json = json_encode($data, true);
        $href = 'https://sms.targetsms.ru/sendsmsjson.php';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'charset=utf8', 'Expect:'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param_json);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_URL, $href);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $res = curl_exec($ch);
        $result = json_decode($res, true);
        curl_close($ch);

        return $result;
    }
}