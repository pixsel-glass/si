<?php

class ControllerExtensionModuleFilterPixsel extends Controller {

	public function index() {
		$data['bg_mask'] = $this->config->get('t1_category_mask_toggle');

		$this->load->model('tool/image');


		$detect_mb = new Mobile_Detect();

		if ($detect_mb->isMobile()) {

			$data['mobile'] = 1;

		} else {

			$data['mobile'] = 0;

		}

		$lang = $this->language->get('code');
		//if ($lang == 'pl') {
		//	$data['lang'] = '';
		//} else 
		if ($lang == 'en') {
			$data['lang'] = 'eng';
		//} else if ($lang == 'uk') {
		//	$data['lang'] = 'ua';
		} else {
			$data['lang'] = $lang;
		}

		if (isset($this->request->get['path'])) {

			$parts = explode('_', (string)$this->request->get['path']);

		} else {

			$parts = array();

		}

		if(strpos($_SERVER['SERVER_NAME'], 'catalog.') !== false || strpos($_SERVER['SERVER_NAME'], 'price.') !== false) {
			$lite = 1;
			$data['lite'] = 1;
		} else {
			$lite = 0;
			$data['lite'] = 0;
		}

		if (!isset($this->request->get['route']) || isset($this->request->get['route']) && $this->request->get['route'] == 'common/home') {
			$data['ishome'] = 1;
		} else {
			$data['ishome'] = 0;
		}

		$data['server'] = 'https://' . $_SERVER['SERVER_NAME'];



		$category_id = end($parts);



		$this->load->model('catalog/category');


		if (count($parts) == 2) {
			$category_info = $this->model_catalog_category->getCategory($category_id);
			$category_info = $this->model_catalog_category->getCategory($category_info['parent_id']);
			$category_subinfo = $this->model_catalog_category->getCategory($category_id);
		} else {
			$category_info = $this->model_catalog_category->getCategory($category_id);
		}


		if (isset($this->request->get['path'])) {

			$toplevel = strpos($this->request->get['path'], '_')==false;

		} else {

			$toplevel = false;

		}


		if (count($parts) == 0) {

			$this->load->language('extension/module/filter_pixsel');



			$url = '';



			if (isset($this->request->get['sort'])) {

				$url .= '&sort=' . $this->request->get['sort'];

			}



			if (isset($this->request->get['order'])) {

				$url .= '&order=' . $this->request->get['order'];

			}



			if (isset($this->request->get['limit'])) {

				$url .= '&limit=' . $this->request->get['limit'];

			}



			// $data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url));



			if (isset($this->request->get['filter'])) {

				$data['filter_category'] = explode(',', $this->request->get['filter']);

			} else {

				$data['filter_category'] = array();

			}



			// all top categories

            $all_top_categories = $this->model_catalog_category->getCategories();

            $results_alltopcategories = array();

            foreach ($all_top_categories as $result) {

				$results_alltopcategories[] = array(

					'category_id' => $result['category_id'],

					'name' => $result['name'],

					'href' => $this->url->link('product/category', 'path='.$result['category_id']),

					'category_thumb' => $this->model_tool_image->resizeWc($result['image'], 320, 219),


        			'category_svg' => html_entity_decode($result['svg'], ENT_QUOTES, 'UTF-8'),

				);

		    }

		    $data['marks'] = $results_alltopcategories;

			// all top categories


		    if ($lite <= 0) {
				return $this->load->view('extension/module/filter_home_pixsel', $data);
			} else {
				return $this->load->view('extension/module/filter_listing_pixsel', $data);
			}

		}



		// if ($category_info && $toplevel) {

		if ($category_info && (count($parts) == 1 || count($parts) == 2)) {

			$this->load->language('extension/module/filter_pixsel');



			$url = '';



			if (isset($this->request->get['sort'])) {

				$url .= '&sort=' . $this->request->get['sort'];

			}



			if (isset($this->request->get['order'])) {

				$url .= '&order=' . $this->request->get['order'];

			}



			if (isset($this->request->get['limit'])) {

				$url .= '&limit=' . $this->request->get['limit'];

			}



			$data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url));



			if (isset($this->request->get['filter'])) {

				$data['filter_category'] = explode(',', $this->request->get['filter']);

			} else {

				$data['filter_category'] = array();

			}



			// top category info

     	    if ($category_info['image']) {

				// $image_file_old = $category_info['image'];
				// $image_file_new = 'cache/' . utf8_substr($category_info['image'], 0, utf8_strrpos($category_info['image'], '.')) . '-160x90.png';
				//if (!is_file(DIR_IMAGE . $image_file_new) || (filemtime(DIR_IMAGE . $image_file_old) > filemtime(DIR_IMAGE . $image_file_new))) {
			        // $image = $this->model_tool_image->resizeWc($category_info['image'], 320, 219);
			        $image = $this->model_tool_image->resizeWc($category_info['image'], 160, 90);
			    //} else {
			    	$image = 'image/cachewebp/' . utf8_substr($category_info['image'], 0, utf8_strrpos($category_info['image'], '.')) . '-160x90.webp';
			    //}
		    } else {

		        $image = $this->model_tool_image->resize('placeholder.png', 50, 50);

		    }

			$image_sizes = getimagesize($image);
			$image_width = $image_sizes[0];
			$image_height = $image_sizes[1];

			if (count($parts) == 2) {
				$data['category_id'] = $category_info['category_id'];
				$data['subcategory_id'] = $category_subinfo['category_id'];
			} else {
				$data['category_id'] = $category_info['category_id'];
			}


        	$data['category_thumb'] = $image;

			$image_thumb_old = $category_info['image'];
			// $image_thumb_new = 'cache/' . utf8_substr($category_info['image'], 0, utf8_strrpos($category_info['image'], '.')) . '-160x90.png';
			// if (!is_file(DIR_IMAGE . $image_thumb_new) || (filemtime(DIR_IMAGE . $image_thumb_old) > filemtime(DIR_IMAGE . $image_thumb_new))) {
	        	// $data['category_thumb_top'] = $this->model_tool_image->resizeWc($category_info['image'], 320, 219);
	        	$data['category_thumb_top'] = $this->model_tool_image->resizeWc($category_info['image'], 160, 90);
	        //} else {
	        	$data['category_thumb_top'] = 'image/cachewebp/' . utf8_substr($category_info['image'], 0, utf8_strrpos($category_info['image'], '.')) . '-160x90.webp';;
	        //}

			$data['category_img_width'] = $image_width;

			$data['category_img_height'] = $image_height;

			$data['category_thumb_holder'] = $this->model_tool_image->resize('catalog/frametheme/src_holder.png', 50, 50);

        	$data['category_svg'] = html_entity_decode($category_info['svg'], ENT_QUOTES, 'UTF-8');

			// top category info



			// all top categories

            $all_top_categories = $this->model_catalog_category->getCategories();

            $results_alltopcategories = array();

            foreach ($all_top_categories as $result) {

				$results_alltopcategories[] = array(

					'category_id' => $result['category_id'],

					'name' => $result['name'],

					'href' => $this->url->link('product/category', 'path='.$result['category_id']),

				);

		    }

		    $data['marks'] = $results_alltopcategories;

			// all top categories



			// second categories
           	$second_categories = $this->model_catalog_category->getCategories($category_info['category_id']);

            $results_secondcategories = array();

            foreach ($second_categories as $result) {

				$results_secondcategories[] = array(

					'category_id' => $result['category_id'],

					'name' => $result['name'],

				);

		    }

		    $data['models'] = $results_secondcategories;

		    if (count($parts) == 2) {
		    	$data['seccat'] = 1;
		    } else {
		    	$data['seccat'] = 0;
		    }
			// second categories

		    // third categories
		    if (count($parts) == 2) {
	            $third_categories = $this->model_catalog_category->getCategories($category_subinfo['category_id']);

	            $results_thirdcategories = array();

	            foreach ($third_categories as $result) {

	            	$catinfo = $this->model_catalog_category->getCategory($result['category_id']);

		     	    if ($catinfo['image']) {
		     	    	//$image_ccinfo_old = $catinfo['image'];
						//$image_ccinfo_new = 'cache/' . utf8_substr($catinfo['image'], 0, utf8_strrpos($catinfo['image'], '.')) . '-160x90.png';
						//if (!is_file(DIR_IMAGE . $image_ccinfo_new) || (filemtime(DIR_IMAGE . $image_ccinfo_old) > filemtime(DIR_IMAGE . $image_ccinfo_new))) {
					        // $image = $this->model_tool_image->resizeWc($catinfo['image'], 320, 219);
					        $image = $this->model_tool_image->resizeWc($catinfo['image'], 160, 90);
					    //} else {
					    	$image = 'image/cachewebp/' . utf8_substr($catinfo['image'], 0, utf8_strrpos($catinfo['image'], '.')) . '-160x90.webp';;

					    //}

				    } else {

				        $image = $this->model_tool_image->resize('placeholder.png', 50, 50);

				    }

					$image_sizes = getimagesize($image);
					$image_width = $image_sizes[0];
					$image_height = $image_sizes[1];			    

		        	$category_thumb = $image;

					$category_img_width = $image_width;

					$category_img_height = $image_height;

					$category_thumb_holder = $this->model_tool_image->resize('catalog/frametheme/src_holder.png', 50, 50);

		        	$category_svg = html_entity_decode($catinfo['svg'], ENT_QUOTES, 'UTF-8');



					$results_thirdcategories[] = array(

						'category_id' => $result['category_id'],

						'name' => $result['name'],

		        		'category_thumb' => $category_thumb,

						'category_img_width' => $category_img_width,

						'category_img_height' => $category_img_height,

						'category_thumb_holder' => $category_thumb_holder,

		        		'category_svg' => $category_svg,

					);

			    }

			    $data['years'] = $results_thirdcategories;
			}
			// third categories


			return $this->load->view('extension/module/filter_pixsel', $data);

		}



		if ($category_info && count($parts) == 3) {

			$this->load->language('extension/module/filter_pixsel');



			$url = '';



			if (isset($this->request->get['sort'])) {

				$url .= '&sort=' . $this->request->get['sort'];

			}



			if (isset($this->request->get['order'])) {

				$url .= '&order=' . $this->request->get['order'];

			}



			if (isset($this->request->get['limit'])) {

				$url .= '&limit=' . $this->request->get['limit'];

			}



			$data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url));



			if (isset($this->request->get['filter'])) {

				$data['filter_category'] = explode(',', $this->request->get['filter']);

			} else {

				$data['filter_category'] = array();

			}



			// top category info

     	    if ($category_info['image']) {

				// $image_info_old = $category_info['image'];
				// $image_info_new = 'cache/' . utf8_substr($category_info['image'], 0, utf8_strrpos($category_info['image'], '.')) . '-160x90.png';
				//if (!is_file(DIR_IMAGE . $image_info_new) || (filemtime(DIR_IMAGE . $image_info_old) > filemtime(DIR_IMAGE . $image_info_new))) {
			        // $image = $this->model_tool_image->resizeWc($category_info['image'], 320, 219);
			        $image = $this->model_tool_image->resizeWc($category_info['image'], 160, 90);
			    //} else {
			    	$image = 'image/cachewebp/' . utf8_substr($category_info['image'], 0, utf8_strrpos($category_info['image'], '.')) . '-160x90.webp';;
			    //}

		    } else {

		        $image = $this->model_tool_image->resize('placeholder.png', 80, 80);

		    }

			$image_sizes = getimagesize($image);
			$image_width = $image_sizes[0];
			$image_height = $image_sizes[1];		    

			$data['category_id'] = $category_info['category_id'];

        	$data['category_thumb'] = $image;

			$data['category_img_width'] = $image_width;

			$data['category_img_height'] = $image_height;

			$data['category_thumb_holder'] = $this->model_tool_image->resize('catalog/frametheme/src_holder.png', 50, 50);

        	$data['category_svg'] = html_entity_decode($category_info['svg'], ENT_QUOTES, 'UTF-8');

			// top category info



			// all top categories

            $all_top_categories = $this->model_catalog_category->getCategories();

            $results_alltopcategories = array();

            foreach ($all_top_categories as $result) {

            	$catinfo = $this->model_catalog_category->getCategory($result['category_id']);

	     	    if ($catinfo['image']) {
	     	    	// $image_cinfo_old = $catinfo['image'];
					// $image_cinfo_new = 'cache/' . utf8_substr($catinfo['image'], 0, utf8_strrpos($catinfo['image'], '.')) . '-160x90.png';
					// if (!is_file(DIR_IMAGE . $image_cinfo_new) || (filemtime(DIR_IMAGE . $image_cinfo_old) > filemtime(DIR_IMAGE . $image_cinfo_new))) {
			        	// $image = $this->model_tool_image->resizeWc($catinfo['image'], 320, 219);
			        	$image = $this->model_tool_image->resizeWc($catinfo['image'], 160, 90);
			        // } else {
			        	$image = 'image/cachewebp/' . utf8_substr($catinfo['image'], 0, utf8_strrpos($catinfo['image'], '.')) . '-160x90.webp';;
			        //}
			    } else {

			        $image = $this->model_tool_image->resize('placeholder.png', 50, 50);

			    }

				$image_sizes = getimagesize($image);
				$image_width = $image_sizes[0];
				$image_height = $image_sizes[1];			    

	        	$category_thumb = $image;

				$category_img_width = $image_width;

				$category_img_height = $image_height;

				$category_thumb_holder = $this->model_tool_image->resize('catalog/frametheme/src_holder.png', 50, 50);

	        	$category_svg = html_entity_decode($catinfo['svg'], ENT_QUOTES, 'UTF-8');



				$results_alltopcategories[] = array(

					'category_id' => $result['category_id'],

					'name' => $result['name'],

					'href' => $this->url->link('product/category', 'path='.$result['category_id']),

	        		'category_thumb' => $category_thumb,

					'category_img_width' => $category_img_width,

					'category_img_height' => $category_img_height,

					'category_thumb_holder' => $category_thumb_holder,

	        		'category_svg' => $category_svg,

				);

		    }

		    $data['marks'] = $results_alltopcategories;

			// all top categories



			// second categories

            $second_categories = $this->model_catalog_category->getCategories($parts[0]);

            $results_secondcategories = array();

            foreach ($second_categories as $result) {

				$results_secondcategories[] = array(

					'category_id' => $result['category_id'],

					'name' => $result['name'],

				);

		    }

		    $data['models'] = $results_secondcategories;

			// second categories



			// third categories

            $third_categories = $this->model_catalog_category->getCategories($parts[1]);

            $results_thirdcategories = array();

            foreach ($third_categories as $result) {

            	$catinfo = $this->model_catalog_category->getCategory($result['category_id']);

	     	    if ($catinfo['image']) {
	     	    	// $image_ccinfo_old = $catinfo['image'];
					// $image_ccinfo_new = 'cache/' . utf8_substr($catinfo['image'], 0, utf8_strrpos($catinfo['image'], '.')) . '-160x90.png';
					// if (!is_file(DIR_IMAGE . $image_ccinfo_new) || (filemtime(DIR_IMAGE . $image_ccinfo_old) > filemtime(DIR_IMAGE . $image_ccinfo_new))) {
				        // $image = $this->model_tool_image->resizeWc($catinfo['image'], 320, 219);
				        $image = $this->model_tool_image->resizeWc($catinfo['image'], 160, 90);
				    // } else {
				    	$image = 'image/cachewebp/' . utf8_substr($catinfo['image'], 0, utf8_strrpos($catinfo['image'], '.')) . '-160x90.webp';;

				    // }

			    } else {

			        $image = $this->model_tool_image->resize('placeholder.png', 50, 50);

			    }

				$image_sizes = getimagesize($image);
				$image_width = $image_sizes[0];
				$image_height = $image_sizes[1];			    

	        	$category_thumb = $image;

				$category_img_width = $image_width;

				$category_img_height = $image_height;

				$category_thumb_holder = $this->model_tool_image->resize('catalog/frametheme/src_holder.png', 50, 50);

	        	$category_svg = html_entity_decode($catinfo['svg'], ENT_QUOTES, 'UTF-8');



				$results_thirdcategories[] = array(

					'category_id' => $result['category_id'],

					'name' => $result['name'],

	        		'category_thumb' => $category_thumb,

					'category_img_width' => $category_img_width,

					'category_img_height' => $category_img_height,

					'category_thumb_holder' => $category_thumb_holder,

	        		'category_svg' => $category_svg,

				);

		    }

		    $data['years'] = $results_thirdcategories;

			// third categories



			// selected value

			$data['mark_id'] = $parts[0];

			$data['mark_name'] = $this->model_catalog_category->getCategory($parts[0])['name'];

			// $data['mark_img'] = $this->model_tool_image->resizeWc($this->model_catalog_category->getCategory($parts[0])['image'],320, 219);
			$data['mark_img'] = $this->model_tool_image->resizeWc($this->model_catalog_category->getCategory($parts[0])['image'],160, 90);

			//$data['mark_svg'] = html_entity_decode($this->model_catalog_category->getCategory($parts[0])['svg'], ENT_QUOTES, 'UTF-8');
			// $data['mark_svg'] = $this->model_tool_image->resizeWc($this->model_catalog_category->getCategory($parts[0])['svg'], 320, 219);
			$data['mark_svg'] = $this->model_tool_image->resizeWc($this->model_catalog_category->getCategory($parts[0])['svg'], 160, 90);


			$data['model_id'] = $parts[1];

			$data['model_name'] = $this->model_catalog_category->getCategory($parts[1])['name'];

			$data['year_id'] = $parts[2];

			$data['year_name'] = $this->model_catalog_category->getCategory($parts[2])['name'];



			return $this->load->view('extension/module/filter_listing_pixsel', $data);

		}

	}



	public function getSubcat() {

		if (isset($this->request->post['category_id'])) {

			$this->load->model('catalog/category');

			$this->load->model('tool/image');

			$category_id = $this->request->post['category_id'];

			// third categories

            $third_categories = $this->model_catalog_category->getCategories($category_id);

            $results_thirdcategories = array();

            foreach ($third_categories as $result) {

            	$catinfo = $this->model_catalog_category->getCategory($result['category_id']);

	     	    if ($catinfo['image']) {

			        $image = $this->model_tool_image->resizeWc($catinfo['image'], 320, 219);

			    } else {

			        $image = $this->model_tool_image->resize('placeholder.png', 50, 50);

			    }

				$image_sizes = getimagesize($image);
				$image_width = $image_sizes[0];
				$image_height = $image_sizes[1];			    

	        	$category_thumb = $image;

				$category_img_width = $image_width;

				$category_img_height = $image_height;

				$category_thumb_holder = $this->model_tool_image->resize('catalog/frametheme/src_holder.png', 50, 50);

	        	$category_svg = html_entity_decode($catinfo['svg'], ENT_QUOTES, 'UTF-8');



				$results_thirdcategories[] = array(

					'category_id' => $result['category_id'],

					'name' => $result['name'],

					// 'href' => $this->url->link('product/category', 'path='.$result['category_id']),

					'href' => $this->url->link('product/category', 'path='),

	        		// 'category_thumb' => $category_thumb,
	        		'category_thumb' => str_replace('png', 'webp', str_replace('cache', 'cachewebp', $category_thumb)),

					'category_img_width' => $category_img_width,

					'category_img_height' => $category_img_height,

					'category_thumb_holder' => $category_thumb_holder,

	        		'category_svg' => $category_svg,

				);

		    }

		    echo json_encode($results_thirdcategories);

			// third categories

		}

	}



	public function getSvg() {

		if (isset($this->request->post['category_id'])) {

			$this->load->model('catalog/category');

			$category_id = $this->request->post['category_id'];

           	$catinfo = $this->model_catalog_category->getCategory($category_id);

        	$category_svg = html_entity_decode($catinfo['svg'], ENT_QUOTES, 'UTF-8');

		    echo $category_svg;

		}

	}

}