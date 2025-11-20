<?php

// *	@source		See SOURCE.txt for source and other copyright.

// *	@license	GNU General Public License version 3; see LICENSE.txt



class ControllerAccountManagerbuh extends Controller {

	public function index() {

		if (!$this->customer->isLogged()) {

			$this->session->data['redirect'] = $this->url->link('account/manager_buh', '', true);



			$this->response->redirect($this->url->link('account/login', '', true));

		}



		$this->load->language('account/order');



		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->setRobots('noindex,follow');



		$url = '';



		if (isset($this->request->get['page'])) {

			$url .= '&page=' . $this->request->get['page'];

		}



		$data['breadcrumbs'] = array();



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('text_home'),

			'href' => $this->url->link('common/home')

		);



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('text_account'),

			'href' => $this->url->link('account/account', '', true)

		);



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('heading_title'),

			'href' => $this->url->link('account/order', $url, true)

		);

		if (isset($this->request->get['page'])) {

			$page = (int)$this->request->get['page'];

		} else {

			$page = 1;

		}


		$data['current_language_arr'] = explode("-", $this->session->data['language']);
		$data['current_language'] = $current_language_arr[0];


		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();



		$data['orders'] = array();



		$this->load->model('account/order');
		$this->load->model('account/customer');



		$order_total = $this->model_account_order->getManagerTotalOrders();


		$results = $this->model_account_order->getManagerOrders(($page - 1) * 10, 10);

		foreach ($results as $result) {

			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);

			$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

			$client_info = $this->model_account_customer->getCustomer($result['customer_id']);

			$order_quantity = 0;
			$order_quantity_query = $this->db->query("SELECT quantity FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . $result['order_id'] . "'");
			foreach ($order_quantity_query->rows as $oq) {
				$order_quantity = $order_quantity + $oq['quantity'];
			}

			if (!empty($client_info)) {
				if ($client_info['customer_type'] == 1 && !empty($client_info['company_name'])) {
					$customer_name = $client_info['company_name'] . '<br>' . $result['firstname'] . ' ' . $result['lastname'];
				} else {
					$customer_name = $result['firstname'] . ' ' . $result['lastname'];
				}
			} else {
				$customer_name = $result['firstname'] . ' ' . $result['lastname'];
			}

			$data['orders'][] = array(

				'order_id'			    => $result['order_id'],

				'name'			        => $customer_name,

				'status'			    => $result['status'],

				'order_status_id'	    => $result['order_status_id'],

				'country_id'			=> $result['shipping_country_id'],

				'date_added'			=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),

				// 'products'			    => ($product_total + $voucher_total),
				'products'			    => $order_quantity,

				'total'			        => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),

				'tax_total'		        => $this->currency->format($this->tax->calc_tax($result['total']), $result['currency_code'], $result['currency_value']),

				'view'			        => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], true),

				'pdf_link'			    => $result['infakt_pdf'],

	            'infakt_need'           => $result['infakt_need'],

	            'infakt_no'             => $result['infakt_no'],

	            'infakt_pdf'            => $result['infakt_pdf'],

	            'infakt_number'         => $result['infakt_number'],

	            'infakt_language'       => $result['infakt_language'],

	            'infakt_currency'       => $result['infakt_currency'],

	            'infakt_pmethod'        => $result['infakt_pmethod'],

	            'infakt_pmethod_ba'     => $result['infakt_pmethod_ba'],

	            'infakt_pmethod_bn'     => $result['infakt_pmethod_bn'],

	            'infakt_vat'            => $result['infakt_vat'],

	            'infakt_nip'            => $result['infakt_nip'],

	            'infakt_privat_faktyre' => $result['infakt_privat_faktyre'],

	            'order_my_sklad'		=> $result['order_my_sklad'],
	            'order_my_sklad_no'		=> $result['order_my_sklad_no'],

	            'acode'					=> (isset($client_info['customer_my_sklad']) ? $client_info['customer_my_sklad'] : '')

			);

		}



		$pagination = new Pagination();

		$pagination->total = $order_total;

		$pagination->page = $page;

		$pagination->limit = 10;

		$pagination->url = $this->url->link('account/manager_buh', 'page={page}', true);



		$data['pagination'] = $pagination->render();



		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));



		$data['continue'] = $this->url->link('account/manager_buh', '', true);



		$data['column_left'] = $this->load->controller('common/column_left');

		$data['column_right'] = $this->load->controller('common/column_right');

		$data['content_top'] = $this->load->controller('common/content_top');

		$data['content_bottom'] = $this->load->controller('common/content_bottom');

		$data['footer'] = $this->load->controller('common/footer');

		$data['header'] = $this->load->controller('common/header');



		$this->response->setOutput($this->load->view('account/manager_buh', $data));

	}


	public function export_info() {
		if ( (isset($this->request->post['date_start'])) && (isset($this->request->post['date_end'])) ) {
			$date_start = $this->request->post['date_start'];
			$date_end = $this->request->post['date_end'];

			$orders_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE (date_added BETWEEN '" . date('Y-m-d 00:00:01', strtotime($date_start)) . "' AND '" . date('Y-m-d 23:59:59', strtotime($date_end)) . "') AND (`infakt_number`<>'') AND `infakt_number` IS NOT NULL");
			$orders = $orders_query->rows;

			$orders_brak_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE (date_added BETWEEN '" . date('Y-m-d 00:00:01', strtotime($date_start)) . "' AND '" . date('Y-m-d 23:59:59', strtotime($date_end)) . "') AND ((`infakt_number`='') OR `infakt_number` IS NULL)");
			$orders_brak = $orders_brak_query->rows;

			$area_polysk = 0;
			$area_mat = 0;

			$area_polysk_brak = 0;
			$area_mat_brak = 0;

			$infakt_noms = array();

			foreach ($orders as $order) {
				$infakt_noms["uuids"][] = $order['infakt_no'];

				$orders_products_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . $order['order_id'] . "'");
				$orders_products = $orders_products_query->rows;

				foreach ($orders_products as $oproduct) {
					$product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . $oproduct['product_id'] . "'");

					$product = $product_query->row;

					if (substr($oproduct['model'], -1) == 'G') {
						$area_polysk = $area_polysk+($product['pixsel_area']*$oproduct['quantity']);
					}

					if (substr($oproduct['model'], -1) == 'M') {
						$area_mat = $area_mat+($product['pixsel_area']*$oproduct['quantity']);
					}
				}
			}

			foreach ($orders_brak as $order_brak) {
				$orders_brak_products_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . $order_brak['order_id'] . "'");
				$orders_brak_products = $orders_brak_products_query->rows;

				foreach ($orders_brak_products as $oproduct_brak) {
					$product_brak_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . $oproduct_brak['product_id'] . "'");

					$product_brak = $product_brak_query->row;

					if (substr($oproduct_brak['model'], -1) == 'G') {
						$area_polysk_brak = $area_polysk_brak+($product_brak['pixsel_area']*$oproduct_brak['quantity']);
					}

					if (substr($oproduct_brak['model'], -1) == 'M') {
						$area_mat_brak = $area_mat_brak+($product_brak['pixsel_area']*$oproduct_brak['quantity']);
					}
				}
			}

			// infakt download link
			$curl = curl_init();
			$infakt_uuids = implode(',', $infakt_noms);
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://api.infakt.pl:443/api/v3/async/incomes/pdf_many.json',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => json_encode($infakt_noms),
			  CURLOPT_HTTPHEADER => array(
				'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331',
				'Content-Type: application/json'
			  ),
			));
			$response = curl_exec($curl);	
			// print_r($response);	
			// echo json_encode($infakt_noms);
			curl_close($curl);
			$response_arr = json_decode($response, true);
			// $infakt_url = 'https://api.infakt.pl/api/v3/async/incomes/pdf_many/' . $response_arr['download_token'] . '.json';
			$infakt_url = $response_arr['download_token'];
			// infakt download link

			$return = array(number_format(round($area_polysk, 1), 1), number_format(round($area_mat, 1), 1), number_format(round($area_polysk_brak, 1), 1), number_format(round($area_mat_brak, 1), 1), $infakt_url);

			echo json_encode($return, true);

		} else {
			return new Action('error/not_found');
		}
	}

	public function infaktAllPdf() {
		set_time_limit (60);
		ini_set('memory_limit', '5512M');

		if ( isset($this->request->get['token']) ) {
			$curl = curl_init();

			curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.infakt.pl/api/v3/async/incomes/pdf_many/' . $this->request->get['token'] . '.json',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331'
			),
			));
			
			$response = curl_exec($curl);
			// print_r($response); exit;
			curl_close($curl);

			$response_arr = json_decode($response, true);
			// print_r($response_arr); exit;
			// echo $response_arr['error']; exit;
			if (!isset($response_arr['errors'])) {
				// ob_clean();

				header("Content-type: application/pdf");
				echo $response;
			} else {
				$error = 1;
				while ($error == 1) {
					$curl = curl_init();

					curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://api.infakt.pl/api/v3/async/incomes/pdf_many/' . $this->request->get['token'] . '.json',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array(
						'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331'
					),
					));
					
					$response = curl_exec($curl);
					curl_close($curl);
		
					$response_arr = json_decode($response, true);
					if (!isset($response_arr['errors'])) {
						// ob_clean();

						header("Content-type: application/pdf");
						echo $response;

						$error = 0;
					} else {
						sleep(10);

						$error = 1;
					}
				}
			}
		} else {
			echo 'Error';
		}
	}
		
	public function export() {
		if ( (isset($this->request->get['date_start'])) && (isset($this->request->get['date_end'])) ) {
			$this->load->model('account/customer');
			$date_start = $this->request->get['date_start'];
			$date_end = $this->request->get['date_end'];
			$orders_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE (date_added BETWEEN '" . date('Y-m-d 00:00:01', strtotime($date_start)) . "' AND '" . date('Y-m-d 23:59:59', strtotime($date_end)) . "') AND (`infakt_number`<>'') AND `infakt_number` IS NOT NULL");
			$orders = $orders_query->rows;
			$excel_arr = array();
			// PIXSEL PRICE
			$pprice_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pixsel_material`");
			$pprice = $pprice_query->rows;
			$all_rw = 0.000;
			$all_pw = 0.000;
			$all_material_price = 0.00;
			$all_vat_price = 0.00;
			$all_brutto = 0.00;
			$all_wydana = 0;
			// BOX
			$box1quantity = 0;
			$box2quantity = 0;
			$box3quantity = 0;
			$box4quantity = 0;
			$all_box_netto = 0.00;
			$all_box_vat = 0.00;
			$all_box_brutto = 0.00;
			$this->load->model('setting/setting');
			$settings = $this->model_setting_setting->getSetting('manager_buh');
			$sebes = $settings['manager_buh_sebes'];
			$rshpr = $settings['manager_buh_rshpr'];
			$number_line = 1;
			foreach($orders as $order) {
				// CLIENT
				$client_info = $this->model_account_customer->getCustomer($order['customer_id']);
				if (!empty($client_info)) {
					if ($client_info['customer_type'] == 1 && !empty($client_info['company_name'])) {
						$customer_name = $client_info['company_name'];
					} else {
						$customer_name = $order['firstname'] . ' ' . $order['lastname'];
					}
				} else {
					$customer_name = $order['firstname'] . ' ' . $order['lastname'];
				}
				// PRODUCTS
				$orders_products_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . $order['order_id'] . "'");
				$orders_products = $orders_products_query->rows;
				foreach ($orders_products as $oproduct) {
					$product_sku_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option_value` WHERE `pixsel_sku` = '" . $oproduct['model'] . "'")->row;
					$oproduct['product_id'] = $product_sku_query['product_id'];
					
					$product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . $oproduct['product_id'] . "'");
					$product = $product_query->row;
					$wydana = $oproduct['quantity'];
					if (substr($oproduct['model'], -1) == 'G') {
						$material = 'połysk';
					}
					if (substr($oproduct['model'], -1) == 'M') {
						$material = 'mat';
					}
					$material_price = number_format($sebes, 3);
					if ($rshpr > 0) {
						$area_plus = ($product['pixsel_area']/100)*$rshpr;
						$area = $product['pixsel_area']+$area_plus;
					} else {
						$area = $product['pixsel_area'];
					}
					$product_desc_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = '" . $oproduct['product_id'] . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "'");
					$product_desc_en_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = '" . $oproduct['product_id'] . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "'");
					$description = $product_desc_query->row;
					$description_en = $product_desc_en_query->row;
					$product_desc_sku_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = '" . $product_sku_query['product_id'] . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "'")->row;
					$name = (!empty($description['name']) ? $description['name'] : $product_desc_sku_query['name']);
					$faktura = $order['infakt_number'];
					$date = date("d.m.Y", strtotime($order['date_added']));
					$rw = number_format($area, 3)*$wydana;
					$pw = number_format($area, 3)*$wydana;
					$netto = number_format($material_price*$area, 3)*$wydana;
					$vat = 23;
					$vat_price = number_format(($netto/100)*23, 3);
					$brutto = ($netto+$vat_price);
					$jedn = 'szt';
					// BOX
					$product_box_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_box` WHERE `product_id` = '" . $oproduct['product_id'] . "' AND `order_id` = '" . $order['order_id'] . "'");
					$box_id = $product_box_query->row['pixsel_box_id'];
					$box_quantity = (!empty($product_box_query->row['quantity']) ? $product_box_query->row['quantity'] : 0);
					$box_price = number_format(str_replace(',', '.', $settings['manager_buh_box_' . $product_box_query->row['pixsel_box_id'] . '_price']), 3);
					$box_qprice = number_format($box_price, 3)*number_format($box_quantity, 3);
					$box_vat = number_format(($box_qprice/100)*23, 3);
					$box_fprice = number_format($box_qprice, 3)+number_format($box_vat, 3);
					if ($box_id == 1) {
						$box1quantity = $box1quantity + $box_quantity;
					} else if ($box_id == 2) {
						$box2quantity = $box2quantity + $box_quantity;
					} else if ($box_id == 3) {
						$box3quantity = $box3quantity + $box_quantity;
					} else if ($box_id == 4) {
						$box4quantity = $box4quantity + $box_quantity;
					}
					$all_box_netto = $all_box_netto+$box_qprice;
					$all_box_vat = $all_box_vat+$box_vat;
					$all_box_brutto = $all_box_brutto+$box_fprice;
					// SUMMA
					$all_rw = $all_rw + $rw;
					$all_pw = $all_pw + $pw;
					$all_material_price = $all_material_price+$material_price;
					$all_vat_price = $all_vat_price+$vat_price;
					$all_brutto = $all_brutto+$brutto;
					$all_wydana = $all_wydana+$wydana;
					$excel_arr[] = array($number_line, $name, $material, $customer_name, $faktura, $date, $rw, $pw, $material_price, number_format($netto, 3, ',', ''), $vat, number_format($vat_price, 3, ',', ''), number_format($brutto, 3, ',', ''), $wydana, $jedn, $box_id, $box_quantity, number_format($box_qprice, 3, ',', ''), $box_vat, $box_fprice);
					$number_line++;
				}
			}
			// WRITE EXCEL
			require_once(DIR_SYSTEM.'library/PHPExcel/PHPExcel.php');
			$objPHPExcel = PHPExcel_IOFactory::load(DIR_SYSTEM.'library/PHPExcel/wablon_faktura.xlsx');
			$objPHPExcel->setActiveSheetIndex(0);
			$row = 9;
			// Pre-apply styles to all rows based on the number of data entries
			$total_rows = count($excel_arr);
			for ($i = 0; $i < $total_rows; $i++) {
				$current_row = $row + $i;
				// Copy styles from template row (row 9) for columns A:S and U:AA
				$objPHPExcel->getActiveSheet()->duplicateStyle(
					$objPHPExcel->getActiveSheet()->getStyle('A9:S9'),
					'A' . $current_row . ':S' . $current_row
				);
				$objPHPExcel->getActiveSheet()->duplicateStyle(
					$objPHPExcel->getActiveSheet()->getStyle('U9:AA9'),
					'U' . $current_row . ':AA' . $current_row
				);
				$objPHPExcel->getActiveSheet()->getStyle('A' . $current_row . ':S' . $current_row)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->mergeCells("B" . $current_row . ":F" . $current_row);
			}
			// Write data to rows
			foreach ($excel_arr as $exdata) {
				$objPHPExcel->getActiveSheet()
					->setCellValue('A' . $row, $exdata[0])
					->setCellValue('B' . $row, $exdata[1])
					->setCellValue('G' . $row, $exdata[2])
					->setCellValue('H' . $row, $exdata[3])
					->setCellValue('I' . $row, $exdata[4])
					->setCellValue('J' . $row, $exdata[5])
					->setCellValue('K' . $row, $exdata[6])
					->setCellValue('L' . $row, $exdata[7])
					->setCellValue('M' . $row, $exdata[8])
					->setCellValue('N' . $row, $exdata[9])
					->setCellValue('O' . $row, $exdata[10])
					->setCellValue('P' . $row, $exdata[11])
					->setCellValue('Q' . $row, $exdata[12])
					->setCellValue('R' . $row, $exdata[13])
					->setCellValue('S' . $row, $exdata[14])
					->setCellValue('U' . $row, ($exdata[15] == 1 ? $exdata[16] : 0))
					->setCellValue('V' . $row, ($exdata[15] == 2 ? $exdata[16] : 0))
					->setCellValue('W' . $row, ($exdata[15] == 3 ? $exdata[16] : 0))
					->setCellValue('X' . $row, ($exdata[15] == 4 ? $exdata[16] : 0))
					->setCellValue('Y' . $row, $exdata[17])
					->setCellValue('Z' . $row, $exdata[18])
					->setCellValue('AA' . $row, $exdata[19]);
				$row++;
			}
			$row = $row + 2;
			// Write summary data with styles
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('O2', date("m/Y", strtotime($date_start)))
				->setCellValue('B' . $row, 'Summa')
				->setCellValue('K' . $row, $all_rw)
				->setCellValue('L' . $row, $all_pw)
				->setCellValue('M' . $row, $all_material_price)
				->setCellValue('P' . $row, $all_vat_price)
				->setCellValue('Q' . $row, $all_brutto)
				->setCellValue('R' . $row, $all_wydana)
				->setCellValue('U' . $row, $box1quantity)
				->setCellValue('V' . $row, $box2quantity)
				->setCellValue('W' . $row, $box3quantity)
				->setCellValue('X' . $row, $box4quantity)
				->setCellValue('Y' . $row, $all_box_netto)
				->setCellValue('Z' . $row, $all_box_vat)
				->setCellValue('AA' . $row, $all_box_brutto);
			// Merge B-F for "Summa" in total row
			$objPHPExcel->getActiveSheet()->mergeCells('B' . $row . ':F' . $row);
			// Apply Arial 12 bold to total row (except O2)
			$total_cells = ['B' . $row, 'K' . $row, 'L' . $row, 'M' . $row, 'P' . $row, 'Q' . $row, 'R' . $row, 'U' . $row, 'V' . $row, 'W' . $row, 'X' . $row, 'Y' . $row, 'Z' . $row, 'AA' . $row];
			foreach ($total_cells as $cell) {
				$objPHPExcel->getActiveSheet()->getStyle($cell)->getFont()
					->setName('Arial')
					->setSize(12)
					->setBold(true);
			}
			// Merge A-S in the row below total and set yellow background, explicit borders, and center alignment
			$row++;
			$objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':S' . $row);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getFill()
				->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				->getStartColor()->setRGB('FFFF00');
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getBorders()->getTop()
				->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getBorders()->getBottom()
				->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getBorders()->getLeft()
				->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getBorders()->getRight()
				->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
				->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			// Next row: yellow background, explicit borders, center alignment, merge cells, and set labels
			$row++;
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getFill()
				->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				->getStartColor()->setRGB('FFFF00');
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getBorders()->getTop()
				->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getBorders()->getBottom()
				->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getBorders()->getLeft()
				->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getBorders()->getRight()
				->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':S' . $row)->getAlignment()
				->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
				->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);
			$objPHPExcel->getActiveSheet()->mergeCells('C' . $row . ':D' . $row);
			$objPHPExcel->getActiveSheet()->mergeCells('F' . $row . ':M' . $row);
			$objPHPExcel->getActiveSheet()
				->setCellValue('A' . $row, 'Wystawił')
				->setCellValue('C' . $row, 'Zatwierdził')
				->setCellValue('E' . $row, 'Wydał')
				->setCellValue('F' . $row, 'Data')
				->setCellValue('R' . $row, 'Odebrał');
			// Set row heights to auto
			foreach($objPHPExcel->getActiveSheet()->getRowDimensions() as $rd) {
				$rd->setRowHeight(-1);
			}
			// Output headers and file
			header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header("Content-Disposition: attachment; filename=" . $date_start . "-" . $date_end . ".xlsx");
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save('php://output');
			exit();
		} else {
			return new Action('error/not_found');
		}
	}

    public function export_brak() {
		if ( (isset($this->request->get['date_start'])) && (isset($this->request->get['date_end'])) ) {
			$this->load->model('account/customer');

			$date_start = $this->request->get['date_start'];
			$date_end = $this->request->get['date_end'];

			$orders_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE (date_added BETWEEN '" . date('Y-m-d 00:00:01', strtotime($date_start)) . "' AND '" . date('Y-m-d 23:59:59', strtotime($date_end)) . "') AND (`infakt_number` = '' OR `infakt_number` IS NULL)");

			$orders = $orders_query->rows;

			$excel_arr = array();

			// PIXSEL PRICE
			$pprice_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pixsel_material`");
			$pprice = $pprice_query->rows;

			$all_rw = 0.000;
			// $all_pw = 0.000;
			// $all_material_price = 0.00;
			// $all_vat_price = 0.00;
			// $all_brutto = 0.00;
			$all_wydana = 0;

			$this->load->model('setting/setting');
			$settings = $this->model_setting_setting->getSetting('manager_buh');
            $sebes = $settings['manager_buh_sebes'];
            $rshpr = $settings['manager_buh_rshpr'];
			// $sebes =  $this->request->get['sebes'];
			// $rshpr =  $this->request->get['rshpr'];

			$number_line = 1;

			foreach($orders as $order) {
				// CLIENT
				$client_info = $this->model_account_customer->getCustomer($order['customer_id']);
				if (!empty($client_info)) {
					if ($client_info['customer_type'] == 1 && !empty($client_info['company_name'])) {
						$customer_name = $client_info['company_name'];
					} else {
						$customer_name = $order['firstname'] . ' ' . $order['lastname'];
					}
				} else {
					$customer_name = $order['firstname'] . ' ' . $order['lastname'];
				}

				// PRODUCTS
				$orders_products_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . $order['order_id'] . "'");
				$orders_products = $orders_products_query->rows;
				foreach ($orders_products as $oproduct) {
					$product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . $oproduct['product_id'] . "'");

					$product = $product_query->row;

					$wydana = $oproduct['quantity'];

					if (substr($oproduct['model'], -1) == 'G') {
						$material = 'połysk';
					}

					if (substr($oproduct['model'], -1) == 'M') {
						$material = 'mat';
					}

					// $material_price = number_format($sebes, 3)*$wydana;

					if ($rshpr > 0) {
						$area_plus = ($product['pixsel_area']/100)*$rshpr;
						$area = $product['pixsel_area']+$area_plus;
					} else {
						$area = $product['pixsel_area'];
					}

					$product_desc_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = '" . $oproduct['product_id'] . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "'");
					$description = $product_desc_query->row;

					$name = $description['name'];

					// $faktura = $order['infakt_number'];

					$date = date("d.m.Y", strtotime($order['date_added']));

					$rw = number_format($area, 3)*$wydana;
					// $pw = number_format($area, 3)*$wydana;

					// $netto = number_format($material_price*$area, 3)*$wydana;

					// $vat = 23;

					// $vat_price = number_format(($netto/100)*23, 3)*$wydana;

					// $brutto = ($netto+$vat_price)*$wydana;

					$jedn = 'szt';

					// SUMMA
					$all_rw = $all_rw + $rw;
					// $all_pw = $all_pw + $pw;
					// $all_material_price = $all_material_price+$material_price;
					// $all_vat_price = $all_vat_price+$vat_price;
					// $all_brutto = $all_brutto+$brutto;
					$all_wydana = $all_wydana+$wydana;

					$excel_arr[] = array($number_line, $name, $material, $customer_name, $date, $rw, $wydana, $jedn);

					$number_line++;
				}
			}

			// WRITE EXCEL
    	    require_once(DIR_SYSTEM.'library/PHPExcel/PHPExcel.php');
 	        $objPHPExcel = PHPExcel_IOFactory::load(DIR_SYSTEM.'library/PHPExcel/wablon_faktura_brak.xlsx');

			$objPHPExcel->setActiveSheetIndex(0);

			$row = 9;
	        foreach ($excel_arr as $exdata) {
	        	// WRAP TEXT
	        	$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':L' . $row)->getAlignment()->setWrapText(true);

	        	$objPHPExcel->getActiveSheet()->mergeCells("B" . (string)$row . ":F" . (string)$row);
	            $objPHPExcel->getActiveSheet()
	            	->setCellValue('A' . (string)$row, $exdata[0])
	                ->setCellValue('B' . (string)$row, $exdata[1])
	                ->setCellValue('G' . (string)$row, $exdata[2])
	                ->setCellValue('H' . (string)$row, $exdata[3])
	                ->setCellValue('I' . (string)$row, $exdata[4])
	                ->setCellValue('J' . (string)$row, $exdata[5])
	                ->setCellValue('K' . (string)$row, $exdata[6])
	                ->setCellValue('L' . (string)$row, $exdata[7]);

	            $row++;
	           	
	           	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row);
	        }

	        $row = $row + 2;

            $objPHPExcel->setActiveSheetIndex(0)
            	->setCellValue('K2', date("m/Y", strtotime($date_start)))
            	->setCellValue('J' . (string)$row, $all_rw)
                ->setCellValue('K' . (string)$row, $all_wydana);

			foreach($objPHPExcel->getActiveSheet()->getRowDimensions() as $rd) { 
			    $rd->setRowHeight(-1); 
			}

			header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header("Content-Disposition: attachment; filename=" . $date_start . "-" . $date_end . "-BRAK.xlsx");
			 
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save('php://output');
			exit();
		} else {
			return new Action('error/not_found');
		}
    }

	public function editBuh() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			$this->load->model('setting/setting');

			$this->load->language('account/order');

			$settings = $this->model_setting_setting->getSetting('manager_buh');

			// print_r($settings);

            $data['sebes'] = $settings['manager_buh_sebes'];
            $data['rshpr'] = $settings['manager_buh_rshpr'];

            $data['boxes'] = $this->getPixselBoxPrices();

            for ($b = 0; $b < count($data['boxes']); $b++) {
            	// echo $settings['manager_buh_box_4_price'];
            	// $data['manager_buh_box_' + $data['boxes']['nummer'] + '_price'] = $settings['manager_buh_box_' + $data['boxes']['nummer'] + '_price'];
            	$data['boxes'][$b]['price'] = $settings['manager_buh_box_' . $data['boxes'][$b]['nummer'] . '_price'];
            }

            // print_r($data['boxes']);

			$this->response->setOutput($this->load->view('account/manager_buhset_modal', $data));
		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
  	}

	public function saveBuh() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			$this->load->model('setting/setting');

			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				$this->model_setting_setting->editSetting('manager_buh', $this->request->post);

	            $this->response->addHeader('Content-Type: application/json');
	            $json = array('success' => 'success');
	            echo json_encode($json);
			}
		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
  	}

  	public function getPixselBoxPrices() {
    	$result = array();
    	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pixsel_box` ORDER BY pixsel_box_id ASC");

    	foreach ($query->rows as $row) {
      		$result[] = array('nummer' => $row['pixsel_box_id'], 'name' => $row['pixsel_box'], 'price' => '');
    	}

    	return $result;
  	}

}