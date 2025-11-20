<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

final class TurbosmsHttp extends SmsGate {

    protected $baseurl = 'https://api.turbosms.ua/';

    public function send() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        $module = 'message';
        $method = 'send.json';

        $url = $this->baseurl . $module . '/' . $method;

        $phones = [];

        if ($this->username) {
            if ($this->to) {
                $phones[] = $this->prepPhone($this->to);
            } else {
                $sms_log->write('(TurboSMS) Error: Phone destination not found!');
            }

            if ($this->from) {
                $sender = $this->from;
            } else {
                $sender = 'Market';
                $sms_log->write('(TurboSMS) Notice: Default Sender set! Please input real Sender');
            }

            if ($this->copy) {
                $numbers = explode(',', $this->copy);

                foreach ($numbers as $number) {
                    $phones[] = $this->prepPhone($number);
                }
            }

            $body = [];

            if ($phones) {
                $body = [
                    'recipients' => $phones,
                ];

                $params = [
                    'sender' => $sender,
                    'text'   => $this->message,
                    'viber'  => $this->viber,
                ];

                //VIBER
                if ($this->viber['status']) {
                    $body = $this->bodySMS($body, $params);
                    $body = $this->bodyViber($body, $params);
                } else {
                    $body = $this->bodySMS($body, $params);
                }
            }

            $balance = $this->getBalance();

            if ($balance && $body) {
                $result = $this->getResponse($url, $body);
                $sms_log->write('(TurboSMS) : ' . $this->getResponseCode($result['response_code']));

                if (isset($result['response_result'])) {
                    foreach ($result['response_result'] as $response) {
                        $sms_log->write('(TurboSMS) MessageID: ' . $response['message_id'] . ' - ' . $this->getResponseCode($response['response_code']));
                    }
                }
            } else {
                $sms_log->write('(TurboSMS) : Current Balance is 0, Sms not send or Authorisation fault');
            }
        } else {
            $sms_log->write('(TurboSMS) Error: Please set correct login/password!');
        }
    }

    public function getBalance() {
        //Sms Log
        $sms_log = new Log('sms_log.log');

        $result = null;
        $module = 'user';
        $method = 'balance.json';

        $url = $this->baseurl . $module . '/' . $method;
        $body = [];

        $response = $this->getResponse($url, $body);
        $sms_log->write('(TurboSMS) : ' . $this->getResponseCode($response['response_code']));
        if (isset($response['response_result']['balance'])) {
            $result = $response['response_result']['balance'];
        }

        return $result;
    }

    public function bodySMS($body, $params) {
        $body['sms'] = [
            'sender' => $params['sender'],
            'text'   => $params['text'],
        ];

        return $body;
    }

    public function bodyViber($body, $params) {
        if ($params['viber']['sender']) {
            $body['viber'] = [
                'sender' => $params['viber']['sender'],
                'text'   => $params['viber']['message'],
            ];

            if ($params['viber']['ttl']) {
                $body['viber']['ttl'] = $params['viber']['ttl'];
            }
            if ($params['viber']['image_url']) {
                $body['viber']['image_url'] = $params['viber']['image_url'];
            }
            if ($params['viber']['caption']) {
                $body['viber']['caption'] = $params['viber']['caption'];
            }
            if ($params['viber']['action']) {
                $body['viber']['action'] = $params['viber']['action'];
            }

            return $body;
        }
    }

    public function prepPhone($phone) {
        $result = preg_replace('/[^0-9,]/', '', $phone);

        return $result;
    }

    private function getResponse($url, $data) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Authorization: Bearer ' . $this->username,
            'Content-Type: application/json'
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $res = curl_exec($ch);
        $result = json_decode($res, true);
        curl_close($ch);

        return $result;
    }

    private function getResponseCode($code) {
        $response_code = [
            '0'   => 'Запрос обработан успешно.',
            '1'   => 'Успешный результат вызова метода ping.',
            '103' => 'Отсутствует токен аутентификации.',
            '104' => 'Отсутствуют данные запроса.',
            '105' => 'Аутентификация не пройдена, не верный токен.',
            '106' => 'Пользователь заблокирован, работа с API невозможна до разблокировки.',
            '200' => 'Отсутствует или пустой параметр отправителя сообщения.',
            '201' => 'Отсутствует или пустой параметр текста сообщения.',
            '202' => 'Отсутствует или пустой список получателей сообщения.',
            '203' => 'Не достаточно кредитов на балансе для создания рассылки.',
            '204' => 'Отсутствуют или пустые параметры кнопки в сообщении, когда она обязательна.',
            '205' => 'Отсутствует или пустой параметр текста на кнопке в сообщении.',
            '206' => 'Отсутствует или пустой параметр URL адреса, куда перейдёт получатель сообщения при нажатии на кнопку.',
            '300' => 'Неверный запрос, проверьте его структуру и корректность данных.',
            '301' => 'Неверный токен аутентификации.',
            '302' => 'Неверный отправитель сообщения.',
            '303' => 'Неверная дата отложенной отправки сообщения.',
            '304' => 'Недопустимое значение текста сообщения. Возвращается если передано не строковое значение или кодировка символов не входит в набор UTF-8.',
            '305' => 'Недопустимый номер получателя, система не смогла распознать страну и оператора получателя.',
            '306' => 'Недопустимое значение параметра ttl, значение должно быть целочисленным и не представлено в виде строки.',
            '307' => 'Недопустимое значение параметра message_id, неверный формат.',
            '308' => 'Недопустимое значение параметра id при вызове метода file/details, неверный формат.',
            '400' => 'Не разрешённый отправитель для текущего пользователя.',
            '401' => 'Отправитель разрешён, но не активирован на данный момент (не оплачено использование в текущем месяце, не завершена регистрация и т.п.).',
            '402' => 'Недопустимый тип файла изображения.',
            '403' => 'Недопустимая дата отложенной отправки сообщения (выходит за пределы установленных ограничений).',
            '404' => 'Номер получателя находится в стоплисте (для sms) или в игнорлисте (для Viber), отправка невозможна.',
            '405' => 'Недопустимое количество получателей.',
            '406' => 'Недопустимая страна получателя. У пользователя не активирована возможность отправлять сообщения получателям данной страны. Для активации такой возможности свяжитесь с нашим отделом поддержки клиентов.',
            '407' => 'Получатель уже присутствует в рассылке, дубликаты игнорируются.',
            '408' => 'Текст на кнопке слишком длинный, допускается не более 30 символов.',
            '409' => 'Недопустимое значение параметра ttl (выходит за пределы установленных ограничений).',
            '410' => 'Недопустимый контент в транзакционном сообщении. В таких сообщениях можно отправлять только текст, а кнопка и изображения запрещены.',
            '411' => 'Какой-то из параметров имеет недопустимое значение, свяжитесь с нашим отделом поддержки клиентов для выяснения деталей.',
            '412' => 'Текст содержит запрещённые фрагменты.',
            '413' => 'Превышена допустимая длина текста сообщения.',
            '414' => 'Данные сообщения с переданным message_id недоступны для текущего пользователя.',
            '415' => 'Запрещено отправлять транзакционные сообщения от общего отправителя.',
            '416' => 'Не найден шаблон, соответствующий переданному транзакционному сообщению.',
            '417' => 'Файл с переданным id не существует или недоступен для текущего пользователя.',
            '418' => 'Указанный загружаемый файл не найден или пустой.',
            '419' => 'Неподддерживаемый тип файла.',
            '420' => 'Размер файла превышает максимально допустимый размер 3Мб.',
            '500' => 'Не удалось сконвертировать данные результата в JSON формат, незамедлительно свяжитесь с нашим отделом поддержки клиентов для выяснения деталей.',
            '501' => 'Не удалось сконвертировать данные результата в XML формат, незамедлительно свяжитесь с нашим отделом поддержки клиентов для выяснения деталей.',
            '502' => 'Не удалось распознать тело запроса (неверный формат).',
            '503' => 'Не удалось отправить SMS сообщение.',
            '504' => 'Не удалось отправить Viber сообщение.',
            '505' => 'Не удалось сохранить изображение.',
            '505' => 'Не удалось сохранить файл.',
            '800' => 'Сообщения успешно созданы и добавлены в очередь отправки. Некоторые сообщения могут попадать на предварительную модерацию.',
            '801' => 'Сообщения успешно отправлены.',
            '802' => 'Сообщения успешно созданы и добавлены в очередь отправки, но некоторые получатели не попали в список рассылки, детали смотрите в ответе.',
            '803' => 'Сообщения успешно отправлены, но некоторые получатели не попали в список рассылки, детали смотрите в ответе.',
            '999' => 'Ошибка выполнения запроса, свяжитесь с отделом поддержки для выяснения деталей.',
        ];

        $result = false;

        if (isset($response_code[$code])) {
            $result = $response_code[$code];
        }

        return $result;
    }
}