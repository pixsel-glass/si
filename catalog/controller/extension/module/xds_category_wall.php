<?php
class ControllerExtensionModuleXDSCategoryWall extends Controller {
	public function index($setting) {
		static $module = 0;

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();
		$language_id = $this->config->get('config_language_id');

		$this->load->language('extension/module/xds_category_wall');
		$this->load->language('extension/module/frametheme/ft_global');

		$this->load->model('tool/image');

		$this->load->model('setting/setting');

		$ft_settings = array();
		$ft_settings = $this->model_setting_setting->getSetting('theme_frame', $this->config->get('config_store_id'));

		if (isset($ft_settings['t1_high_definition_imgs']) && $ft_settings['t1_high_definition_imgs']){
			$hd_imgs = $ft_settings['t1_high_definition_imgs'];
		} else {
			$hd_imgs = false;
		}

    if (isset($ft_settings['t1_catalog_page_lazy']) && $ft_settings['t1_catalog_page_lazy']){
			$data['lazyload_imgs'] = $ft_settings['t1_catalog_page_lazy'];
		} else {
			$data['lazyload_imgs'] = false;
		}


    if ($setting['type'] == 0) {
      $this->document->addStyle('catalog/view/theme/' . $this->config->get('theme_frame_directory') . '/javascript/owl-carousel/owl.carousel.min.css');
  		$this->document->addScript('catalog/view/theme/' . $this->config->get('theme_frame_directory') . '/javascript/owl-carousel/owl.carousel.min.js');
    }

    $data['type'] = $setting['type'];
    $data['click_action'] = $setting['click_action'];


		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if ($setting['title']) {
			$data['heading_title'] = $setting['title'][$language_id];
		}

		if ($setting['allcat']) {
			$data['allcat'] = $setting['allcat'];
		}
		if ($setting['catline']) {
			$data['catline'] = $setting['catline'];
		}
		if ($setting['cssclass']) {
			$data['cssclass'] = $setting['cssclass'];
		}

		$data['controls'] = isset($setting['controls']) ? $setting['controls'] : array();

		$data['autoplay'] = $setting['autoplay'];
		$data['autoplay_speed'] = $setting['autoplay_speed'];

		$data['items'] = $setting['items'] ? $setting['items'] : 1;
		$data['responsive_items'] = array();

		$responsive_items = isset($setting['responsive_items']) ? $setting['responsive_items'] : array();
		foreach ($responsive_items as $item) {
			if ($item['breakpoint'] && $item['amount']) {
				$data['responsive_items'][] = array(
					'breakpoint' => $item['breakpoint'],
					'amount' => $item['amount']
				);
			}
		}

    if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

    if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}

		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$categories_list = $setting['category'];

    $categories = array();

		if ($setting['allcat']) {
			/*$categories_list = array();
			$acarr = $this->model_catalog_category->getCategories();
			foreach ($acarr as $cat) {
				$categories_list[] = $cat['category_id'];

				$category_info = $this->model_catalog_category->getCategory($cat['category_id']);
				if ($category_info) {
					$categories[] = $category_info;
				}				
			}*/
			$categories = $this->model_catalog_category->getCategories();
		} else {
	    foreach ($categories_list as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if ($category_info) {
					$categories[] = $category_info;
				}
			}
		}

		foreach ($categories as $category) {
			/*$children_data = array();

				//if ($category['category_id'] == $data['category_id']) {	}

				$children = $this->model_catalog_category->getCategories($category['category_id']);

        $children = array_slice($children, 0, $setting['limit']);

				foreach($children as $child) {
					$filter_data = array('filter_category_id' => $child['category_id'], 'filter_sub_category' => true);

					$children_data[] = array(
						'category_id' => $child['category_id'],
						// 'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'name' => $child['name'],
						'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}*/




			$filter_data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			);

      if ($category['image']) {
				// $image_file = 'cachewebp/' . utf8_substr($category['image'], 0, utf8_strrpos($category['image'], '.')) . '-' . (int)$setting['width'] . 'x' . (int)$setting['height'] . '.webp';
				//$image_file_old = $category['image'];
				// $image_file_new = 'cache/' . utf8_substr($category['image'], 0, utf8_strrpos($category['image'], '.')) . '-' . (int)$setting['width'] . 'x' . (int)$setting['height'] . '.png';
				// $image_file_new_webp = 'cachewebp/' . utf8_substr($category['image'], 0, utf8_strrpos($category['image'], '.')) . '-' . (int)$setting['width'] . 'x' . (int)$setting['height'] . '.webp';
				// echo (filemtime(DIR_IMAGE . $image_file_old) < filemtime(DIR_IMAGE . $image_file_new)) . ' - ';
				//if (!is_file(DIR_IMAGE . $image_file_new_webp) || (filemtime(DIR_IMAGE . $image_file_old) > filemtime(DIR_IMAGE . $image_file_new_webp))) {
					$image = $this->model_tool_image->resizeWc($category['image'], $setting['width'], $setting['height']);
				//}// else {
       		$image = 'image/cachewebp/' . utf8_substr($category['image'], 0, utf8_strrpos($category['image'], '.')) . '-' . (int)$setting['width'] . 'x' . (int)$setting['height'] . '.webp';
			//		if (!is_file($image)) {
       		//	$image = $this->model_tool_image->resizeWc($category['image'], $setting['width'], $setting['height']);
       		//}
       	//}
				
				$image_sizes = getimagesize($image);
				$setting['width'] = $image_sizes[0];
				$setting['height'] = $image_sizes[1];
        if ($hd_imgs) {
          // $image2x = $this->model_tool_image->resizeWc($category['image'], $setting['width']*2, $setting['height']*2);
          // $image3x = $this->model_tool_image->resizeWc($category['image'], $setting['width']*3, $setting['height']*3);
          // $image4x = $this->model_tool_image->resizeWc($category['image'], $setting['width']*4, $setting['height']*4);
          // $image2x = $image;
          // $image3x = $image;
          // $image4x = $image;
        }
      } else {
        $image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
        if ($hd_imgs) {
          $image2x = $this->model_tool_image->resize('placeholder.png', $setting['width']*2, $setting['height']*2);
          $image3x = $this->model_tool_image->resize('placeholder.png', $setting['width']*3, $setting['height']*3);
          $image4x = $this->model_tool_image->resize('placeholder.png', $setting['width']*4, $setting['height']*4);
        }
      }

			$data['categories'][] = array(
				'category_id' => $category['category_id'],
        'thumb'       => $image,
				'img_width'   => $setting['width'] . 'px',
				'img_height'  => $setting['height'] . 'px',
				'thumb_holder'=> $this->model_tool_image->resizeWc('catalog/frametheme/src_holder.png', $setting['width'], $setting['height']),
        // 'thumb2x'     => $hd_imgs ? $image2x : NULL,
        // 'thumb3x'     => $hd_imgs ? $image3x : NULL,
        // 'thumb4x'     => $hd_imgs ? $image4x : NULL,
        'svg' 		    => html_entity_decode($category['svg'], ENT_QUOTES, 'UTF-8'),
				'active'     	=> ($category['category_id'] == $data['category_id']) ? true : false,
				// 'name'        => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
				'name'        => $category['name'],
				// 'children'    => $children_data,
				'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
			);


		}

		$data['module'] = $module++;

		if ($data['categories']) {
			return $this->load->view('extension/module/xds_category_wall', $data);
		}
	}
}
