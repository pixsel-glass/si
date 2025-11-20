<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class Alphasmsua extends SmsGate {

    protected $baseurl = 'https://alphasms.ua/api/http.php?';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        if ($this->username) {
            $body = [
                'version' => 'http',
                'command' => 'balance',
            ];

            if (!$this->password) {
                $body['key'] = $this->username;
            } else {
                $body['login'] = $this->username;
                $body['password'] = $this->password;
            }

            $balance = $this->getBalance($body);

            if ($balance > 0 || $balance !== '0.00') {
                if ($this->to) {
                    $phone = $this->prepPhone($this->to);
                } else {
                    $phone = false;
                    $sms_log->write('(Alphasms.ua) Error: Phone destination not found!');
                }

                if ($this->from) {
                    $sender = $this->from;
                } else {
                    $sender = 'Market';
                    $sms_log->write('(Alphasms.ua) Notice: Default Sender set! Please input real Sender');
                }

                $body['command'] = 'send';
                $body['to'] = $phone;
                $body['from'] = $sender;
                $body['message'] = $this->message;

                if ($phone) {
                    $body = $this->bodyViber($body, $this->viber);
                    $result = $this->getResponse($body);

                    $sms_log->write('(Alphasms.ua) Message send: ' . print_r($result, true));
                }

                if ($this->copy) {
                    $numbers = explode(',', $this->copy);

                    foreach ($numbers as $number) {
                        $phones[] = $this->prepPhone($number);
                    }

                    foreach ($phones as $phone) {
                        $body['to'] = $phone;

                        $body = $this->bodyViber($body, $this->viber);
                        $result = $this->getResponse($body);

                        $sms_log->write('(Alphasms.ua) Message send: ' . print_r($result, true));
                    }
                }
            } else {
                $sms_log->write('(Alphasms.ua): Current Balance is 0, Sms not send or Authorisation fault');
            }
        } else {
            $sms_log->write('(Alphasms.ua) Error: Please enter valid Login or Password!');
        }
    }

    public function getBalance($body) {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        $result = null;

        $response = $this->getResponse($body);

        $result = preg_replace('/[^0-9.]/', '', $response);

        $sms_log->write('(Alphasms.ua): Current Balanse is: ' . $result);

        return $result;
    }

    public function bodyViber($body, $params) {
        if ($params && $params['status']) {
            $body['viber'] = true;
            $body['viber_sms'] = true;
            $body['viber_from'] = $params['sender'];
            $body['viber_message'] = $params['message'];

            if ($params['ttl']) {
                $body['viber_lifetime'] = $params['ttl'];
            }

            $body['viber_type'] = 'text';

            if (isset($params['image_url']) && !isset($params['caption'])) {
                $body['viber_type'] = 'image';
            }

            if (isset($params['image_url']) && isset($params['caption'])) {
                $body['viber_type'] = 'button';
            }

            if ($params['image_url']) {
                $body['viber_image'] = $params['image_url'];
            }
            if ($params['caption']) {
                $body['viber_button'] = $params['caption'];
            }
            if ($params['action']) {
                $body['viber_url'] = $params['action'];
            }
        }

        return $body;
    }

    private function getResponse($body) {
        $url = $this->baseurl . http_build_query($body);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }

    private function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }
}