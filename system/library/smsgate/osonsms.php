<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Osonsms extends SmsGate {

    private $baseurl = 'http://api.osonsms.com/';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'OsonSMS';
                $sms_log->write('(Osonsms) Notice: Default sender OsonSMS set!');
            }

            $dlm = ";";

            if ($this->to) {
                $phone = $this->prepPhone($this->to);
            } else {
                $phone = '';
                $sms_log->write('(Osonsms) Error: Phone destination not found!');
            }
            $txn_id = uniqid();

            // $balance_hash = hash('sha256', $txn_id . $dlm . $this->username. $dlm . $sender. $dlm . $phone . $dlm . $this->password);
            $balance_hash = hash('sha256', $txn_id . $dlm . $this->username . $dlm . $this->password);

            $balance_creds = [
                'login'    => $this->username,
                'str_hash' => $balance_hash,
                'txn_id'   => $txn_id
            ];

            $balance_result = $this->getBalance($balance_creds);

            $balance = json_decode($balance_result, true);

            if (!$balance['error'] && isset($balance['msg']['balance'])) {
                $sms_log->write('(Osonsms) : Balance: ' . $balance['msg']['balance']);

                $smsx_id = uniqid();

                $sms_hash = hash('sha256', $smsx_id . $dlm . $this->username . $dlm . $sender . $dlm . $phone . $dlm . $this->password);

                $sms = array(
                    'from'         => $sender,
                    'phone_number' => $phone,
                    'msg'          => $this->message,
                    'login'        => $this->username,
                    'str_hash'     => $sms_hash,
                    'txn_id'       => $smsx_id
                );

                $result = $this->sendSMS($sms);

                if (!$result['error'] && $result['msg']) {
                    $sms_log->write('(Osonsms) : SMS send result: ' . $result['msg']);
                }

                if ($result['error']) {
                    $sms_log->write('(Osonsms) : SMS send error: ' . $result['msg']);
                }

                if ($this->copy) {
                    $numbers = explode(',', $this->copy);
                    foreach ($numbers as $number) {
                        $phone_multi = $this->prepPhone($number);

                        $smsbulkx_id = uniqid();

                        $smsbulk_hash = hash('sha256', $smsbulkx_id . $dlm . $this->username . $dlm . $sender . $dlm . $phone_multi . $dlm . $this->password);

                        $sms_multi = array(
                            'from'         => $sender,
                            'phone_number' => $phone_multi,
                            'msg'          => $this->message,
                            'login'        => $this->username,
                            'str_hash'     => $smsbulk_hash,
                            'txn_id'       => $smsbulkx_id
                        );

                        $bulk_result = $this->sendSMS($sms_multi);

                        if (!$bulk_result['error'] && $bulk_result['msg']) {
                            $sms_log->write('(Osonsms) : SMS Copy send result: ' . $bulk_result['msg']);
                        }

                        if ($bulk_result['error']) {
                            $sms_log->write('(Osonsms) : SMS Copy send error: ' . $bulk_result['msg']);
                        }
                    }
                }
            } else {
                $sms_log->write('(Osonsms) : Authorisation fault or Current Balance is overdraft limit, Sms not send');
            }
        } else {
            $sms_log->write('(Osonsms) Error: Please set correct login/password!');
        }
    }

    public function getBalance($data) {
        $result = $this->sendRequest($this->baseurl . 'check_balance.php', http_build_query($data), 'GET');

        return json_encode($result);
    }

    public function sendSMS($data) {
        $result = $this->sendRequest($this->baseurl . 'sendsms_v1.php', http_build_query($data), 'GET');

        return $result;
    }

    public function checkSMS($data) {
        $result = $this->sendRequest($this->baseurl . 'query_sm.php', http_build_query($data), 'GET');

        return $result;
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function sendRequest($url, $data, $method = 'GET') {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "$url?$data");

        if ($method == "GET") {
            curl_setopt($ch, CURLOPT_URL, "$url?$data");
        } else {
            if ($method == "POST") {
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET'
        ));

        $result = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        $response = array();

        if ($error) {
            $response['error'] = 1;
            $response['msg'] = $error;
        } else {
            $result = json_decode($result);

            if (isset($result->error)) {
                $response['error'] = 1;
                $response['msg'] = "Error Code: " . $result->error->code . " Message: " . $result->error->msg;
            } else {
                $response['error'] = 0;
                $response['msg'] = $result;
            }
        }

        return $response;
    }
}