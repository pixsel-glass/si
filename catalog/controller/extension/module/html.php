<?php
class ControllerExtensionModuleHTML extends Controller {
	public function index($setting) {
		$this->load->model('tool/image');

		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['heading_title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');
			$data['html'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');
			// $data['image'] = (isset($setting['module_description'][$this->config->get('config_language_id')]['image']) ? $setting['module_description'][$this->config->get('config_language_id')]['image'] : '');
			$data['image'] = (isset($setting['module_description'][$this->config->get('config_language_id')]['image']) ? $this->model_tool_image->resizeWc($setting['module_description'][$this->config->get('config_language_id')]['image'], 326, 183) : '');

			return $this->load->view('extension/module/html', $data);
		}
	}
}