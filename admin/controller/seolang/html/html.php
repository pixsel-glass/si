<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerSeoLangHtmlHtml', false)) {
class ControllerSeoLangHtmlHtml extends Controller
{
	private $error = array();
	protected $data;
    private $controller_main;
    private $widget = 'html';

	public function __construct($registry) {
		parent::__construct($registry);
        $controller_main = 'seolang/seolang';
        $this->load->model($controller_main);
        $this->controller_main = $this->model_seolang_seolang->control($controller_main);
	}

	public function get_main($method) {
        if (method_exists($this->registry->get($this->controller_main), $method)) {
	        $controller_main = $this->controller_main;
        	$main = $this->$controller_main->$method();
        	$main_data = $main->get_data();
        	$this->data = array_merge($this->data, $main_data);
        }
		return $this->data;
	}

    private function prefix() {
		$pull = array();
		$length = 3;
		while (count($pull) < $length) {
		  $pull = array_merge($pull, range('0', '9'));
		}
		shuffle($pull);
		$this->data['prefix'] = substr(implode($pull), 0, $length);
    }

	public function add_multi($this_data) {
		$this->data = $this_data;

        /*
        // Get main controller methods
        $this->data['thisis']->load_get_layouts();
        $main_data = $this->data['thisis']->get_data();
        $this->data = array_merge($this->data, $main_data);
        // or
        $this->data = $this->get_main('load_get_layouts');
        */

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            if (isset($this->request->post['widget_name']) && $this->request->post['widget_name'] != '') {
            	parse_str(str_replace('&amp;', '&', $this->request->post['myform']), $post_form);
            	$settings_widget = $post_form['seolang_settings_'. $this->data['store_id']]['multi'][$this->request->post['widget_name']];
                $settings_widget_data = $post_form[$this->request->post['widget_name']];
				$settings_widget_data['cmswidget'] = '';
            } else {
            	$post_form = array();
            	$settings_widget = array();
            	$settings_widget_data = array();
            }

            $this->prefix();
            // No repeat

            $this->data['multi_name'] = 'HTML-';

            if (!empty($settings_widget)) {
                $settings_array = $settings_widget;
            	$settings_array['name'] = $this->data['multi_name'];

            } else {

	            $this->data['seolang_settings_store']['multi'] = array();

		        if (empty($this->data['seolang_settings_store']['multi'])) {

		        	$settings_array = array('name' => $this->data['multi_name'],
						 					'widget' => $this->widget,
						 					'status' => true
							 			 	);

				}
			}

            $settings_widget_data = $this->load_settings_more($settings_widget_data);

            $this->data['seolang_settings_store']['multi'] = array($this->data['multi_name'] => $settings_array);

            $this->data['multi'] = $this->data['seolang_settings_store']['multi'][$this->data['multi_name']];

            $this->data['seolang_settings_store_widgets_data'] = array($this->data['multi_name'] => $settings_widget_data);

            $this->data['template'] = 'seolang/' . $this->widget .'/multi';
		}
		return $this->data;
	}

	public function load_language($this_data) {
    	$this->data = $this_data;

        $this->language->load('seolang/' . $this->widget . '/' . $this->widget);

        $this->data['entry_seolang_html_description'] = $this->language->get('entry_seolang_html_description');

    	return $this->data;
	}

	public function save_settings($this_data) {
    	$this->data = $this_data;

        if (!empty($this->request->post['seolang_settings_' . $this->data['store_id']]['multi'])) {
		    foreach ($this->request->post['seolang_settings_' . $this->data['store_id']]['multi'] as $multi_name => $multi_array) {
				$this->model_seolang_seolang->editSetting($multi_name, $this->request->post, $this->data['store_id']);
		    }
		}
    	return $this->data;
	}

	private function load_settings_more($settings_widget_data) {

			foreach ($this->data['languages'] as $code => $lang) {
				if (!isset($settings_widget_data['description'][$lang['language_id']])) {
			       	$settings_widget_data['description'][$lang['language_id']] = '';
		        }

	        }

		return $settings_widget_data;
	}


	public function load_settings($this_data) {
    	$this->data = $this_data;

    	if (!empty($this->data['seolang_settings_store']['multi'])) {

	        foreach($this->data['seolang_settings_store']['multi'] as $multi) {
	        	$settings_widget_data = $this->model_seolang_seolang->getSetting($multi['name'], $this->data['store_id']);
		        $settings_widget_data = $this->load_settings_more($settings_widget_data);
                // Merge settings all widgets
		        if (isset($this->data['seolang_settings_store_widgets_data'][$multi['name']])) {
		        	$settings_widget_data = array_merge($settings_widget_data, $this->data['seolang_settings_store_widgets_data'][$multi['name']]);
		        }

		        $this->data['seolang_settings_store_widgets_data'][$multi['name']] = $settings_widget_data;
	        }
        }

    	return $this->data;
	}
	
	public function load_tab_menu($this_data) {
		$this->data = $this_data;
	
		if (!isset($this->data['html_tab_menu'])) {
			$this->data['html_tab_menu'] = '';
		}
		$this->data['widget'] = $this->widget;
		if (!isset($this->data['seolang_settings']['widget_' . $this->widget . '_status'])) {
			$this->data['seolang_settings']['widget_' . $this->widget . '_status'] = true;
		}
		$this->data['template'] = 'seolang/' . $this->widget . '/tab_menu';
		$this->data['thisis']->set_data($this->data);
	
		$this->data['thisis']->load_view();
	
		$main_data = $this->data['thisis']->get_data();
		$this->data['html_tab_menu'] .= $main_data['html'];
	
		return $this->data;
	}

	public function load_tab_install($this_data) {
		$this->data = $this_data;
        if (!isset($this->data['html_tab_install'])) {
        	$this->data['html_tab_install'] = '';
        }

        $this->data['template'] = 'seolang/'.$this->widget . '/tab_install';
        $this->data['thisis']->set_data($this->data);

        $this->data['thisis']->load_view();

        $main_data = $this->data['thisis']->get_data();
        $this->data['html_tab_install'] .= $main_data['html'];

		return $this->data;
	}

	public function load_tab_options($this_data) {
		$this->data = $this_data;
        if (!isset($this->data['html_tab_options'])) {
        	$this->data['html_tab_options'] = '';
        }
        $this->data['widget'] = $this->widget;
        if (!isset($this->data['seolang_settings']['widget_' . $this->widget . '_status'])) {
        	$this->data['seolang_settings']['widget_' . $this->widget . '_status'] = true;
        }
        $this->data['template'] = 'seolang/' . $this->widget . '/tab_options';
        $this->data['thisis']->set_data($this->data);

        $this->data['thisis']->load_view();

        $main_data = $this->data['thisis']->get_data();
        $this->data['html_tab_options'] .= $main_data['html'];

		return $this->data;
	}


    public function load_tab_service($this_data) {
		$this->data = $this_data;
        if (!isset($this->data['html_tab_service'])) {
        	$this->data['html_tab_service'] = '';
        }

        $this->data['template'] = 'seolang/' . $this->widget . '/tab_service';
        $this->data['thisis']->set_data($this->data);

        $this->data['thisis']->load_view();

        $main_data = $this->data['thisis']->get_data();
        $this->data['html_tab_service'] .= $main_data['html'];

		return $this->data;
    }

    public function load_tab_doc($this_data) {
		$this->data = $this_data;
        if (!isset($this->data['html_tab_doc'])) {
        	$this->data['html_tab_doc'] = '';
        }

        $this->data['template'] = 'seolang/' . $this->widget . '/tab_doc';
        $this->data['thisis']->set_data($this->data);

        $this->data['thisis']->load_view();

        $main_data = $this->data['thisis']->get_data();
        $this->data['html_tab_doc'] .= $main_data['html'];

		return $this->data;
    }


	private function validate() {
		$this->language->load('seolang/seolang');
		if (!$this->user->hasPermission('modify', 'seolang/seolang')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->error) {
			return true;
		} else {
			$this->request->post = array();
			return false;
		}
	}

}
}