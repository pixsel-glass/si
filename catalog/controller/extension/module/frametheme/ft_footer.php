<?php

class ControllerExtensionModuleFramethemeFTFooter extends Controller {

	public function index() {

		$this->load->language('extension/module/frametheme/ft_global');

		$this->load->language('extension/module/frametheme/ft_footer');

		$mobile_detect = new Mobile_Detect;
		if ( $mobile_detect->isMobile() ) {
			$data['is_mobile'] = 1;
		} else {
			$data['is_mobile'] = 0;
		}

		$data['current_language'] = $this->language->get('code');

		$data['currency'] = $this->load->controller('common/currency/footer_currency');
		$data['ft_hlinks'] = $this->load->controller('extension/module/frametheme/ft_hlinks');

		$data['ft_links_footer'] = $this->load->controller('extension/module/frametheme/ft_links/footer_links');

		$data['languages'] = array();
		$data['language_select'] = $this->session->data['language'];
		$results_languages = $this->model_localisation_language->getLanguages();

		foreach ($results_languages as $result_language) {
			if ($result_language['status']) {
				if (strpos($result_language['code'], $data['current_language']) !== false) {
					$result_language['sort_order'] = 0;
				}
				$data['languages'][] = array(
					'name' => $result_language['name'],
					'code' => $result_language['code'],
					'language_id' => $result_language['language_id'],
					'sort_order' => $result_language['sort_order'],
				);

			}
		}

		usort($data['languages'], function($a, $b) {
		    return $a['sort_order'] <=> $b['sort_order'];
		});


		$this->load->model('setting/setting');



		$ft_settings = array();

		$ft_settings = $this->model_setting_setting->getSetting('theme_frame', $this->config->get('config_store_id'));

		$language_id = $this->config->get('config_language_id');



		if (isset($ft_settings['t1_high_definition_imgs']) && $ft_settings['t1_high_definition_imgs']){

			$hd_imgs = $ft_settings['t1_high_definition_imgs'];

		} else {

			$hd_imgs = false;

		}



		if (isset($ft_settings['t1_pay_icons_toggle']) && $ft_settings['t1_pay_icons_toggle']){

			$data['pay_icons_status'] = $ft_settings['t1_pay_icons_toggle'];

		} else {

			$data['pay_icons_status'] = false;

		}



		if (isset($ft_settings['t1_pay_icons_banner_id']) && $ft_settings['t1_pay_icons_banner_id']){

			$banner_id = $ft_settings['t1_pay_icons_banner_id'];

		} else {

			$banner_id = false;

		}



    if (isset($ft_settings['t1_catalog_mode']) && !empty($ft_settings['t1_catalog_mode'])) {

      $data['catalog_mode'] = $ft_settings['t1_catalog_mode'];

    } else {

      $data['catalog_mode'] = false;

    }





		$this->load->model('catalog/information');



		$data['informations'] = array();



		foreach ($this->model_catalog_information->getInformations() as $result) {

			if ($result['bottom']) {

				$data['informations'][] = array(

					'title' => $result['title'],

					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])

				);

			}

		}


		$data['contact'] = $this->url->link('information/contact');

		$data['return'] = $this->url->link('account/return/add', '', true);

		$data['sitemap'] = $this->url->link('information/sitemap');

		$data['tracking'] = $this->url->link('information/tracking');

		$data['manufacturer'] = $this->url->link('product/manufacturer');

		$data['voucher'] = $this->url->link('account/voucher', '', true);

		$data['affiliate'] = $this->url->link('affiliate/login', '', true);

		$data['special'] = $this->url->link('product/special');

		$data['account'] = $this->url->link('account/account', '', true);

		$data['order'] = $this->url->link('account/order', '', true);

		$data['wishlist'] = $this->url->link('account/wishlist', '', true);

		$data['newsletter'] = $this->url->link('account/newsletter', '', true);



		// $data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time())).$this->language->get('g_theme_powered');
		// $data['powered'] = sprintf($this->language->get('text_powered'), date('Y', time()) . '' . $this->language->get('g_theme_powered'));
		$data['powered'] = sprintf($this->language->get('text_powered'), date('Y', time()));



		$this->load->model('design/banner');

		$this->load->model('tool/image');



		$data['pay_icons'] = array();

		$pay_icons = $this->model_design_banner->getBanner($banner_id);



		foreach ($pay_icons as $pay_icon) {

			if (is_file(DIR_IMAGE . $pay_icon['image'])) {

				$data['pay_icons'][] = array(

					'title' 		=> $pay_icon['title'],

					'link'  		=> $pay_icon['link'],

					'image' 		=> $this->model_tool_image->resize($pay_icon['image'], 100, 56),

					'image2x' 	=> $hd_imgs ? $this->model_tool_image->resize($pay_icon['image'], 100*2, 56*2) : NULL,

					'image3x' 	=> $hd_imgs ? $this->model_tool_image->resize($pay_icon['image'], 100*3, 56*3) : NULL,

					'image4x' 	=> $hd_imgs ? $this->model_tool_image->resize($pay_icon['image'], 100*4, 56*4) : NULL

				);

			}

		}

		return $this->load->view('extension/module/frametheme/ft_footer', $data);

	}

}

