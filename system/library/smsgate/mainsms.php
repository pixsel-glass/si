<?php
/**
 * @author  Alexander Vakhovskiy (AlexWaha)
 * @link    https://ocdev.pro
 * @email support@ocdev.pro
 * @license Commercial
 */

require_once DIR_SYSTEM . 'library/smsgate/lib/mainsms.class.php';

final class Mainsms extends SmsGate {

	public function send() {
		//Sms Log
		$sms_log = new Log('sms_log.log');

		if ($this->username && $this->password) {
			$api = new MainSMSLib($this->username, $this->password, true, false);

			$balance = $api->getBalance();

			if ($balance && round($balance) > 2) {
				$sms_log->write('(Mainsms) : Balance: ' . $balance);

				if ($this->from) {
					$sender = $this->from;
				} else {
					$sender = 'INFORM';
					$sms_log->write('(Mainsms) Notice: Default Sender set! Please input real Sender');
				}

				if ($this->to) {
					$number = $this->prepPhone($this->to);

					$api->sendSMS($number, $this->message, $sender);

					$sms_result = $api->getResponse();

					if (is_array($sms_result) && $sms_result['status'] == 'success') {
						$sms_log->write('(Mainsms) : Sms Result: Status: ' . $sms_result['status'] . '; Cost: ' . $sms_result['price'] . '; Balance: ' . $sms_result['balance']);

						if (isset($sms_result['messages_id'])) {
							foreach ($sms_result['messages_id'] as $message_id) {
								$sms_status = $api->checkStatus($message_id);
								$sms_log->write('(Mainsms) : Sms Status: ID: ' . $message_id . '; Status: ' . strtoupper($sms_status[$message_id]));
							}
						}

						$sms_log->write('(Mainsms) : Sms Result: Status: ' . $sms_result['status'] . '; Cost: ' . $sms_result['price'] . '; Balance: ' . $sms_result['balance']);
					} else {
						$sms_log->write('(Mainsms) : Sms Result: ' . $sms_result['status'] . ' Message: ' . $sms_result['message']);
					}
				} else {
					$sms_log->write('(Mainsms) Error: Phone destination not found!');
				}

				if ($this->copy) {
					$phones = explode(',', $this->copy);

					$numbers = [];

					foreach ($phones as $phone) {
						$numbers[] = $this->prepPhone($phone);
					}

					$numbers = implode(',', $numbers);

					$api->sendSMS($numbers, $this->message, $sender);

					$sms_bulk_result = $api->getResponse();

					if (is_array($sms_bulk_result) && $sms_bulk_result['status'] == 'success') {
						$sms_log->write('(Mainsms) : Sms Result: Status: ' . $sms_bulk_result['status'] . '; Cost: ' . $sms_bulk_result['price'] . '; Balance: ' . $sms_bulk_result['balance']);

						if (is_array($sms_bulk_result['recipients'])) {
							foreach ($sms_bulk_result['recipients'] as $key => $recipient) {
								$sms_log->write('(Mainsms) : Sms Result Recipient: [' . $key . '] - ' . $recipient);
							}
						}

						if (isset($sms_result['messages_id'])) {
							foreach ($sms_result['messages_id'] as $message_id) {
								$sms_bulk_status = $api->checkStatus($message_id);
								$sms_log->write('(Mainsms) : Sms Status: ID:' . $message_id . '; Status: ' . strtoupper($sms_bulk_status[$message_id]));
							}
						}
					} else {
						$sms_log->write('(Mainsms) : Sms Result: ' . $sms_bulk_status['status'] . ' Message: ' . $sms_bulk_status['message']);
					}
				}
			} else {
				$sms_log->write('(Mainsms) : Current Balance is : ' . $balance . ' SMS NOT SEND');
			}
		} else {
			$sms_log->write('(Mainsms) Error: Please set correct login/password!');
		}
	}

	public function prepPhone($phone) {
		$result = preg_replace('/[^0-9,]/', '', $phone);

		return $result;
	}
}