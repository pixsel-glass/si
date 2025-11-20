<?php 
class ControllerExtensionModuleTelnotification extends Controller {

	public function index(){
		/*load model file*/
		$this->load->model('extension/module/telnotification');
		// load language-file
		$this->load->language('extension/module/telnotification');

		$this->document->setTitle($this->language->get('heading_title'));

		// save module-settings, when user click 'Save'
		if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) { 
			// call method from model file for save settings
			$this->model_extension_module_telnotification->setSettings($this->request->post);
			// exit from settings with alert success-message
			$this->session->data['success'] = $this->language->get('text_success');
			// doing redirect to list extensions
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		//If there is an error, set the error flag to $data
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		
		$data = array();

		// Check bot_id::string
		if(isset($this->request->post['module_telnotification_bot_id'])){
			$data['bot_id'] = $this->request->post['module_telnotification_bot_id'];
		} else {
			$data['bot_id'] = $this->config->get('module_telnotification_bot_id');
		}

		// Check recipients::array (Array(['name']=>'chat_id'))
		$data['recipients'] = array();
		if(isset($this->request->post['module_telnotification_recipients'])){
			$data['recipients'] = $this->request->post['module_telnotification_recipients'];
		} else {
			$data['recipients'] = json_decode($this->config->get('module_telnotification_recipients'), true);
		}

		//check status module
		if(isset($this->request->post['module_telnotification_status'])){
			$data['module_telnotification_status'] = $this->request->post['module_telnotification_status'];
		} else {
			$data['module_telnotification_status'] = $this->config->get('module_telnotification_status');
		}


		// New order
		if(isset($this->request->post['module_telnotification_neworder'])){
			$data['module_telnotification_neworder'] = $this->request->post['module_telnotification_neworder'];
		} else {
			$data['module_telnotification_neworder'] = $this->config->get('module_telnotification_neworder');
		}
		if(isset($this->request->post['module_telnotification_message'])){
			$data['module_telnotification_message'] = $this->request->post['module_telnotification_message'];
		} else {
			$data['module_telnotification_message'] = $this->config->get('module_telnotification_message');
		}
		// Quick order
		if(isset($this->request->post['module_telnotification_quickorder'])){
			$data['module_telnotification_quickorder'] = $this->request->post['module_telnotification_quickorder'];
		} else {
			$data['module_telnotification_quickorder'] = $this->config->get('module_telnotification_quickorder');
		}
		if(isset($this->request->post['module_telnotification_quickorder_message'])){
			$data['module_telnotification_quickorder_message'] = $this->request->post['module_telnotification_quickorder_message'];
		} else {
			$data['module_telnotification_quickorder_message'] = $this->config->get('module_telnotification_quickorder_message');
		}
		// Callback
		if(isset($this->request->post['module_telnotification_callback'])){
			$data['module_telnotification_callback'] = $this->request->post['module_telnotification_callback'];
		} else {
			$data['module_telnotification_callback'] = $this->config->get('module_telnotification_callback');
		}
		if(isset($this->request->post['module_telnotification_callback_message'])){
			$data['module_telnotification_callback_message'] = $this->request->post['module_telnotification_callback_message'];
		} else {
			$data['module_telnotification_callback_message'] = $this->config->get('module_telnotification_callback_message');
		}
		// New user
		if(isset($this->request->post['module_telnotification_newuser'])){
			$data['module_telnotification_newuser'] = $this->request->post['module_telnotification_newuser'];
		} else {
			$data['module_telnotification_newuser'] = $this->config->get('module_telnotification_newuser');
		}
		if(isset($this->request->post['module_telnotification_newuser_message'])){
			$data['module_telnotification_newuser_message'] = $this->request->post['module_telnotification_newuser_message'];
		} else {
			$data['module_telnotification_newuser_message'] = $this->config->get('module_telnotification_newuser_message');
		}
		// Contact page
		if(isset($this->request->post['module_telnotification_contact'])){
			$data['module_telnotification_contact'] = $this->request->post['module_telnotification_contact'];
		} else {
			$data['module_telnotification_contact'] = $this->config->get('module_telnotification_contact');
		}
		if(isset($this->request->post['module_telnotification_contact_message'])){
			$data['module_telnotification_contact_message'] = $this->request->post['module_telnotification_contact_message'];
		} else {
			$data['module_telnotification_contact_message'] = $this->config->get('module_telnotification_contact_message');
		}
		// Order status change
		if(isset($this->request->post['module_telnotification_orderstatus'])){
			$data['module_telnotification_orderstatus'] = $this->request->post['module_telnotification_orderstatus'];
		} else {
			$data['module_telnotification_orderstatus'] = $this->config->get('module_telnotification_orderstatus');
		}
		if(isset($this->request->post['module_telnotification_orderstatus_message'])){
			$data['module_telnotification_orderstatus_message'] = $this->request->post['module_telnotification_orderstatus_message'];
		} else {
			$data['module_telnotification_orderstatus_message'] = $this->config->get('module_telnotification_orderstatus_message');
		}
		// Price level change
		if(isset($this->request->post['module_telnotification_pricelevel'])){
			$data['module_telnotification_pricelevel'] = $this->request->post['module_telnotification_pricelevel'];
		} else {
			$data['module_telnotification_pricelevel'] = $this->config->get('module_telnotification_pricelevel');
		}
		if(isset($this->request->post['module_telnotification_pricelevel_message'])){
			$data['module_telnotification_pricelevel_message'] = $this->request->post['module_telnotification_pricelevel_message'];
		} else {
			$data['module_telnotification_pricelevel_message'] = $this->config->get('module_telnotification_pricelevel_message');
		}

		//Set Breadcrumbs

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/butik_fastorder', 'user_token=' . $this->session->data['user_token'], true)
        );

		//Set handlers for buttons 'action' and 'cancel'

		$data['action'] = $this->url->link('extension/module/telnotification', 'user_token=' . $this->session->data['user_token']);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']);

		//load controllers header, footer and columns
		$data['header'] = $this->load->controller('common/header');
    	$data['column_left'] = $this->load->controller('common/column_left');
    	$data['column_right'] = $this->load->controller('common/column_right');
    	$data['footer'] = $this->load->controller('common/footer');
 
    	$this->response->setOutput($this->load->view('extension/module/telnotification', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/account')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('extension/module/telnotification');

		$data = array();
		$data['module_telnotification_neworder'] = 1;
		$data['module_telnotification_quickorder'] = 1;
		$data['module_telnotification_callback'] = 1;
		$data['module_telnotification_newuser'] = 1;
		$data['module_telnotification_contact'] = 1;
		$data['module_telnotification_orderstatus'] = 1;
		$data['module_telnotification_pricelevel'] = 1;

		$this->model_extension_module_telnotification->setSettings($data);
	}

}