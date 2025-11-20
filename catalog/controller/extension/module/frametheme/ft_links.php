<?php

class ControllerExtensionModuleFramethemeFTLinks extends Controller {

	public function index($pos = 'header') {



		// $this->load->model('tool/image');



		$this->load->model('setting/setting');



		$ft_settings = array();

		$ft_settings = $this->model_setting_setting->getSetting('theme_frame', $this->config->get('config_store_id'));

		$language_id = $this->config->get('config_language_id');



		if (isset($ft_settings['t1_main_menu_toggle']) && $ft_settings['t1_main_menu_toggle']){

			$data['links_status'] = $ft_settings['t1_main_menu_toggle'];

		} else {

			$data['links_status'] = false;

		}



		if (isset($ft_settings['t1_main_menu_item']) && $ft_settings['t1_main_menu_item']){

			$links = $ft_settings['t1_main_menu_item'];

		} else {

			$links = array();

		}



		if (!empty($links)){

			foreach ($links as $key => $value) {

				$sorted_links[$key] = $value['sort'];

			}

			array_multisort($sorted_links, SORT_ASC, $links);

		}

		$data['links'] = array();
		// print_r($links);
		foreach ($links as $link) {
			//if (!empty($link['link'][$language_id]) && substr($this->session->data['language'], 0, 2) == 'ro') {
				// $link_lng = $link['link'][$language_id];
			//	$link_lng = $link['link'][2];
			//} else {
				if (substr($this->session->data['language'], 0, 2) == 'en') {
					$link_lng = 'eng/'.$link['link'][2];
				} else if (substr($this->session->data['language'], 0, 2) == 'es') {
					$link_lng = ''.$link['link'][2];
				} else if (substr($this->session->data['language'], 0, 2) == 'ru') {
					$link_lng = 'ru/'.$link['link'][2];
				} else {
					$link_lng = substr($this->session->data['language'], 0, 2).'/'.$link['link'][2];
				}
			//}
			// $link_lng = $link['link'][2];
			$data['links'][] = array(
				'title'        => html_entity_decode($link['title'][$language_id], ENT_QUOTES, 'UTF-8'),
				// 'href'         => $link['link'][$language_id],
				'href'         => $link_lng,
				'sort'         => $link['sort'],
			);
		}

		if($pos == 'footer'){
			return $this->load->view('extension/module/frametheme/ft_links_footer', $data);
		} else {
			return $this->load->view('extension/module/frametheme/ft_links', $data);
		}
	}

	public function footer_links() {
		return $this->index('footer');
	}
}

