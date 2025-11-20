<?php
class ControllerExtensionShippingFANCourier extends Controller {
	private $error = array();
	public $domain = HTTPS_CATALOG;

	public function index() {
		$this->load->language('extension/shipping/fancourier');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->db->query("UPDATE `" . DB_PREFIX . "fancourier_service` SET `status` = 0 WHERE 1");

			foreach($this->request->post as $key => $request) {

                if (strpos($key, '_price') !== false) {
                    continue;
                }

				$servId = (int)filter_var($key, FILTER_SANITIZE_NUMBER_INT);

                if (!$servId) {
                    continue;
                }

                $price = $this->request->post['shipping_fancourier_' .$servId. '_price'] ?: null;
                $price = is_numeric($price) ? $price : null;


                $this->db->query("UPDATE `" . DB_PREFIX . "fancourier_service` SET `status` = 1, `fixed_price` = '".$price."' WHERE `fancourier_id` = '" . $servId. "'");
			}
			$this->model_setting_setting->editSetting('shipping_fancourier', $this->request->post);

			//salvare date client

			$clientId = $this->request->post['shipping_fancourier_clientid'];
			$username = $this->request->post['shipping_fancourier_username'];
			$pass = $this->request->post['shipping_fancourier_password'];

			$packageType =  $this->request->post['shipping_fancourier_parcel'];
			$numberOfAWBs = $this->request->post['shipping_fancourier_labels'];
			$payment = $this->request->post['shipping_fancourier_paymentdest'];
			$priceWithoutVat = $this->request->post['shipping_fancourier_fara_tva'];
			$priceAdditionalKm = $this->request->post['shipping_fancourier_doar_km'];
			$minValueFreeShipping = $this->request->post['shipping_fancourier_min_gratuit'];
			$fixValueBucuresti =  $this->request->post['shipping_fancourier_valoare_fixa_bucuresti'];
			$fixValueCountry = $this->request->post['shipping_fancourier_valoare_fixa'];
			$refundRequest =  $this->request->post['shipping_fancourier_ramburs'];
			$addTransportFeeToRefund  = $this->request->post['shipping_fancourier_totalrb'];
			$refundToBankAccount = $this->request->post['shipping_fancourier_contcolector'];
			$CODPayment =  $this->request->post['shipping_fancourier_paymentrbdest'];
			$transportInsurance = $this->request->post['shipping_fancourier_asigurare'];
			$productCodeInContent =  $this->request->post['shipping_fancourier_content'];
			$openOndelivery = $this->request->post['shipping_fancourier_deschidere_livrare'] != "" ? 1 : 0;
			$ePOD = $this->request->post['shipping_fancourier_epod'] != "" ? 1 : 0;
			$observations = $this->request->post['shipping_fancourier_comment'];
			$contactPerson = $this->request->post['shipping_fancourier_pers_contact_expeditor'];

			$auth = $this->db->query("SELECT token FROM `" . DB_PREFIX . "fancourier_auth_info`");


			$authorization = "Authorization: Bearer ".$auth->rows[0]["token"];
			$domain = HTTPS_CATALOG;
			$url = 'https://ecommerce.fancourier.ro/save-client-options';
			$choptions = curl_init($url);
			curl_setopt($choptions, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($choptions, CURLOPT_POST, true);
			curl_setopt($choptions, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded',$authorization));
			curl_setopt ($choptions, CURLOPT_POSTFIELDS, "domain=$domain&clientId=$clientId&username=$username&pass=$pass&packageType=$packageType&numberOfAWBs=$numberOfAWBs&payment=$payment&priceWithoutVat=$priceWithoutVat&priceAdditionalKm=$priceAdditionalKm&minValueFreeShipping=$minValueFreeShipping&fixValueBucuresti=$fixValueBucuresti&fixValueCountry=$fixValueCountry&refundRequest=$refundRequest&addTransportFeeToRefund=$addTransportFeeToRefund&refundToBankAccount=$refundToBankAccount&transportInsurance=$transportInsurance&productCodeInContent=$productCodeInContent&openOndelivery=$openOndelivery&ePOD=$ePOD&observations=$observations&contactPerson=$contactPerson&CODPayment=$CODPayment&platform=Opencart");
			curl_setopt($choptions ,CURLOPT_SSL_VERIFYPEER,0);

			$response = curl_exec($choptions);
			curl_close($choptions);

			$this->session->data['success'] = $this->language->get('text_success');
			//$this->response -> redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_clientid'] = $this->language->get('entry_clientid');
		$data['help_entry_clientid'] = $this->language->get('help_entry_clientid');
		$data['entry_username'] = $this->language->get('entry_username');
		$data['help_entry_username'] = $this->language->get('help_entry_username');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['help_entry_password'] = $this->language->get('help_entry_password');
		$data['entry_onlyadm'] = $this->language->get('entry_onlyadm');
		$data['entry_parcel'] = $this->language->get('entry_parcel');
		$data['text_labels'] = $this->language->get('text_labels');
		$data['help_text_labels'] = $this->language->get('help_text_labels');
		$data['entry_paymentdest'] = $this->language->get('entry_paymentdest');
		$data['entry_fara_tva'] = $this->language->get('entry_fara_tva');

		//afisare pret doar km suplimentari
		$data['entry_doar_km'] = $this->language->get('entry_doar_km');
		$data['help_entry_doar_km'] = $this->language->get('help_entry_doar_km');
		//sfarsit afisare pret doar km suplimentari

		$data['entry_payment0'] = $this->language->get('entry_payment0');
		$data['text_min_gratuit'] = $this->language->get('text_min_gratuit');
		//alex g valoare fixa
		$data['text_valoare_fixa'] = $this->language->get('text_valoare_fixa');
		//sfarsit valoare fixa

		//alex g valoare fixa Bucuresti
		$data['text_valoare_fixa_bucuresti'] = $this->language->get('text_valoare_fixa_bucuresti');
		//sfarsit valoare fixa Bucuresti

		$data['entry_ramburs'] = $this->language->get('entry_ramburs');
		$data['entry_totalrb'] = $this->language->get('entry_totalrb');
		$data['entry_contcolector'] = $this->language->get('entry_contcolector');
		$data['entry_paymentrbdest'] = $this->language->get('entry_paymentrbdest');
		$data['help_entry_paymentrbdest'] = $this->language->get('help_entry_paymentrbdest');
		$data['entry_asigurare'] = $this->language->get('entry_asigurare');
		$data['entry_content'] = $this->language->get('entry_content');
		$data['entry_comment'] = $this->language->get('entry_comment');
		//adaugare persoana de contact
		$data['entry_pers_cont_exp'] = $this->language->get('entry_pers_cont_exp');
		//adaugare optiune
		$data['entry_deschidere_livrare'] = $this->language->get('entry_deschidere_livrare');
		$data['help_entry_deschidere_livrare'] = $this->language->get('help_entry_deschidere_livrare');
		//end
		$data['entry_epod'] = $this->language->get('entry_epod');
		$data['help_entry_epod'] = $this->language->get('help_entry_epod');
		//end
		$data['entry_redcode'] = $this->language->get('entry_redcode');
		$data['entry_express'] = $this->language->get('entry_express');

		//Collect Point
		$data['entry_ridicare_paypoint'] = $this->language->get('entry_ridicare_paypoint');
		$data['entry_ridicare_keba'] = $this->language->get('entry_ridicare_keba');
		//end Collect Point

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['clientid'])) {
			$data['error_clientid'] = $this->error['clientid'];
		} else {
			$data['error_clientid'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['labels'])) {
			$data['error_labels'] = $this->error['labels'];
		} else {
			$data['error_labels'] = '';
		}

		if (isset($this->error['min_gratuit'])) {
			$data['error_min_gratuit'] = $this->error['min_gratuit'];
		} else {
			$data['error_min_gratuit'] = '';
		}

		//alex g valoare fixa
		if (isset($this->error['valoare_fixa'])) {
			$data['error_valoare_fixa'] = $this->error['valoare_fixa'];
		} else {
			$data['error_valoare_fixa'] = '';
		}
		//sfarsit alex g

		//alex g valoare fixa bucuresti
		if (isset($this->error['valoare_fixa_bucuresti'])) {
			$data['error_valoare_fixa_bucuresti'] = $this->error['valoare_fixa_bucuresti'];
		} else {
			$data['error_valoare_fixa_bucuresti'] = '';
		}
		//sfarsit alex g


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_extension'),
			'href'      => $this->url->link('marketplace/shipping', 'user_token=' . $this->session->data['user_token']. '&type=shipping', true),

		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/shipping/fancourier', 'user_token=' . $this->session->data['user_token'], true),

		);

		$data['action'] = $this->url->link('extension/shipping/fancourier', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=shipping', true);

		if (isset($this->request->post['shipping_fancourier_status'])) {
			$data['shipping_fancourier_status'] = $this->request->post['shipping_fancourier_status'];
		} else {
			$data['shipping_fancourier_status'] = $this->config->get('shipping_fancourier_status');
		}

		if (isset($this->request->post['shipping_fancourier_clientid'])) {
			$data['shipping_fancourier_clientid'] = $this->request->post['shipping_fancourier_clientid'];
		} else {
			$data['shipping_fancourier_clientid'] = $this->config->get('shipping_fancourier_clientid');
		}

		if (isset($this->request->post['shipping_fancourier_username'])) {
			$data['shipping_fancourier_username'] = $this->request->post['shipping_fancourier_username'];
		} else {
			$data['shipping_fancourier_username'] = $this->config->get('shipping_fancourier_username');
		}

		if (isset($this->request->post['shipping_fancourier_password'])) {
			$data['shipping_fancourier_password'] = $this->request->post['shipping_fancourier_password'];
		} else {
			$data['shipping_fancourier_password'] = $this->config->get('shipping_fancourier_password');
		}

		if (isset($this->request->post['shipping_fancourier_parcel'])) {
			$data['shipping_fancourier_parcel'] = $this->request->post['shipping_fancourier_parcel'];
		} else {
			$data['shipping_fancourier_parcel'] = $this->config->get('shipping_fancourier_parcel');
		}

		if (isset($this->request->post['shipping_fancourier_labels'])) {
			$data['shipping_fancourier_labels'] = $this->request->post['shipping_fancourier_labels'];
		} else {
			$data['shipping_fancourier_labels'] = $this->config->get('shipping_fancourier_labels');
		}

		if (isset($this->request->post['shipping_fancourier_paymentdest'])) {
			$data['shipping_fancourier_paymentdest'] = $this->request->post['shipping_fancourier_paymentdest'];
		} else {
			$data['shipping_fancourier_paymentdest'] = $this->config->get('shipping_fancourier_paymentdest');
		}

		if (isset($this->request->post['shipping_fancourier_fara_tva'])) {
			$data['shipping_fancourier_fara_tva'] = $this->request->post['shipping_fancourier_fara_tva'];
		} else {
			$data['shipping_fancourier_fara_tva'] = $this->config->get('shipping_fancourier_fara_tva');
		}


		//afisare valoare doar km suplimentari
		if (isset($this->request->post['shipping_fancourier_doar_km'])) {
			$data['shipping_fancourier_doar_km'] = $this->request->post['shipping_fancourier_doar_km'];
		} else {
			$data['shipping_fancourier_doar_km'] = $this->config->get('shipping_fancourier_doar_km');
		}
		//sfarsit afisare valoare doar km suplimentari


		if (isset($this->request->post['shipping_fancourier_payment0'])) {
			$data['shipping_fancourier_payment0'] = $this->request->post['shipping_fancourier_payment0'];
		} else {
			$data['shipping_fancourier_payment0'] = $this->config->get('shipping_fancourier_payment0');
		}

		if (isset($this->request->post['shipping_fancourier_min_gratuit'])) {
			$data['shipping_fancourier_min_gratuit'] = $this->request->post['shipping_fancourier_min_gratuit'];
		} else {
			$data['shipping_fancourier_min_gratuit'] = $this->config->get('shipping_fancourier_min_gratuit');
		}

		//alex g valoare fixa
		if (isset($this->request->post['shipping_fancourier_valoare_fixa'])) {
			$data['shipping_fancourier_valoare_fixa'] = $this->request->post['shipping_fancourier_valoare_fixa'];
		} else {
			$data['shipping_fancourier_valoare_fixa'] = $this->config->get('shipping_fancourier_valoare_fixa');
		}
		//sfarsit valoare fixa

		//alex g valoare fixa bucuresti
		if (isset($this->request->post['shipping_fancourier_valoare_fixa_bucuresti'])) {
			$data['shipping_fancourier_valoare_fixa_bucuresti'] = $this->request->post['shipping_fancourier_valoare_fixa_bucuresti'];
		} else {
			$data['shipping_fancourier_valoare_fixa_bucuresti'] = $this->config->get('shipping_fancourier_valoare_fixa_bucuresti');
		}
		//sfarsit valoare fixa bucuresti

		if (isset($this->request->post['shipping_fancourier_ramburs'])) {
			$data['shipping_fancourier_ramburs'] = $this->request->post['shipping_fancourier_ramburs'];
		} else {
			$data['shipping_fancourier_ramburs'] = $this->config->get('shipping_fancourier_ramburs');
		}

		if (isset($this->request->post['shipping_fancourier_totalrb'])) {
			$data['shipping_fancourier_totalrb'] = $this->request->post['shipping_fancourier_totalrb'];
		} else {
			$data['shipping_fancourier_totalrb'] = $this->config->get('shipping_fancourier_totalrb');
		}

		if (isset($this->request->post['shipping_fancourier_contcolector'])) {
			$data['shipping_fancourier_contcolector'] = $this->request->post['shipping_fancourier_contcolector'];
		} else {
			$data['shipping_fancourier_contcolector'] = $this->config->get('shipping_fancourier_contcolector');
		}

		if (isset($this->request->post['shipping_fancourier_paymentrbdest'])) {
			$data['shipping_fancourier_paymentrbdest'] = $this->request->post['shipping_fancourier_paymentrbdest'];
		} else {
			$data['shipping_fancourier_paymentrbdest'] = $this->config->get('shipping_fancourier_paymentrbdest');
		}

		if (isset($this->request->post['shipping_fancourier_asigurare'])) {
			$data['shipping_fancourier_asigurare'] = $this->request->post['shipping_fancourier_asigurare'];
		} else {
			$data['shipping_fancourier_asigurare'] = $this->config->get('shipping_fancourier_asigurare');
		}

		if (isset($this->request->post['shipping_fancourier_content'])) {
			$data['shipping_fancourier_content'] = $this->request->post['shipping_fancourier_content'];
		} else {
			$data['shipping_fancourier_content'] = $this->config->get('shipping_fancourier_content');
		}

		if (isset($this->request->post['shipping_fancourier_comment'])) {
			$data['shipping_fancourier_comment'] = $this->request->post['shipping_fancourier_comment'];
		} else {
			$data['shipping_fancourier_comment'] = $this->config->get('shipping_fancourier_comment');
		}

		//boby 02.05.2014 adaugare persoana de contact
		if (isset($this->request->post['pers_contact_expeditor'])) {
			$data['shipping_fancourier_pers_contact_expeditor'] = $this->request->post['shipping_fancourier_pers_contact_expeditor'];
		} else {
			$data['shipping_fancourier_pers_contact_expeditor'] = $this->config->get('shipping_fancourier_pers_contact_expeditor');
		}
		//end boby

		//boby 05.05.2014 deschidere la livrare
		if (isset($this->request->post['shipping_fancourier_deschidere_livrare'])) {
			$data['shipping_fancourier_deschidere_livrare'] = $this->request->post['shipping_fancourier_deschidere_livrare'];
		} else {
			$data['shipping_fancourier_deschidere_livrare'] = $this->config->get('shipping_fancourier_deschidere_livrare');
		}

		if (isset($this->request->post['shipping_fancourier_epod'])) {
			$data['shipping_fancourier_epod'] = $this->request->post['shipping_fancourier_epod'];
		} else {
			$data['shipping_fancourier_epod'] = $this->config->get('shipping_fancourier_epod');
		}
		//end boby

		if (isset($this->request->post['shipping_fancourier_redcode'])) {
			$data['shipping_fancourier_redcode'] = $this->request->post['shipping_fancourier_redcode'];
		} else {
			$data['shipping_fancourier_redcode'] = $this->config->get('shipping_fancourier_redcode');
		}

		if (isset($this->request->post['shipping_fancourier_express'])) {
			$data['shipping_fancourier_express'] = $this->request->post['shipping_fancourier_express'];
		} else {
			$data['shipping_fancourier_express'] = $this->config->get('shipping_fancourier_express');
		}

		if (isset($this->request->post['shipping_fancourier_ridicare_paypoint'])) {
			$data['shipping_fancourier_ridicare_paypoint'] = $this->request->post['shipping_fancourier_ridicare_paypoint'];
		} else {
			$data['shipping_fancourier_ridicare_paypoint'] = $this->config->get('shipping_fancourier_ridicare_paypoint');
		}

		if (isset($this->request->post['shipping_fancourier_ridicare_keba'])) {
			$data['shipping_fancourier_ridicare_keba'] = $this->request->post['shipping_fancourier_ridicare_keba'];
		} else {
			$data['shipping_fancourier_ridicare_keba'] = $this->config->get('shipping_fancourier_ridicare_keba');
		}


		//preluare tipuri de servicii si statusul lor din DB
		$services = $this->db->query("SELECT fancourier_id, fancourier_name, status, fixed_price FROM `" . DB_PREFIX . "fancourier_service`");
		$data['services'] = $services->rows;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/fancourier', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/fancourier')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['shipping_fancourier_clientid']) {
			$this->error['clientid'] = $this->language->get('error_clientid');
		}

		if (!$this->request->post['shipping_fancourier_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['shipping_fancourier_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['shipping_fancourier_labels']) {
			$this->error['labels'] = $this->language->get('error_labels');
		}

		if ($this->request->post['shipping_fancourier_labels']) {
			if (!is_numeric($this->request->post['shipping_fancourier_labels'])){
				$this->error['labels'] = $this->language->get('error_labels');
			}
		}

		if ($this->request->post['shipping_fancourier_min_gratuit']) {
			if (!is_numeric($this->request->post['shipping_fancourier_min_gratuit'])){
				$this->error['min_gratuit'] = $this->language->get('error_min_gratuit');
			}
		}

		//alex g valoare fixa
		if ($this->request->post['shipping_fancourier_valoare_fixa']) {
			if (!is_numeric($this->request->post['shipping_fancourier_valoare_fixa'])){
				$this->error['valoare_fixa'] = $this->language->get('error_valoare_fixa');
			}
		}
		//sfarsit valoare fixa

		//alex g valoare fixa bucuresti
		if ($this->request->post['shipping_fancourier_valoare_fixa_bucuresti']) {
			if (!is_numeric($this->request->post['shipping_fancourier_valoare_fixa_bucuresti'])){
				$this->error['valoare_fixa_bucuresti'] = $this->language->get('error_valoare_fixa_bucuresti');
			}
		}
		//sfarsit valoare fixa bucuresti

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "fancourier_auth_info` (
			`id` INT(10) NOT NULL AUTO_INCREMENT,
			`token` VARCHAR(255) NOT NULL,
			PRIMARY KEY (`id`)
		)");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "fancourier_order_info` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`id_order` varchar(255) NOT NULL,
			`fan_AWB` varchar(255) NOT NULL,
			`fanbox_name` varchar(255),
			PRIMARY KEY (`id`)
		)");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "fancourier_service` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`fancourier_id` INT(11) NOT NULL,
			`fancourier_name` VARCHAR(255),
			`name` VARCHAR(255),
			`status` TINYINT,
			`fixed_price` DECIMAL(10,2),
			PRIMARY KEY (`id`)
		)");

		$domain = HTTPS_CATALOG;
		$url = 'https://ecommerce.fancourier.ro/authShop';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "domain=$domain");
		curl_setopt($ch ,CURLOPT_SSL_VERIFYPEER,0);

		$response = curl_exec($ch);
		curl_close($ch);

		$responseDecoded = json_decode($response);

		if(property_exists($responseDecoded,"token")){
			$token = $responseDecoded->token;
		} else {
			exit;
		}

		if(strlen($token)==128){
			//insert token in fancourier_auth_info
			$this->db->query("INSERT INTO `" . DB_PREFIX . "fancourier_auth_info` SET token = '" .$token. "'");
		} else {
			exit;
		}

		$authorization = "Authorization: Bearer ".$token;

		//get and insert services
		$url = 'https://ecommerce.fancourier.ro/get-services';
		$chservices = curl_init($url);
		curl_setopt($chservices, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($chservices, CURLOPT_POST, true);
		curl_setopt($chservices, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded',$authorization));
		curl_setopt ($chservices, CURLOPT_POSTFIELDS, "domain=$domain");
		curl_setopt($chservices ,CURLOPT_SSL_VERIFYPEER,0);

		$response = curl_exec($chservices);

		$services = json_decode($response);
		//$this->log($services,1);
		curl_close($chservices);

		if(!is_array($services)){
			exit;
		}

		foreach($services as $service){

            if (strpos($service->ServiceName, 'Cont Colector') !== false) {
                continue;
            }

			//$this->log->write("merge".json_encode($service->ServiceId));
			$this->db->query("INSERT INTO `" . DB_PREFIX . "fancourier_service` SET fancourier_id = '" .$service->ServiceId. "', fancourier_name='".$service->ServiceName."', status = 0");
		}
		$this->load->model('setting/event');
		$this->model_setting_event->addEvent('generateOrder', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/module/fancourier/generateOrder');
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "fancourier_auth_info`;");
		// $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "fancourier_order_info`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "fancourier_service`;");

	}

	public function info($orderInfo)
	{
		$shippingCode = explode('.', $orderInfo['shipping_code']);

		$shippingByFan = $shippingCode[0] === 'fancourier';

		if (!$shippingByFan) {
			return null;
		}

		$orderId = $orderInfo['order_id'];

		$error = $this->request->get['error'] ?? null;
		$success = $this->request->get['success'] ?? null;

		switch ($error) {
			case 1:
				$error = "Awb-ul nu a putut fi generat... verifică fișierul de log.";
				break;
			case 2:
				$error = "A aparut o eroare la generarea AWB-ului...";
				break;
			case 3:
				$error = "AWB-ul nu a putut fi șters...";
				break;
			default:
				$error = null;
		}

		switch ($success) {
			case 1:
				$error = "Awb-ul a fost generat cu success.";
				break;
			case 2:
				$error = "Awb-ul a fost sters cu success.";
				break;
			default:
				$error = null;
		}


		$generateAwbUrl = $this->url->link(
			'extension/shipping/fancourier/generateAwb',
			$this->addToken(['order_id' => $orderId]),
			true
		);
		$deleteAwbUrl = $this->url->link(
			'extension/shipping/fancourier/deleteAwb',
			$this->addToken(['order_id' => $orderId]),
			true
		);
		$printAwbUrl = $this->url->link(
			'extension/shipping/fancourier/printAwb',
			$this->addToken(['order_id' => $orderId]),
			true
		);

        $return = null;
        if ($error) {
            $return = $error;
        } else if ($success) {
            $return = $success;
        }

		$orderInfoFAN = $this->db->query(
			"SELECT fan_AWB, id_order, fanbox_name FROM `" . DB_PREFIX . "fancourier_order_info` WHERE id_order = $orderId"
		)->row;

        $awb = $orderInfoFAN['fan_AWB'] ?? null;

		return [
			'awb_icon' => $awb ? 'minus' : 'plus',
			'awb_class' => $awb ? 'danger' : 'success',
			'awb_text' => $awb ? 'Sterge AWB' : 'Genereaza AWB',
			'print_awb_url' => $printAwbUrl,
			'awb_url' => $awb ? $deleteAwbUrl : $generateAwbUrl, // delete or generate url
			'awb' => $awb, // null
			'fanbox_name' => $orderInfoFAN['fanbox_name'] ?? null,
			'error' => $return
		];
	}

	private function addToken(array $parts = [])
	{
		if (isset($this->session->data['token'])) {
			return array_merge($parts, ['token' => $this->session->data['token']]);
		}

		if (isset($this->session->data['user_token'])) {
			return array_merge($parts, ['user_token' => $this->session->data['user_token']]);
		}

		return $parts;
	}

	public function generateAwb()
	{
		$this->load->model('sale/order');

		$orderId = $this->request->get['order_id'];
		$orderInfo = $this->model_sale_order->getOrder($orderId);

		if (!isset($orderId) || !$orderInfo) {
			return new Action('error/not_found');
		}

		$token = $this->db->query("SELECT token FROM `" . DB_PREFIX . "fancourier_auth_info`")->row['token'] ?? null;

		if (!$token) {
			$this->log->write('Fan Courier - Token missing from Database. Please reinstall the module');
			$this->response->redirect(
				$this->url->link('sale/order/info', $this->addToken(array('order_id' => $orderId)), true)
			);
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://ecommerce.fancourier.ro/generate-awb',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_POSTFIELDS => [
				'domain' => HTTPS_CATALOG,
				'orderId' => $orderId
			],
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer ' . $token
			),
		));

		$response = curl_exec($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_code !== 200) {
			$this->log->write('Fan Courier - Awb is not created. - ' . $response);
			$this->response->redirect(
				$this->url->link('sale/order/info', $this->addToken(['order_id' => $orderId, 'error' => 1]), true)
			);
		}

		$responseBody = json_decode($response, true);
		$awb = $responseBody['awbNo'] ?? null;

		if ($awb === null) {
			$this->response->redirect(
				$this->url->link('sale/order/info', $this->addToken(['order_id' => $orderId, 'error' => 2]), true)
			);
		}

		$this->db->query(
			"UPDATE `" . DB_PREFIX . "fancourier_order_info` SET fan_AWB = ".$awb." WHERE id_order = " . $orderId
		);

		$this->response->redirect(
			$this->url->link('sale/order/info', $this->addToken(['order_id' => $orderId, 'success' => 1]), true)
		);
	}

	public function printAwb()
	{
		$this->load->model('sale/order');
		$this->load->model('setting/setting');
//		$this->model_setting_setting->editSetting('shipping_fancourier', $this->request->post);

		$orderId = $this->request->get['order_id'];

		$awb = $this->db->query(
			"SELECT fan_AWB, id_order FROM `" . DB_PREFIX . "fancourier_order_info` WHERE id_order = $orderId"
		)->row['fan_AWB'] ?? null;

		$url = "https://www.selfawb.ro/view_awb_integrat_pdf.php";
		$params = [
			'username' => $this->config->get('shipping_fancourier_username'),
			'user_pass' => $this->config->get('shipping_fancourier_password'),
			'client_id' => $this->config->get('shipping_fancourier_clientid'),
			'nr' => $awb,
			'pdf' => 1,
		];

		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => [
				'Content-Type' => 'application/x-www-form-urlencoded'
			],
		]);

		$response = curl_exec($curl);
		curl_close($curl);


		header('Cache-Control: public');
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="' . $awb . '.pdf"');

		echo $response;
	}

	public function deleteAwb() {

		$orderId = $this->request->get['order_id'];

		$token = $this->db->query("SELECT token FROM `" . DB_PREFIX . "fancourier_auth_info`")->row['token'] ?? null;

		if (!$token) {
			$this->log->write('Fan Courier - Token missing from Database. Please reinstall the module');
			$this->response->redirect(
				$this->url->link('sale/order/info', $this->addToken(array('order_id' => $orderId)), true)
			);
		}

		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => 'https://ecommerce.fancourier.ro/delete-awb',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_POSTFIELDS => [
				'domain' => HTTPS_CATALOG,
				'orderId' => $orderId
			],
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => [
				'Authorization: Bearer ' . $token
			],
		]);

		$response = curl_exec($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_code !== 200) {
			$this->log->write('Fan Courier - Awb can\'t be deleted. - ' . $response);
			$this->response->redirect(
				$this->url->link('sale/order/info', $this->addToken(['order_id' => $orderId, 'error' => 3]), true)
			);
		}

        $this->db->query(
            "UPDATE `" . DB_PREFIX . "fancourier_order_info` SET fan_AWB = '' WHERE id_order =" . $orderId
        );

		$this->response->redirect(
			$this->url->link('sale/order/info', $this->addToken(['order_id' => $orderId, 'success' => 2]), true)
		);
	}

}

