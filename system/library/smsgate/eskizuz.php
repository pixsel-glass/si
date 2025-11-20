<?php

/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Eskizuz extends SmsGate
{

    private $baseurl = 'notify.eskiz.uz/api/';

    public function send()
    {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username && $this->password) {
            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'Eskiz';
                $sms_log->write('(Eskiz.uz) Notice: Default sender Eskiz set!');
            }

            if ($this->to) {
                $phone = $this->prepPhone($this->to);
            } else {
                $phone = '';
                $sms_log->write('(Eskiz.uz) Error: Phone destination not found!');
            }

            $credentials = [
                'email'    => $this->username,
                'password' => $this->password,
            ];

            $token = $this->getToken($credentials);

            if (!$token) {
                $sms_log->write('(Eskiz.uz) Auth Error: Token is not recieved!');
            }

            if ($token) {
                $user = $this->getUser($token);

                if (isset($user['message'])) {
                    $sms_log->write('(Eskiz.uz) Auth: ' . $user['message']);
                }

                if (isset($user['data']['balance']) && $user['data']['balance'] > 0) {
                    $sms_log->write('(Eskiz.uz) User Balance: ' . $user['data']['balance']);

                    $sms = [
                        'from' => $sender,
                        'phone' => $phone,
                        'text' => $this->message,
                    ];

                    $result = $this->sendSMS($token, $sms);

                    if ($result['status'] !== 'error' && $result['message']) {
                        $sms_log->write('(Eskiz.uz) SMS send: Id: ' . $result['id'] . '; Response: ' . $result['message']);

                        if ($result['id']) {
                            $status = $this->statusSMS($token, $result['id']);
                            $sms_log->write('(Eskiz.uz) SMS send result: Status: ' . $status['status'] . '; Response: ' . print_r($status['message'], true));
                        }
                    }

                    if ($result['status'] == 'error' && $result['message']) {
                        $sms_log->write('(Eskiz.uz) SMS send error: ' . print_r($result['message'], true));
                    }

                    if ($this->copy) {
                        $numbers = explode(',', $this->copy);
                        foreach ($numbers as $number) {
                            $phone_multi = $this->prepPhone($number);

                            $sms_multi = [
                                'from' => $sender,
                                'phone' => $phone_multi,
                                'text' => $this->message,
                            ];

                            $bulk_result = $this->sendSMS($token, $sms_multi);

                            if ($bulk_result['status'] !== 'error' && $bulk_result['message']) {
                                $sms_log->write('(Eskiz.uz) SMS send: Id: ' . $bulk_result['id'] . '; Response: ' . print_r($bulk_result['message'], true));

                                if ($bulk_result['id']) {
                                    $bulk_status = $this->statusSMS($token, $bulk_result['id']);
                                    $sms_log->write('(Eskiz.uz) SMS send result: Status: ' . $bulk_status['status'] . '; Response: ' . print_r($bulk_status['message'], true));
                                }
                            }

                            if ($bulk_result['status'] == 'error' && $bulk_result['message']) {
                                $sms_log->write('(Eskiz.uz) SMS send error: ' . print_r($bulk_result['message'], true));
                            }
                        }
                    }
                } else {
                    $sms_log->write('(Eskiz.uz) : Authorisation fault or Current Balance is overdraft limit, Sms not send');
                }
            }
        } else {
            $sms_log->write('(Eskiz.uz) Error: Please set correct login/password!');
        }
    }

    public function prepPhone($phone)
    {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    public function getToken($data)
    {
        $cache_file = DIR_LOGS . 'eskiz_sms.token';

        if (file_exists($cache_file)) {
            $cache = json_decode(file_get_contents($cache_file), true);

            $config_token = $cache['token'];
            $config_date = $cache['date_added'];

            $date_expired = strtotime($config_date . "+28 days");

            if ($config_token && strtotime($config_date) < $date_expired) {
                $result = $config_token;
            }

            if ($config_date && strtotime($config_date) > $date_expired) {
                $new_token = $this->refreshToken($cache_file);

                $result = $new_token;
            }

            return $result;
        } else {
            $result = $this->sendRequest('', 'token', $this->baseurl . 'auth/login', $data);

            if (isset($result['message']) && $result['message'] == 'token_generated') {
                $token = $result['data']['token'];

                $data = [
                    'token'      => $token,
                    'date_added' => date('d-m-Y')
                ];

                file_put_contents($cache_file, json_encode($data));

                return $token;
            }
        }
    }

    public function refreshToken($cache_file)
    {
        $result = $this->sendRequest('', 'refresh', $this->baseurl . 'auth/refresh');

        if (isset($result['message']) && $result['message'] == 'token_generated') {
            $token = $result['data']['token'];

            $data = [
                'token'      => $token,
                'date_added' => date('d-m-Y')
            ];

            file_put_contents($cache_file, json_encode($data));

            return $token;
        }
    }

    public function sendRequest($token, $method, $url, $data = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        if ($method == 'token') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'email'    => $data['email'],
                'password' => $data['password']
            ]);
        }

        if ($method == 'send' && $data) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'mobile_phone' => $data['phone'],
                'message'      => $data['text'],
                'from'         => $data['from']
            ]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token"
            ]);
        }

        if ($method == 'status') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token"
            ]);
        }

        if ($method == 'refresh') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token"
            ]);
        }

        if ($method == 'user') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token"
            ]);
        }

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response, true);
    }

    public function getUser($token)
    {
        $result = $this->sendRequest($token, 'user', $this->baseurl . 'auth/user');

        return $result;
    }

    public function sendSMS($token, $data)
    {
        $result = $this->sendRequest($token, 'send', $this->baseurl . 'message/sms/send', $data);

        return $result;
    }

    public function statusSMS($token, $sms_id)
    {
        $result = $this->sendRequest($token, 'status', $this->baseurl . 'message/sms/status/' . $sms_id);

        return $result;
    }
}
