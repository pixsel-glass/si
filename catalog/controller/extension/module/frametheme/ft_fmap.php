<?php
class ControllerExtensionModuleFramethemeFTFmap extends Controller {
	public function index() {
		$this->load->language('extension/module/frametheme/ft_fmap');

		$this->load->model('setting/setting');

		$ft_settings = array();
		$ft_settings = $this->model_setting_setting->getSetting('theme_frame', $this->config->get('config_store_id'));
		$language_id = $this->config->get('config_language_id');

		if (isset($ft_settings['t1_footer_map_toggle']) && $ft_settings['t1_footer_map_toggle']){
			$data['fmap_status'] = $ft_settings['t1_footer_map_toggle'];
		} else {
			$data['fmap_status'] = '';
		}

		if (isset($ft_settings['t1_footer_map_code']) && $ft_settings['t1_footer_map_code']){
			$data['fmap_code'] = html_entity_decode($ft_settings['t1_footer_map_code'], ENT_QUOTES, 'UTF-8');
		} else {
			$data['fmap_code'] = '';
		}

		if (isset($ft_settings['t1_footer_map_geocode']) && $ft_settings['t1_footer_map_geocode']){
			$data['geocode'] = $ft_settings['t1_footer_map_geocode'];
		} else {
			$data['geocode'] = $this->config->get('config_geocode');
		}

		$this->load->model('tool/image');

		$data['map_bg'] = $this->model_tool_image->resize('catalog/frametheme/fmap_bg.png', 1120, 84);
    $data['map_bg_src_holder'] = $this->model_tool_image->resize('catalog/frametheme/src_holder.png', 1120, 84);

		if (isset($this->request->get['get_map_code']) && $this->request->get['get_map_code']) {

			$data['get_map_code'] = true;

			$this->response->setOutput($this->load->view('extension/module/frametheme/ft_fmap', $data));

		} else {

			$data['get_map_code'] = false;

			return $this->load->view('extension/module/frametheme/ft_fmap', $data);
		}
	}
}
