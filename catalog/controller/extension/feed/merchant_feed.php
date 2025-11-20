<?php
class ControllerExtensionFeedMerchantFeed extends Controller {
	public function index($generation = false) {

		if ($this->config->get('feed_merchant_feed_status')) {
			
			$this->load->model('setting/setting');
			
			$settings = $this->model_setting_setting->getSetting('feed_merchant_feed');
	
			if(isset($this->request->get['feed_id'])) { 
				$feed_id = $this->request->get['feed_id'];
			} else {
				$feed_id = false;
			}
			
			if(isset($this->request->get['key'])) { 
				$key = $this->request->get['key'];
			} else {
				$key = false;
			}

			if($feed_id && !empty($settings['feed_merchant_feed_settings'][$feed_id])) {
				
				$setting_feed = $settings['feed_merchant_feed_settings'][$feed_id];
			
				if($key == $setting_feed['cron_key']) {
					
					if($code_lang = $this->getLangById($setting_feed['language_id'])) {
						$this->session->data['language'] = $code_lang['code'];
						
						if($setting_feed['url_prefix'] && isset($this->session->data['language_url'])) {
							$this->session->data['language_url'] = $setting_feed['url_prefix'];
						} elseif(isset($this->session->data['language_url'])) {
							$this->session->data['language_url'] = $code_lang['language_url'];
						}
						
						if($this->config->get('config_language_id') != $setting_feed['language_id']) {
							$this->config->set('config_language_id', $setting_feed['language_id']);
						}
					}
	
					$this->load->model('extension/feed/merchant_feed');
					
					$products = $this->model_extension_feed_merchant_feed->getProducts($setting_feed);
					
					$output = $this->outputXml($setting_feed, $products, $feed_id, $flag = 'full');
					
					if(!$generation) {
						$this->response->addHeader('Content-Type: application/xml');
						$this->response->setOutput($output);
					} else {
						return $output;
					}					
				}
			}
		}
	}
	
	public function generate() {
		$json = array();
		
		if(isset($this->request->get['feed_id'])) {
			$feed_merchant_feed_settings = $this->config->get('feed_merchant_feed_settings');
	
			if(!empty($feed_merchant_feed_settings[$this->request->get['feed_id']])) {
				$this->load->language('extension/feed/merchant_feed');
				
				$this->load->model('extension/feed/merchant_feed');
				
				if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
					$server = HTTPS_SERVER; 
				} else {
					$server = HTTP_SERVER; 
				}
				
				if(isset($this->request->get['start'])) {
					$start = $this->request->get['start'];
				} else {
					$start = 0;
				}	
				
				if(isset($this->request->get['feed_id'])) {
					$feed_id = $this->request->get['feed_id'];
				} else {
					$feed_id = 1;
				}
				
				$limit = 10;
				
				$setting_feed = $feed_merchant_feed_settings[$this->request->get['feed_id']];
				
				if($code_lang = $this->getLangById($setting_feed['language_id'])) {
					$this->session->data['language'] = $code_lang['code'];
					
					if($setting_feed['url_prefix'] && isset($this->session->data['language_url'])) {
						$this->session->data['language_url'] = $setting_feed['url_prefix'];
					} elseif(isset($this->session->data['language_url'])) {
						$this->session->data['language_url'] = $code_lang['language_url'];
					}
					
					if($this->config->get('config_language_id') != $setting_feed['language_id']) {
						$this->config->set('config_language_id', $setting_feed['language_id']);
					}
				}
	
				$setting_feed['start'] = $start;
				$setting_feed['limit'] = $limit;
				
				if(!$this->request->get['count_all']) {
					$count_product = $this->model_extension_feed_merchant_feed->getTotalProduct($setting_feed);
				} else {
					$count_product = $this->request->get['count_all'];
				}

				$products = $this->model_extension_feed_merchant_feed->getProducts($setting_feed);
				
				$next = $start + $limit;
				
				$json['success'] = $next;
				$json['count_all'] = $count_product;
				$json['feed_id'] = $feed_id;
				$json['link'] = $server . 'google_feed_' . $feed_id . '_' . $setting_feed['currency'] . '.xml';
				
				if($count_product > $limit) { 
					if($next <= $count_product) {
						if($start == 0) {
							$file = str_replace('system/', '', DIR_SYSTEM) . 'google_feed_' . $feed_id . '_' . $setting_feed['currency'] . '.xml';
							
							if(file_exists($file)) {
								unlink($file);
							}
							
							$this->outputXml($setting_feed, $products, $feed_id, $flag = 'start');
							$this->outputXml($setting_feed, $products, $feed_id, $flag = 'body');
						} else {
							$this->outputXml($setting_feed, $products, $feed_id, $flag = 'body');
						}
						
						$json['percent'] = round($next/$count_product*100);
					} else {
						$this->outputXml($setting_feed, $products, $feed_id, $flag = 'body');
						$this->outputXml($setting_feed, $products, $feed_id, $flag = 'end');
						
						$json['percent'] = 100;
					}
				} else {
					$file = str_replace('system/', '', DIR_SYSTEM) . 'google_feed_' . $feed_id . '_' . $setting_feed['currency'] . '.xml';
							
					if(file_exists($file)) {
						unlink($file); 
					} 
					
					$this->outputXml($setting_feed, $products, $feed_id, $flag = 'full_save');
				}
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function outputXml($data, $products, $feed_id, $flag) {
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_SERVER; 
		} else {
			$server = HTTP_SERVER; 
		}
		
		if(isset($data['currency'])) { 
			$currency = $data['currency'];
		} else {
			$currency = $this->session->data['currency'];
		}
		
		$currency_value = $this->currency->getValue($currency); 
		
		if(!empty($this->config->get('feed_merchant_feed_category_items'))) {
			$google_category = $this->config->get('feed_merchant_feed_category_items');
		} else {
			$google_category = array();
		}
	
		$output = ''; 
		
		if($flag == 'start' || $flag == 'full' || $flag == 'full_save') {
			$output .= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
			$output .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">' . PHP_EOL;
			$output .= '<title>' . $data['title'] . '</title>' . PHP_EOL;
			$output .= '<link>' . $server . '</link>' . PHP_EOL;
			$output .= '<description>' . $data['description'] . '</description>' . PHP_EOL;
			$output .= '<channel>' . PHP_EOL;
		}
		
		if($flag == 'body' || $flag == 'full' || $flag == 'full_save') {
			foreach($products as $product) {
				$descr = $data['product_html'] ? $this->removeHtml($product['description']) : $product['description'];				
				
				$product_id = $data['product_id'] ? $product['product_id'] : $product['model'];
				
				if($data['product_descr']) {
					$descr = $product['meta_description'];
				}
				
				if(!isset($product['item_group_id'])) {
					$id = $product_id;
				} else {
					$id = $product['item_group_id'];
				}
				
				$output .= '<item>' . PHP_EOL;
				$output .= '<g:id>' . $id . '</g:id>' . PHP_EOL;
				$output .= '<g:title><![CDATA[' . $product['name'] . ']]></g:title>' . PHP_EOL;
				$output .= '<g:description><![CDATA[' . mb_substr($descr, 0, 4999, 'UTF-8') . ']]></g:description>' . PHP_EOL;
				
				$link = $this->url->link('product/product', 'product_id=' . $product['product_id']);
				
				if(!empty($data['url_prefix']) && strpos($link, $server . trim($data['url_prefix']) . '/') === false) {
					$link = str_replace($server, $server . trim($data['url_prefix']) . '/', $link);
				}
				
				$output .= '<g:link>' . htmlspecialchars($link, ENT_QUOTES) . '</g:link>' . PHP_EOL;
				$output .= '<g:image_link>' . $server . 'image/' . $product['image'] . '</g:image_link>' . PHP_EOL;
				
				if($product['images']) {
					foreach($product['images'] as $image) {
						$output .= '<g:additional_image_link>' . $server . 'image/' . $image['image'] . '</g:additional_image_link>' . PHP_EOL;
					}
				}
				
				if($product['category']) {
					$output .= '<g:product_type>' . htmlspecialchars($product['category'], ENT_QUOTES) . '</g:product_type>' . PHP_EOL;
				}	
				
				if(isset($google_category[$product['category_id']]['google_id'])) {
					$output .= '<g:google_product_category>' . $google_category[$product['category_id']]['google_id'] . '</g:google_product_category>' . PHP_EOL;
				}
				
				if(!empty($google_category[$product['category_id']]['custom_label'])) {
					foreach($google_category[$product['category_id']]['custom_label'] as $key => $label) {
						if(!empty($google_category[$product['category_id']]['custom_label'][$key])) {
							$output .= '<g:custom_label_' . $key . '>' . $label . '</g:custom_label_' . $key . '>' . PHP_EOL;
						}
					}
				}
				
				if($product['quantity'] > 0) {
					$output .= '<g:availability>in_stock</g:availability>' . PHP_EOL;
				} elseif($product['quantity'] <= 0 && in_array($product['stock_status_id'], $data['product_stock_status'])) {
					$output .= '<g:availability>preorder</g:availability>' . PHP_EOL;
				} else {
					$output .= '<g:availability>out_of_stock</g:availability>' . PHP_EOL;
				}
				
				if($product['manufacturer']) {
					$output .= '<g:brand><![CDATA[' . $product['manufacturer'] . ']]></g:brand>' . PHP_EOL;
				}
				
				if($data['product_condition']) {
					$output .= '<g:condition>new</g:condition>' . PHP_EOL;
				} else {
					$output .= '<g:condition>used</g:condition>' . PHP_EOL;
				}
				
				if($data['product_adult']) {
					$output .= '<g:adult>yes</g:adult>' . PHP_EOL;
				}
				
				if(isset($product['item_group_id'])) {
					$output .= '<g:item_group_id>' . $product_id . '</g:item_group_id>' . PHP_EOL;
				}
				
				if($product['gtin']) {
					$output .= '<g:gtin>' . $product['gtin'] . '</g:gtin>' . PHP_EOL;
				}
				
				if($product['mpn']) {
					$output .= '<g:mpn>' . $product['mpn'] . '</g:mpn>' . PHP_EOL;
				}
				
				if(!$product['gtin'] && !$product['mpn']) {
					$output .= '<g:identifier_exists>no</g:identifier_exists>' . PHP_EOL;
				}
			
				if(isset($product['special']['price']) && $data['product_special']) {
					$output .= '<g:sale_price>' . $this->currency->format($this->tax->calculate($product['special']['price'], false, false), $currency, $currency_value, false) . ' ' . $currency . '</g:sale_price>' . PHP_EOL;
					$output .= '<g:price>' . $this->currency->format($this->tax->calculate($product['price'], false, false), $currency, $currency_value, false) . ' ' . $currency . '</g:price>' . PHP_EOL;
					if((!empty($product['special']['date_start']) && $product['special']['date_start'] != '0000-00-00') && (!empty($product['special']['date_end']) && $product['special']['date_end'] != '0000-00-00')) {
						$output .= '<g:sale_price_effective_date>' . date("c", strtotime($product['special']['date_start'])) . '/' . date("c", strtotime($product['special']['date_end'])) . '</g:sale_price_effective_date>' . PHP_EOL;
					}
				} else {
					$output .= '<g:price>' . $this->currency->format($this->tax->calculate($product['price'], false, false), $currency, $currency_value, false) . ' ' . $currency . '</g:price>' . PHP_EOL;
				}
				
				if(!empty($product['size'])) {
					$output .= '<g:size>' . $product['size'] . '</g:size>' . PHP_EOL;
					$output .= '<g:size_type>regular</g:size_type>' . PHP_EOL;
					$output .= '<g:size_system>EU</g:size_system>' . PHP_EOL;
				}
				
				$output .= '</item>' . PHP_EOL;
			}
		}	
		
		if($flag == 'end' || $flag == 'full' || $flag == 'full_save') {
			$output .= '</channel>' . PHP_EOL;
			$output .= '</rss>';
		}
		
		if($flag == 'full') {
			return $output;
		} else {
			$file = str_replace('system/', '', DIR_SYSTEM) . 'google_feed_' . $feed_id . '_' . $data['currency'] . '.xml';
			
			file_put_contents($file, $output, FILE_APPEND);
		}
	}
	
	public function generateCron() {
		if(isset($this->request->get['feed_id'])) {
			$feed_id = $this->request->get['feed_id'];
		} else {
			$feed_id = 0;
		}
		
		if($feed_id) {
			if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
				$server = HTTPS_SERVER; 
			} else {
				$server = HTTP_SERVER; 
			}
			
			$output = $this->index($generation = true);
		
			$feed_merchant_feed_settings = $this->config->get('feed_merchant_feed_settings');
			
			$setting_feed = isset($feed_merchant_feed_settings[$feed_id]) ? $feed_merchant_feed_settings[$feed_id] : '';
	
			if($setting_feed) {
				$file = str_replace('system/', '', DIR_SYSTEM) . 'google_feed_' . $feed_id . '_' . $setting_feed['currency'] . '.xml';
				
				if(file_exists($file)) {
					unlink($file);
				}
				
				file_put_contents($file, $output, FILE_APPEND);
				
				echo 'Generation complete!<br> Feed: <a href="' . $server . 'google_feed_' . $feed_id . '_' . $setting_feed['currency'] . '.xml' . '">' . $server . 'google_feed_' . $feed_id . '_' . $setting_feed['currency'] . '.xml' . '</a>';
			}
			
		}
	}
	
	private function removeHtml($description) {
		$description = str_replace("", " ",str_replace("\t", " ",str_replace("\n", " ", str_replace("\r", " ", str_replace("\r\n", " ", htmlspecialchars($this->strip_html_tags(htmlspecialchars_decode($description,ENT_COMPAT)),ENT_COMPAT, 'UTF-8'))))));;
		
		return $description;
	}
	
	protected function strip_html_tags($description)	{
    	$description = preg_replace(
        array(
            '@&nbsp;@siu',
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu'
        ),
        array(
            ' ', 
            ' ', 
			' ', 
			' ', 
			' ', 
			' ', 
			' ', 
			' ', 
			' ', 
			' ',
            "\n\$0", 
			"\n\$0", 
			"\n\$0", 
			"\n\$0", 
			"\n\$0", 
			"\n\$0",
            "\n\$0", 
			"\n\$0"
        ),
        $description);
		
		return strip_tags($description);
	}
	
	private function getLangById($language_id) {
		$this->load->model('localisation/language');

		$languages = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$languages[$result['language_id']] = array(
					'code'			=> $result['code'],
					'language_url'	=> isset($result['url']) ? $result['url'] : ''
				);
			}
		}
		
		if(isset($languages[$language_id])) {
			return $languages[$language_id];
		} else {
			return false;
		}
	}
	
	public function reviewFeed() {
		$this->load->model('extension/feed/merchant_feed');
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_SERVER; 
		} else {
			$server = HTTP_SERVER; 
		}
		
		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$favicon = $server . 'image/' . $this->config->get('config_icon');
		} else {
			$favicon = '';
		}
		
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<feed xmlns:vc="http://www.w3.org/2007/XMLSchema-versioning" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation= "http://www.google.com/shopping/reviews/schema/product/2.3/product_reviews.xsd">';
		$output .= '<version>2.3</version>';
		$output .= '<aggregator>';
		$output .= '<name>Sample Reviews Aggregator (if applicable)</name>';
		$output .= '</aggregator>';
		$output .= '<publisher>';
		$output .= '<name>Sample Retailer</name>';
		if($favicon) {
			$output .= '<favicon>' . $favicon . '</favicon>';
		}
		$output .= '</publisher>';
		$output .= '<reviews>';
		
		$reviews = $this->model_extension_feed_merchant_feed->getReviewsProduct();
		
		foreach($reviews as $review) {
			$output .= '<review>';
		
			$output .= '<review_id>' . (int)$review['review_id'] . '</review_id>';
			$output .= '<reviewer>';
            $output .= '<name>' . $review['author'] . '</name>';
				
			if($review['customer_id']) {
				$output .= '<reviewer_id>' . $review['customer_id'] . '</reviewer_id>';
			}
			
			$output .= '</reviewer>';
			$output .= '<review_timestamp>' . date("c", strtotime($review['date_added'])) . '</review_timestamp>';
			$output .= '<content>' . $review['text'] . '</content>';
			
			/*
			if($review['plus']) {
				$output .= '<pros>';
				$output .= '<pro>' . $review['plus'] . '</pro>';
				$output .= '</pros>';
			}
			
			if($review['minus']) {
				$output .= '<cons>';
                $output .= '<con>' . $review['minus'] . '</con>';
				$output .= '</cons>';
			}
			*/
			
			if($review['count'] > 1) {
				$text = 'type="group"';
			} else {
				$text = 'type="singleton"';
			}
			
			$output .= '<review_url ' . $text . '>' . $this->url->link('product/product', 'product_id=' . $review['product_id']) . '</review_url>';
			
			$output .= '<ratings>';
            $output .= '<overall min="1" max="5">' . $review['rating'] . '</overall>';
            $output .= '</ratings>';
			
			$output .= '<products>';
			$output .= '<product>';
			$output .= '<product_ids>';
			$output .= '<mpns>';
			$output .= '<mpn>' . $review['product_model'] . '</mpn>';
			$output .= '</mpns>';
			
			if($review['product_brand']) {
			$output .= '<brands>';
			$output .= '<brand>' . preg_replace('/\(.+$/u', '', $review['product_brand']) . '</brand>';
			$output .= '</brands>';
			}
			
			$output .= '</product_ids>';
			
			$output .= '<product_name>' . $review['product_name'] . '</product_name>';
            $output .= '<product_url>' . $this->url->link('product/product', 'product_id=' . $review['product_id']) . '</product_url>';
			$output .= '</product>';
			$output .= '</products>';
			$output .= '<is_spam>false</is_spam>';
			$output .= '</review>';
		}
		
		$output .= '</reviews>';
		$output .= '</feed>';	
	
		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	}
}