<?php
if(!defined('REDSYS_FILE_PATH')){
    define('REDSYS_FILE_PATH', DIR_APPLICATION . '/lib/Redsys/RedsysAPI.php');
}
class ControllerExtensionPaymentRedsys extends Controller
{
	protected $name = 'redsys';
	protected $pay_method = 'T';

	var $merchant_currency;
	var $merchant_code;
	var $merchant_terminal;
	var $merchant_clave_real;
	var $merchant_clave_pruebas;

	private function calcCurrencyParams($code = false)
	{
		$merchant_currencies = explode(',', $this->config->get('payment_redsys_merchant_currency'));
		$merchant_codes = explode(',', $this->config->get('payment_redsys_merchant_code'));
		$merchant_terminals = explode(',', $this->config->get('payment_redsys_merchant_terminal'));
		$merchant_claves = explode(',', $this->config->get('payment_redsys_merchant_clave_real'));
		$merchant_claves_pruebas = explode(',', $this->config->get('payment_redsys_merchant_clave_pruebas'));
		$ret = array();
		$redsys_currency = array(
			'USD' => '840',
			'GBP' => '826',
			'JPY' => '392',
			'CNY' => '156',
			'EUR' => '978'
		);
		if(!isset($_SESSION['currency']) || !$_SESSION['currency']){
			$_SESSION['currency']='EUR';
		}
		if (!$code)
			$code = $redsys_currency[$_SESSION['currency']];
		$idx = array_search($code, $merchant_currencies);
		$this->merchant_currency = $merchant_currencies[(int)$idx];
		$this->merchant_code = $merchant_codes[(int)$idx];
		$this->merchant_terminal = $merchant_terminals[(int)$idx];
		$this->merchant_clave_real = $merchant_claves[(int)$idx];
		$this->merchant_clave_pruebas = $merchant_claves_pruebas[(int)$idx];

	}

	public function index()
	{
		$this->language->load('extension/payment/'. $this->name);
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');
		switch ($this->config->get('payment_redsys_env')) {
			case 1:
				$data['action'] = "https://sis-t.redsys.es:25443/sis/realizarPago";
				break;
			case 2:
				$data['action'] = "https://sis-i.sermepa.es:25443/sis/realizarPago";
				break;
			default:
				$data['action'] = "https://sis.redsys.es/sis/realizarPago";
				break;
		}
		$this->calcCurrencyParams();
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		//Check signature
		if (!class_exists("RedsysAPI")) {
			require_once(REDSYS_FILE_PATH);
		}
		$redsys                         = new RedsysAPI;
		$redsys->setParameter("DS_MERCHANT_AMOUNT", round($this->currency->convert($order_info['total'], $_SESSION['currency'], $this->merchant_currency) * 100));
		$redsys->setParameter("DS_MERCHANT_ORDER", sprintf("%012s", time()));
		$redsys->setParameter("DS_MERCHANT_MERCHANTCODE", $this->merchant_code);
		$redsys->setParameter("DS_MERCHANT_CURRENCY", $this->merchant_currency);
		$redsys->setParameter("DS_MERCHANT_TRANSACTIONTYPE", 0);
		$redsys->setParameter("DS_MERCHANT_TERMINAL", $this->merchant_terminal);
		$redsys->setParameter("Ds_Merchant_ConsumerLanguage", $this->language->get('_ds_merchant_consumerlanguage'));
		$redsys->setParameter("Ds_Merchant_ProductDescription", "Pedido " . $this->session->data['order_id']);
		$redsys->setParameter("Ds_Merchant_Titular", $this->session->data['order_id'] . ' - ' . $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']);
		$redsys->setParameter("Ds_Merchant_MerchantData", $this->session->data['order_id']);
		$redsys->setParameter("Ds_Merchant_MerchantName", $this->config->get('config_name'));
		$redsys->setParameter("Ds_Merchant_PayMethods", $this->pay_method);
		$redsys->setParameter("Ds_Merchant_Module", 'WooCommerce-gateway-redsys-3.5.1 ZhenIT Software');
		if (true || $this->config->get('payment_redsys_notificacion') == 1) { //Con confirmación on-line
			$redsys->setParameter("DS_MERCHANT_MERCHANTURL", $this->url->link('extension/payment/redsys/callback', '', 'SSL'));
			$redsys->setParameter("DS_MERCHANT_URLOK", $this->url->link('checkout/success'));
		} else {
			$redsys->setParameter("DS_MERCHANT_URLOK", $this->url->link('extension/payment/redsys/callback', '', 'SSL'));
		}
		$redsys->setParameter("DS_MERCHANT_URLKO", $this->url->link('checkout/checkout', '', 'SSL'));

		//Datos de configuración
		$data['version']  = "HMAC_SHA256_V1";

		//Clave del comercio que se extrae de la configuración del comercio
		// Se generan los parámetros de la petición
		$data['paramsBase64'] = $redsys->createMerchantParameters();
		$data['signatureMac'] = $redsys->createMerchantSignature($this->_get_clave());

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/redsys')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/redsys', $data);
		} else {
			return $this->load->view('extension/payment/redsys', $data);
		}
	}

	public function callback()
	{
		//Check signature
		if (!class_exists("RedsysAPI")) {
			require_once(REDSYS_FILE_PATH);
		}

		// Se crea Objeto
		$redsys = new RedsysAPI;

		/** Se decodifican los datos enviados y se carga el array de datos **/
		$decoded = $redsys->decodeMerchantParameters($_REQUEST["Ds_MerchantParameters"]);
		$redsys->stringToArray($decoded);

		/** Clave **/
		$kc = $this->_get_clave();

		/** Se calcula la firma **/
		$firma_local = $redsys->createMerchantSignatureNotif($kc, $_REQUEST["Ds_MerchantParameters"]);

		/** Extraer datos de la notificación **/
		$ds_date = $_REQUEST['Ds_Date'];
		$ds_hour = $_REQUEST['Ds_Hour'];
		$ds_amount = $_REQUEST['Ds_Amount'];
		$ds_currency = $_REQUEST['Ds_Currency'];
		$ds_order = $_REQUEST['Ds_Order'];
		$ds_merchantcode = $_REQUEST['Ds_MerchantCode'];
		$ds_terminal = $_REQUEST['Ds_Terminal'];
		$ds_signature = $_REQUEST['Ds_Signature'];
		$ds_response = $_REQUEST['Ds_Response'];
		$ds_transactiontype = $_REQUEST['Ds_TransactionType'];
		$ds_securepayment = $_REQUEST['Ds_SecurePayment'];
		$ds_merchantdata = $_REQUEST['Ds_MerchantData'];
		$ds_authorisationcode = $_REQUEST['Ds_AuthorisationCode'];
		$ds_card_country = $_REQUEST['Ds_Card_Country'];
		$ds_card_type = $_REQUEST['Ds_Card_Country'];
		$this->calcCurrencyParams($ds_currency);
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder((int)$ds_merchantdata);

		if ($order_info) {
			$importe = round($this->currency->convert($order_info['total'],$order_info['currency_code'], $this->merchant_currency) * 100);
			if (!$firma_local === $_POST["Ds_Signature"]
				|| !RedsysHelper::checkRespuesta($ds_response)
				|| !RedsysHelper::checkMoneda($ds_currency)
				|| !RedsysHelper::checkFuc($ds_merchantcode)
				|| !RedsysHelper::checkPedidoNum($ds_order)
				|| !RedsysHelper::checkImporte($ds_amount)
			) {
				if (false && $this->config->get('payment_redsys_notificacion') == 0) { //Sin confirmación on-line
					$this->response->redirect($this->config->get('config_url') . 'index.php?route=checkout/payment');
				}
				$this->log->write("Firma no válida, procedencia del mensaje (" . $_SERVER['REMOTE_ADDR'] . ") no verificada: " . $ds_signature . "!=" . $firma_local);
				die("Firma no válida, procedencia del mensaje no verificada");
			} else if (((int)$ds_response >= 0) && ((int)$ds_response <= 99)) {
				if($importe!=$ds_amount){
					$comment = "¡¡OJO!! Importe del pedido: ".$order_info['total']." importe del pago:".($ds_amount/100);
				}
				//$this->model_checkout_order->confirm((int)$ds_merchantdata, $this->config->get('payment_redsys_order_status_id'));

				$this->db->query("UPDATE oc_order SET payed='1', payed_with='redsys' WHERE order_id = '" . $order_info['order_id'] . "'");

				$this->model_checkout_order->addOrderHistory((int)$ds_merchantdata, $this->config->get('payment_redsys_order_status_id'), $comment);
			} else {
				$errors = array();
				$errors[101] = 'Tarjeta caducada';
				$errors[102] = 'Tarjeta bloqueada por el banco emisor';
				$errors[107] = 'Orden de contactar con el banco emisor de la tarjeta';
				$errors[180] = 'Tarjeta no soportada por el sistema';
				$errors[184] = 'Autenticación del titular de la tarjeta fallida';
				$errors[190] = 'Denegada por el banco emisor de la tarjeta por diversos motivos';
				$errors[201] = 'Tarjeta caducada. Orden de retirar la tarjeta';
				$errors[202] = 'Tarjeta bloqueada por el banco emisor. Orden de retirar la tarjeta';
				$errors[290] = 'Denegada por diversos motivos. Orden de retirar la tarjeta';
				$errors[909] = 'Error de sistema';
				$errors[912] = 'Centro resolutor no disponible';
				$errors[913] = 'Recibido mensaje duplicado';
				$errors[949] = 'Fecha de caducidad de la tarjeta errónea';
				$errors[9111] = 'Banco emisor de la tarjeta no responde';
				$errors[9093] = 'Número de tarjeta inexistente';
				$errors[9112] = 'Número de tarjeta inexistente';
				//Transacción denegada
				$result = $ds_response;
				$deserror = $errors[$ds_response];
				$this->log->write("Pago NOK: " . $result . " -> " . $deserror);
			}
		}
	}

	private function _get_clave()
	{
		switch ($this->config->get('payment_redsys_env')) {
			case 1:
			case 2:
				$seed = $this->merchant_clave_pruebas;
				break;
			default:
				$seed = $this->merchant_clave_real;
				break;
		}
		return $seed;
	}
}