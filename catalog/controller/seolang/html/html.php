<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerSeoLangHtmlHtml', false)) {
class ControllerSeoLangHtmlHtml extends Controller {
	protected $data;
	protected $api_html = false;
	protected $route;
	protected $server_protocol = 'HTTP/1.1';
	protected $widget = 'seolang/html/html';


	public function __construct($registry) {
		parent::__construct($registry);
		if (version_compare(phpversion(), '5.3.0', '<') == true) {
			exit('PHP5.3+ Required');
		}
		$this->data['index'] = false;
	}

	private function html_api() {
		if (!$this->api_html) {
			if (isset($this->registry->get('request')->get['route'])) {
				$this->route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->registry->get('request')->get['route']);
			} else {
				$this->route = 'common/home';
			}
			if (isset($this->route) && stripos($this->route, $this->widget) !== false) {
					$this->api_html = false;
					if (isset($this->registry->get('request')->server['SERVER_PROTOCOL']) && $this->registry->get('request')->server['SERVER_PROTOCOL'] != '') {
						$this->server_protocol = $this->registry->get('request')->server['SERVER_PROTOCOL'];
					} else {
						$this->server_protocol = 'HTTP/1.1';
					}
					header($this->server_protocol . ' ' . '404 Not Found');
					header('Status: 404 Not Found');
					exit();
			}
			$this->api_html = true;
		}
	}



	public function index($data) {
    	$this->html_api();
		return $this
    	->start($data)
    	->load_model()
    	->load_lib()
    	->load_language_get()
    	->load_settings()
		->load_access()
		->load_view_settings()
		->load_view();
	}

    public function get_data() {
    	return $this->data;
    }

    public function start($data) {
        $this->data = $data;
		if (!empty($data)) {
			$this->data = $data;
		}
        $this->data['index'] = true;
		if (!isset($this->data['store_id'])) {
			$this->data['store_id'] = $this->config->get('config_store_id');
		}

        $data_widget = $this->model_seolang_seolang->getSetting($this->data['setting']['name'], $this->data['store_id']);

        $this->data['setting'] = array_merge($this->data['setting'], $data_widget);

    	return $this;
    }

    public function load_model($data = array()) {
    	if (!$this->data['index']) return $this;
		if (!empty($data)) $this->data = $data;
		$this->load->model($this->widget);
    	return $this;
    }
    public function load_lib($data = array()) {
    	if (!$this->data['index']) return $this;
		if (!empty($data)) $this->data = $data;
    	return $this;
    }
    public function load_language_get($data = array()) {
    	if (!$this->data['index']) return $this;
		if (!empty($data)) $this->data = $data;
    	return $this;
    }
    public function load_settings($data = array()) {
    	if (!$this->data['index']) return $this;
		if (!empty($data)) $this->data = $data;
    	return $this;
    }

    public function load_view_settings($data = array()) {
    	if (!$this->data['index']) return $this;
		if (!empty($data)) $this->data = $data;

		$this->data['html'] = html_entity_decode($this->data['setting']['description'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

    	return $this;
    }
	private function load_access($data = array()) {
    	if (!$this->data['index']) return $this;
		if (!empty($data)) $this->data = $data;
    	
		$this->data = $this->checkAccess($this->data);
		
		return $this;
    }

	private function checkAccess($data) {
    	if (!$this->data['index']) return $this->data;
		if (!empty($data)) $this->data = $data;
		$this->data['access_widget'] = false;
		// for all groups check, save queries
		if (!empty($this->data['setting']['access']['customer_groups'])) {
			foreach($this->data['setting']['access']['customer_groups'] as $num => $val) {
				if ($val == -99) {
					$this->data['access_widget'] = true;
					return $this->data;
				}
			}
		}

		$this->data = $this->customer_groups($this->data);

		$setting_customer_groups = $this->data['setting']['access']['customer_groups'];
		$customer_groups = $this->data['customer_groups']['customer_groups'];

		$this->data['customer_intersect'] = array_intersect($setting_customer_groups, $customer_groups);
		
		if (isset($this->data['setting']['access']['customer_groups']) && !empty($this->data['customer_intersect'])) {
			$this->data['access_widget'] = true;
		}
		if (!$this->data['access_widget']) {
			$this->data['index'] = false;	
		}
		return $this->data;
	}

	private function customer_groups($data, $gr = 'customer_groups') {
		$this->data = $data;
	    $customer_order = false;

		if (SC_VERSION > 15) {
			$get_Customer_GroupId = 'getGroupId';
		} else {
			$get_Customer_GroupId = 'getCustomerGroupId';
		}

		$this->data['customer_groups'][$gr] = Array();

		if (is_object($this->customer) &&  $this->customer->isLogged()) {
			$this->data['customer_group_id'] =  $this->customer->$get_Customer_GroupId();
			$this->data['customer_id'] = $this->customer->getId();
			array_push($this->data['customer_groups'][$gr], -1);
		} else {
			$this->data['customer_id'] = 0;
			$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
			// WTF guys?! Why $this->config->get('config_customer_group_id') return false ? This can not be. Who ? What module is doing shit ?
            if ($this->data['customer_group_id'] === false) {
            	$this->load->model('setting/setting');
                $config_data = $this->model_setting_setting->getSetting('config', (int) $this->config->get('config_store_id'));
                if (isset($config_data['config_customer_group_id'])) {
                	$this->data['customer_group_id'] = $config_data['config_customer_group_id'];
                } else {
                	$this->data['customer_group_id'] = 1;
                }
                unset($config_data);
                //x3
                if (!$this->data['customer_group_id']) {
                	$this->data['customer_group_id'] = 1;
                }
            }
		}

		array_push($this->data['customer_groups'][$gr], $this->data['customer_group_id']);

	   	if (!isset($this->data['setting']['complete_status'])) {
			if (!empty($this->data['seolang_settings'])) {
				$this->data['setting']['complete_status'] = $this->data['seolang_settings']['complete_status'];
			} else {
	    		$this->data['setting']['complete_status'] = $this->config->get('config_complete_status_id');
			}
	   	}

       	if (isset($this->request->get['product_id'])) {
			if (is_object($this->customer) &&  $this->customer->isLogged()) {
				$customer_order = $this->model_seolang_html_html->getCustomerOrder((int)$this->data['customer_id'], $this->data['setting']['complete_status'], (int)$this->request->get['product_id']);
			}

			if ($customer_order) {
				array_push($this->data['customer_groups'][$gr], -3);
			}
		}

	   	if (is_object($this->customer) && $this->customer->isLogged() && !$customer_order) {
	   		$customer_order = $this->model_seolang_html_html->getCustomerOrder((int)$this->data['customer_id'], $this->data['setting']['complete_status'], 0);
	   	}

	   	if ($customer_order) {
		   	array_push($this->data['customer_groups'][$gr], -2);
	   	}

	   	return $this->data;
	}

    public function load_view($data = array()) {
    	if (!$this->data['index']) return $this->data;
		if (!empty($data)) $this->data = $data;

		if (!isset($this->data['setting']['template']) || (isset($this->data['setting']['template']) && $this->data['setting']['template'] == '')) {
			$template = 'seolang/' . $this->data['setting']['widget'] . '/' . $this->data['setting']['widget'] . '.tpl';
		} else {
			$template = 'seolang/' . $this->data['setting']['widget'] . '/' . $this->data['setting']['template'];
		}

		$template_info  = pathinfo($template);

       	if (isset($template_info['extension']) && $template_info['extension'] != '') {
       		$template = $template_info['dirname']. '/' . $template_info['filename'];
       	}

        $this->data['template'] = $this->seolanglib->template($template);

    	return $this->data;
    }

}
}
