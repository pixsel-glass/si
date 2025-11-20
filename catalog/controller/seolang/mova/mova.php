<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerSeoLangMovaMova', false)) {
    class ControllerSeoLangMovaMova extends Controller {
        protected $data;

        protected $api_mova = false;
        protected $route;
        protected $server_protocol = 'HTTP/1.1';
        protected $widget = 'seolang/mova/mova';
        protected $http_user_agent;
        protected $settings = false;
        protected $device = array();
        protected $is_desktop = false;
        protected $is_mobile = false;
        protected $is_tablet = false;
        protected $is_smart = false;


        public function __construct($registry) {
            parent::__construct($registry);
            if (version_compare(phpversion(), '5.3.0', '<') == true) {
                exit('PHP5.3+ Required');
            }
            $this->data['index'] = false;
        }

        private function mova_api() {
            if (!$this->api_mova) {
                if (isset($this->registry->get('request')->get['route'])) {
                    $this->route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string) $this->registry->get('request')->get['route']);
                } else {
                    $this->route = 'common/home';
                }
                if (isset($this->route) && stripos($this->route, $this->widget) !== false) {
                    $this->api_mova = false;
                    if (isset($this->registry->get('request')->server['SERVER_PROTOCOL']) && $this->registry->get('request')->server['SERVER_PROTOCOL'] != '') {
                        $this->server_protocol = $this->registry->get('request')->server['SERVER_PROTOCOL'];
                    } else {
                        $this->server_protocol = 'HTTP/1.1';
                    }
                    header($this->server_protocol . ' ' . '404 Not Found');
                    header('Status: 404 Not Found');
                    exit();
                }
                $this->api_mova = true;
            }
        }

        public function index($data) {
            $this->mova_api();
            return $this
                ->start($data)
                ->load_model()
                ->load_lib()
                ->load_language_get()
                ->load_settings()
                ->load_access()
                ->mova_render()
                ->load_view_settings()
                ->load_view();
        }

        private function get_data() {
            return $this->data;
        }

        private function start($data) {
            $this->data = $data;
            if (!empty($data)) {
                $this->data = $data;
            }
            $this->data['index'] = true;

            $this->widget = 'seolang/mova/mova';

            // Get settins widget
            $data_widget = $this->model_seolang_seolang->getSetting($this->data['setting']['name'], $this->data['store_id']);

            $this->data['setting'] = array_merge($this->data['setting'], $data_widget);

            return $this;
        }

        private function load_model($data = array()) {
            if (!$this->data['index']) {
                return $this;
            }
            if (!empty($data)) {
                $this->data = $data;
            }

            if (!is_object($this->model_seolang_mova_mova)) {
                $this->load->model($this->widget);
            }
            return $this;
        }

        private function load_lib($data = array()) {
            if (!$this->data['index']) {
                return $this;
            }
            if (!empty($data)) {
                $this->data = $data;
            }
            // Nothing to load in this widget. Disabled request in method start of widget
            return $this;
        }

        private function load_language_get($data = array()) {
            if (!$this->data['index']) {
                return $this;
            }
            if (!empty($data)) {
                $this->data = $data;
            }

            $this->language->load('seolang/mova/mova');
            $this->data['text_seolang_mova_sort'] = $this->language->get('text_seolang_mova_sort');

            return $this;
        }

        private function load_settings($data = array()) {
            if (!$this->data['index']) {
                return $this;
            }
            if (!empty($data)) {
                $this->data = $data;
            }
            if (!isset($this->data['settings_widget']['cookie_auto_days']) || $this->data['settings_widget']['cookie_auto_days'] == '') {
                $this->data['settings_widget']['cookie_auto_days'] = '30';
            }
            return $this;
        }

        private function load_access($data = array()) {
            if (!$this->data['index']) {
                return $this;
            }
            if (!empty($data)) {
                $this->data = $data;
            }
            // Check access customer
            $this->data = $this->checkAccess($this->data);
            $this->data = $this->checkDevice($this->data);
            return $this;
        }

        private function checkAccess($data) {
            if (!$this->data['index']) {
                return $this->data;
            }
            if (!empty($data)) {
                $this->data = $data;
            }

            $this->data['access_widget'] = false;
            if (!isset($this->data['setting']['access']['customer_groups'])) {
                $this->data['setting']['access']['customer_groups'] = array();
            }
            // for all groups (-99) check, save queries
            if (!empty($this->data['setting']['access']['customer_groups'])) {
                foreach ($this->data['setting']['access']['customer_groups'] as $num => $val) {
                    if ($val == -99) {
                        $this->data['access_widget'] = true;
                        return $this->data;
                    }
                }
            }
            // if not all groups (-99) ... common go ahead in full "program" with many request and codes
            $this->data = $this->customer_groups($this->data);
            if (isset($this->data['setting']['access'])) {
                $setting_customer_groups = $this->data['setting']['access']['customer_groups'];
            } else {
                $setting_customer_groups = array();
            }
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

        private function checkDevice($data) {
            
            $this->is_desktop = false;
            $this->is_mobile = false;
            $this->is_tablet = false;
            $this->is_smart = false;

            if ((isset($this->data['setting']['device']) && $this->data['setting']['device'] != 0) || 
                (isset($this->data['setting']['mobile_detect']) && $this->data['setting']['mobile_detect'])) {

                if (file_exists(DIR_SYSTEM . 'journal2/lib/Mobile_Detect.php')) {
                    if (!class_exists('Mobile_Detect', false)) {
                        require_once(DIR_SYSTEM . 'journal2/lib/Mobile_Detect.php');
                    }
                    $detect = new Mobile_Detect;
                } else {
                    if (!class_exists('seolang_Mobile_Detect', false)) {
                        if (function_exists('modification')) {
                            require_once(modification(DIR_SYSTEM . 'library/seolang/mobile_detect.php'));
                        } else {
                            require_once(DIR_SYSTEM . 'library/seolang/mobile_detect.php');
                        }
                    }
                    $detect = new seolang_Mobile_Detect;
                }

                if ($detect->isMobile()) {
                    $this->is_mobile = true;
                }

                if ($detect->isTablet()) {
                    $this->is_tablet = true;
                }

                if (!$this->is_tablet && !$this->is_mobile) {
                    $this->is_desktop = true;
                }

                if (!$this->is_tablet && $this->is_mobile) {
                    $this->is_smart = true;
                }
            } else {
                $this->is_desktop = true;
                $this->is_mobile = true;
                $this->is_tablet = true;
                $this->is_smart = true;
            }

            $this->data['is_device'] = 'comp';

            if (isset($this->data['setting']['mobile_detect']) && $this->data['setting']['mobile_detect']) {
                if ($this->is_mobile || $this->is_tablet || $this->is_smart) {
                    $this->data['is_device'] = 'mobile';
                }
            }

            if (isset($this->data['setting']['device']) && $this->data['setting']['device'] != 0) {
                $this->device = array(1 => (int)$this->is_desktop, 2 => (int)$this->is_mobile, 3 => (int)$this->is_smart, 4 => (int)$this->is_tablet);


                if (isset($this->device[$this->data['setting']['device']]) && $this->device[$this->data['setting']['device']]) {
                } else {
                    $this->data['access_widget'] = false;
                }
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

            $this->data['customer_groups'][$gr] = array();

            if (is_object($this->customer) && $this->customer->isLogged()) {
                $this->data['customer_group_id'] = $this->customer->$get_Customer_GroupId();
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
                if (is_object($this->customer) && $this->customer->isLogged()) {
                    $customer_order = $this->model_seolang_mova_mova->getCustomerOrder((int)$this->data['customer_id'], $this->data['setting']['complete_status'], (int)$this->request->get['product_id']);
                }
                if ($customer_order) {
                    array_push($this->data['customer_groups'][$gr], -3);
                }
            }

            if (is_object($this->customer) && $this->customer->isLogged() && !$customer_order) {
                $customer_order = $this->model_seolang_mova_mova->getCustomerOrder((int)$this->data['customer_id'], $this->data['setting']['complete_status'], 0);
            }
            if ($customer_order) {
                array_push($this->data['customer_groups'][$gr], -2);
            }

            return $this->data;
        }

        private function load_view_settings($data = array()) {

            if (!empty($data)) {
                $this->data = $data;
            }
            
            // for array settings device comp or mobile convert vars
            foreach ($this->data['setting'] as $name => $vars) {
                if ($name == $this->data['is_device']) {
                    foreach ($vars as $name_var => $vars_var) {

                        if ($vars_var == '') {
                            if ($this->data['setting']['comp'][$name_var] != '') {
                                $vars_var = $this->data['setting']['comp'][$name_var];
                            }
                        }

                        $this->data['settings_widget'][$name_var] = $vars_var;
                    }
                }
            }

            if (!isset($this->data['settings_widget']['window_width']) || $this->data['settings_widget']['window_width'] == '') {
                $this->data['settings_widget']['window_width'] = 600;
            } else {
                $this->data['settings_widget']['window_width'] = trim(str_ireplace('px', '', $this->data['settings_widget']['window_width']));
            }


            if (!isset($this->data['settings_widget']['window_opacity']) || $this->data['settings_widget']['window_opacity'] == '') {
                $this->data['settings_widget']['window_opacity'] = 1;
            } else {
                $this->data['settings_widget']['window_opacity'] = trim(str_ireplace(array(',', 'ю'), array('.', '.'), $this->data['settings_widget']['window_opacity']));
            }      

            // Get and convert the settings
            if (isset($this->data['setting']['title'][$this->config->get('config_language_id')]) && $this->data['setting']['title'][$this->config->get('config_language_id')] != '') {
                $this->data['title'] = html_entity_decode($this->data['setting']['title'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
            } else {
                $this->data['title'] = '';
            }
            if (!empty($this->data['settings_widget']['title'])) {
                foreach ($this->data['settings_widget']['title'] as $language_id => $title) {
                    if ($title != '') {
                        $this->data['settings_widget']['title'][$language_id] = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
                    }
                }
            }
            // Get and convert the settings
            if (isset($this->data['setting']['code_custom'][$this->config->get('config_language_id')]) && $this->data['setting']['code_custom'][$this->config->get('config_language_id')] != '') {
                $this->data['code_custom'] = html_entity_decode($this->data['setting']['code_custom'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
            } else {
                $this->data['code_custom'] = '';
            }
            if (!empty($this->data['settings_widget']['code_custom'])) {

                foreach ($this->data['settings_widget']['code_custom'] as $language_id => $code_custom) {
                    if ($code_custom != '') {
                        $this->data['settings_widget']['code_custom'][$language_id] = html_entity_decode($code_custom, ENT_QUOTES, 'UTF-8');
                    }
                }
            }

            if (!empty($this->data['settings_widget']['html'])) {
                foreach ($this->data['settings_widget']['html'] as $language_id => $html) {
                    if ($html != '') {
                        $this->data['settings_widget']['html'][$language_id] = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
                    }
                }
            }

            if (!empty($this->data['settings_widget']['lm_text_close'])) {
                foreach ($this->data['settings_widget']['lm_text_close'] as $language_id => $lm_text_close) {
                    if ($lm_text_close != '') {
                        $this->data['settings_widget']['lm_text_close'][$language_id] = html_entity_decode($lm_text_close, ENT_QUOTES, 'UTF-8');
                    }
                }
            }

            if (!empty($this->data['settings_widget']['box_begin'])) {
                foreach ($this->data['settings_widget']['box_begin'] as $language_id => $box_begin) {
                    if ($box_begin != '') {
                        $this->data['settings_widget']['box_begin'][$language_id] = html_entity_decode($box_begin, ENT_QUOTES, 'UTF-8');
                    }
                }
            }
            if (!empty($this->data['settings_widget']['box_end'])) {
                foreach ($this->data['settings_widget']['box_end'] as $language_id => $box_end) {
                    if ($box_end != '') {
                        $this->data['settings_widget']['box_end'][$language_id] = html_entity_decode($box_end, ENT_QUOTES, 'UTF-8');
                    }
                }
            }

            if (!empty($this->data['settings_widget']['lang_name'])) {
                foreach ($this->data['settings_widget']['lang_name'] as $language_id => $lang_name) {
                    if ($lang_name != '') {
                        html_entity_decode($lang_name, ENT_QUOTES, 'UTF-8');
                        $this->data['settings_widget']['lang_name'][$language_id] = html_entity_decode($lang_name, ENT_QUOTES, 'UTF-8');

                        foreach ($this->data['languages'] as $ls_prefix => $ls_val) {
                            if ($ls_val['language_id'] == $language_id && $this->config->get('config_store_id') == $ls_val['store_id']) {
                                $this->data['languages'][$ls_prefix]['name'] = html_entity_decode($lang_name, ENT_QUOTES, 'UTF-8');
                            }
                        }
                    }
                }
            }

            if (!$this->data['index']) {
                return $this;
            }



            return $this;
        }

        private function load_view($data = array()) {

            if (!$this->data['index']) {
                return $this->data;
            }

            if (!empty($data)) {
                $this->data = $data;
            }

            if (isset($this->data['setting']['langswitch_replace']) && $this->data['setting']['langswitch_replace']) {
                $folder_template = 'agootemplates/record/';
            } else {
                $folder_template = 'agootemplates/widgets/langmark/';
            }

            if (!isset($this->data['setting']['template']) || (isset($this->data['setting']['template']) && $this->data['setting']['template'] == '')) {
                $template = $folder_template . 'langmark.tpl';
            } else {
                $template = $folder_template . $this->data['setting']['template'];
            }


            $template_info = pathinfo($template);

            if (isset($template_info['extension']) && $template_info['extension'] != '') {
                $template = $template_info['dirname'] . '/' . $template_info['filename'];
            }

            $this->data['template'] = $this->seolanglib->template($template);

            return $this->data;
        }

        private function mova_render() {
            if (!$this->data['index']) {
                return $this;
            }

            $this->data = $this->run($this->data['setting']);
            $this->load_view_settings();
            $this->load_view();

            $this->data['output'] = $this->load_render();

            if (isset($this->data['setting']['langswitch_replace']) && $this->data['setting']['langswitch_replace']) {

                $this->load->lm_reset_data();

                if (SC_VERSION < 20) {
                    $this->load->lm_set_replacedata(array('language' => $this->data['output']));
                } else {
                    $this->load->lm_set_replacedata(array('common/language' => $this->data['output']), 'common/language');
                }

                $this->data['index'] = false;
            }

            if (!isset($this->data['settingswidget']['langswitch_replace']) || !$this->data['settingswidget']['langswitch_replace']) {
                $run_status = true;
                if (isset($this->data['settingswidget']['cookie']) && $this->data['settingswidget']['cookie'] != '') {
                    $run_status = false;

                    if (!isset($this->request->cookie[$this->data['settingswidget']['cookie']])) {
                        $run_status = true;
                    }
                }

                if ($run_status) {
                    if (isset($this->data['settingswidget']['cookie_set']) && $this->data['settingswidget']['cookie_set'] != '') {
                        setcookie($this->data['settingswidget']['cookie_set'], '', -1, '/', $this->host);
                        setcookie($this->data['settingswidget']['cookie_set'], true, time() + 60 * 60 * 24 * 30, '/');
                        $this->request->cookie[$this->data['settingswidget']['cookie_set']] = true;
                    }
                } else {

                    if (isset($this->data['settingswidget']['cookie']) && $this->data['settingswidget']['cookie'] != '') {
                        $this->data['index'] = false;
                    }
                    return $this;
                }
            }

            return $this;
        }

        private function remove_vars() {

            if (isset($this->langmark_settings['pagination_prefix'])) {
                $pagination_prefix = $this->langmark_settings['pagination_prefix'];
            } else {
                $pagination_prefix = 'page';
            }

            if (isset($this->langmark_settings['description_status']) && $this->langmark_settings['description_status']) {

                if (isset($this->request->get[$pagination_prefix]) && $this->request->get[$pagination_prefix] > 1) {

                    if (isset($this->langmark_settings['desc_type']) && !empty($this->langmark_settings['desc_type'])) {
                        foreach ($this->langmark_settings['desc_type'] as $desc_type) {
                            $ex_vars_array = explode(PHP_EOL, trim($desc_type['vars']));
                            $array_replace = array();
                            foreach ($ex_vars_array as $num => $ex_var) {
                                $ex_var = trim($ex_var);
                                if ($ex_var[0] != '#' && $ex_var != '') {
                                    $array_replace[$ex_var] = '';
                                }
                            }

                            if (!empty($array_replace)) {
                                $this->load->lm_set_replacedata($array_replace, $desc_type['title']);
                            }
                        }
                    }
                }
            }
        }

        private function load_render() {

            if (isset($this->data['template']) && $this->data['template'] != '') {
                if (SC_VERSION < 20) {
                    $this->template = $this->data['template'];
                    if (isset($this->data['children']) && !empty($this->data['children'])) {
                        $this->children = $this->data['children'];
                    } else {
                        $this->children = array();
                    }
                    $this->data['output'] = $this->render();
                } else {
                    $this->data['output'] = $this->load->view($this->data['template'], $this->data);
                }
            }
            return $this->data['output'];
        }

        public function getMulti() {
            $this->mova_api();

            $multi = $this->registry->get('langmark_multi');

            if (!isset($multi['name'])) {
                if (isset($this->session->data['langmark_multi']['name']) && isset($this->settings['multi'][$this->session->data['langmark_multi']['name']])) {
                    $multi = $this->settings['multi'][$this->session->data['langmark_multi']['name']];
                } else {
                    $multi = false;
                }
            }

            /*
                $settings = $this->config->get('asc_langmark_' . $this->config->get('config_store_id'));
                if ($settings && (empty($settings['multi']) || count($settings['multi']) < 2)) {
                    $settings = false;
                }

                if ($settings && !$multi) {
                    foreach ($settings['multi'] as $name => $settings_multi) {
                        if (isset($settings_multi['name']) && $this->config->get('config_language_id') == $settings_multi['language_id']) {
                            $this->registry->set('langmark_multi', $settings_multi);
                            break;
                        }
                    }
                }
           
            */

            return $multi;
        }

        private function run_construct() {

            if (!defined('SC_VERSION')) define('SC_VERSION', substr(str_replace('.', '', VERSION), 0, 2));

            if ((isset($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || (isset($this->request->server['X-Requested-With']) && strtolower($this->request->server['X-Requested-With']) == 'xmlhttprequest')) {
                $this->jetcache_buildcache = false;
                $jetcache_headers = getallheaders();
                if (isset($jetcache_headers['JETCACHE_BUILDCACHE'])) {
                    $this->jetcache_buildcache = true;
                }
            }

            $this->host = parse_url(HTTP_SERVER);
            $this->host = $this->host['host'];

            if ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) == 'on'))) {
                $this->protocol = 'https';
            } else {
                $this->protocol = 'http';
            }

            if ($this->protocol == 'https') {
                $config_url_0 = HTTPS_SERVER;
            } else {
                $config_url_0 = HTTP_SERVER;
            }

            $this->load->model('setting/store');

            $this->data['stores'][0] = array(
                'store_id' => 0,
                'name'     => $this->config->get('config_name'),
                'url' => $config_url_0
            );

            $stores = $this->model_setting_store->getStores();

            foreach ($stores as $result) {
                if ($this->protocol == 'https' && isset($result['ssl']) && $result['ssl'] != '') {
                    $store_url = $result['ssl'];
                } else {
                    $store_url = $result['url'];
                }

                $this->data['stores'][$result['store_id']] = array(
                    'store_id' => $result['store_id'],
                    'name' => $result['name'],
                    'url' => $store_url
                );
            }

            if (isset($this->registry->get('request')->server['HTTP_USER_AGENT']) && $this->registry->get('request')->server['HTTP_USER_AGENT'] != '') {
                $this->http_user_agent = $this->registry->get('request')->server['HTTP_USER_AGENT'];
            } else {
                $this->http_user_agent = '';
            }

            $this->settings = $this->config->get('asc_langmark_' . $this->config->get('config_store_id'));

            if (isset($this->data['seolang_settings']['debug']) && $this->data['seolang_settings']['debug']) {

                if (isset($this->request->server['REQUEST_URI']) && $this->request->server['REQUEST_URI'] != '') {
                    $uri = $this->request->server['REQUEST_URI'];
                } else {
                    $uri = '';
                }
                $this->log->write($uri);

                if (isset($this->request->server['HTTP_REFERER'])) {
                    $this->log->write($this->request->server['HTTP_REFERER']);
                }

                if (isset($this->http_user_agent) && $this->http_user_agent != '') {

                    if (isset($this->request->server['REMOTE_ADDR']) && $this->request->server['REMOTE_ADDR'] != '') {
                        $ip = $this->request->server['REMOTE_ADDR'];
                    } else {
                        $ip = '';
                    }

                    $this->log->write($ip . " -> " . $this->http_user_agent);
                }
            }
            return $this;
        }

        private function run($settingswidget = array()) {

            if (empty($settingswidget)) {
                if ($this->registry->get('lm_settingswidget')) {
                    $settingswidget = $this->registry->get('lm_settingswidget');
                }
            }

            $this->run_construct();

            $this->data['settingswidget'] = $this->data['settings_widget'] = $settingswidget;

            $this->config->set('blog_work', true);
            $this->load->model('localisation/language');

            $html = '';
            $language_code = $this->config->get('config_language');
            $language_id = $this->config->get('config_language_id');
            $store_id = $this->config->get('config_store_id');
            $this->data['config_store_id'] = $store_id;

            $this->data['languages'] = array();
            $array_hreflang = array();
            $array_hre = array();

            if (SC_VERSION < 20) {
                $this->load->language('module/language');
            } else {
                $this->load->language('common/language');
            }

            $this->data['text_language'] = $this->language->get('text_language');
            $this->data['action'] = '';
            $this->data['code'] = $this->data['language_code'] = $this->session->data['language'];

            if (isset($this->request->get['route']) && $this->request->get['route'] != '') {
                $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->request->get['route']);
            } else {
                $route = 'common/home';
            }

            $langmark_multi = $this->getMulti();


            $current_config_store_id = $this->config->get('config_store_id');

            krsort($this->data['stores']);

            foreach ($this->data['stores'] as $store_num => $store) {

                $this->config->set('config_store_id', $store['store_id']);

                $settings = $this->config->get('asc_langmark_' . $store['store_id']);

                if (isset($settings['use_link_status']) && !$settings['use_link_status']) {

                    $url_current = $this->url->link($route, $this->getQueryString(array(
                        'route',
                        '_route_',
                        'site_language',
                        'tag'
                    )));

                    // $url_current = str_replace('&amp;', '&', $url_current);
                    $prefix_current = $langmark_multi['prefix'];

                    $pos_current = stripos($url_current,  $prefix_current);
                    $pos_current_len = $pos_current + strlen($prefix_current);
                    $substr_pos_current_len = substr($url_current, $pos_current_len, 1);
                    $substr_prefix_current = substr($prefix_current, -1);
                }



                $languages = $this->model_localisation_language->getLanguages();

                if (isset($languages) && is_array($languages)) {
                    usort($languages, function ($a, $b) {
                        if ($a['sort_order'] == '') $a['sort_order'] = 1000;
                        if ($b['sort_order'] == '') $b['sort_order'] = 1000;

                        if ($a['sort_order'] > $b['sort_order']) {
                            return 1;
                        } else {
                            return -1;
                        }
                    });
                }


                foreach ($languages as $result) {
                    $languages_by_id[$result['language_id']] = $result;
                }

                if (!empty($settings['multi'])) {

                    foreach ($settings['multi'] as $name => $settings_multi) {

                        $this->config->set('config_store_id', $settings_multi['store_id']);

                        if (isset($languages_by_id[$settings_multi['language_id']]) && $languages_by_id[$settings_multi['language_id']]['status']) {

                            if (isset($settings_multi['prefix_switcher_stores']) && $settings_multi['prefix_switcher_stores'] || $store['store_id'] == $store_id) {

                                if ((isset($settings_multi['hreflang_switcher']) && $settings_multi['hreflang_switcher']) || (isset($settings_multi['prefix_switcher']) && $settings_multi['prefix_switcher'])) {

                                    if (!isset($settings['use_link_status']) || (isset($settings['use_link_status']) && $settings['use_link_status'])) {

                                        if (isset($settings_multi['name'])) {
                                            // Why ?
                                            //$settings_multi['store_id'] = $store['store_id'];
                                            $this->registry->set('langmark_multi', $settings_multi);
                                        }

                                        $this->switchLanguage($settings, $settings_multi['language_id'], $languages_by_id[$settings_multi['language_id']]['code']);

                                        $url_lang = $this->url->link($route, $this->getQueryString(array(
                                            'route',
                                            '_route_',
                                            'site_language',
                                            'tag'
                                        )));
                                        //$url_lang = str_replace('&amp;', '&', $url_lang);
                                    } else {

                                        $prefix_replace = $settings_multi['prefix'];

                                        $substr_prefix_replace = substr($prefix_replace, -1);

                                        if ($substr_prefix_current != '/' && $substr_prefix_replace == '/' && $substr_pos_current_len == '/') {
                                            $prefix_replace = substr($prefix_replace, 0, -1);
                                        }

                                        if ($substr_prefix_current == '/' && $substr_prefix_replace != '/' && $substr_pos_current_len != '/' && $substr_pos_current_len) {
                                            $prefix_replace = $prefix_replace . '/';
                                        }

                                        $url_lang = str_ireplace($prefix_current,  $prefix_replace, $url_current);
                                        // $url_lang = str_replace('&amp;', '&', $url_lang);
                                    }
                                }
                                $run_href = true;
                                $hreflang = $settings_multi['hreflang'];


                                if (isset($settings_multi['hreflang_switcher']) && $settings_multi['hreflang_switcher']) {

                                    if ($settings_multi['store_id'] != $store_id) {
                                        if (isset($settings_multi['hreflang_switcher_stores']) && $settings_multi['hreflang_switcher_stores']) {
                                        } else {
                                            $run_href = false;
                                        }
                                    }

                                    if ($run_href && $hreflang != '') {
                                        $array_href[$settings_multi['hreflang']] = array('href' => $url_lang, 'hreflang' => $settings_multi['hreflang']);
                                    }
                                }

                                if (isset($settings_multi['prefix_switcher']) && $settings_multi['prefix_switcher']) {

                                    //if ($settings_multi['prefix'] == $langmark_multi['prefix']) {
                                    if ($settings_multi['prefix'] == $langmark_multi['prefix'] && $langmark_multi['store_id'] == $current_config_store_id) {
                                        $current = true;
                                    } else {
                                        $current = false;
                                    }

                                    if (!isset($languages_by_id[$settings_multi['language_id']]['image'])) {
                                        $languages_by_id[$settings_multi['language_id']]['image'] = 'catalog/language/' . $languages_by_id[$settings_multi['language_id']]['code'] . '/' . $languages_by_id[$settings_multi['language_id']]['code'] . '.png';
                                    }
                                    if (isset($settings_multi['prefix_main']) && $settings_multi['prefix_main'] && $settings_multi['store_id'] == $current_config_store_id) { //
                                        $prefix_main = true;
                                    } else {
                                        $prefix_main = false;
                                    }

                                    if ($prefix_main && isset($settings_multi['hreflang_switcher']) && $settings_multi['hreflang_switcher'] && isset($this->settings['xdefault_status']) && $this->settings['xdefault_status'] && !empty($array_href)) {
                                        $array_href['x-default'] = array('href' => $url_lang, 'hreflang' => 'x-default');
                                    }
                                    if (!isset($settings_multi['multi_sort'])) {
                                        $settings_multi['multi_sort'] = '';
                                    }
                                    $this->data['languages'][$url_lang] = array(
                                        'url'  => $url_lang,
                                        'name'  => $settings_multi['name'],
                                        'language'  => $languages_by_id[$settings_multi['language_id']]['name'],
                                        'code'  => $languages_by_id[$settings_multi['language_id']]['code'],
                                        'code_short'  => strtolower(substr($languages_by_id[$settings_multi['language_id']]['code'], 0, 2)),
                                        'image' => $languages_by_id[$settings_multi['language_id']]['image'],
                                        'store_id' => $store['store_id'],
                                        'store_name' => $store['name'],
                                        'language_id'  => $settings_multi['language_id'],
                                        'main' => $prefix_main,
                                        'multi_sort' => $settings_multi['multi_sort'],
                                        'current' =>  $current
                                    );
                                }
                            }
                        }
                    }
                }

                if (isset($this->data['languages']) && is_array($this->data['languages'])) {

                    $multi = $this->data['languages'];

                    uasort($multi, function ($a, $b) {

                        if (!isset($a['multi_sort'])) $a['multi_sort'] = '';
                        if (!isset($b['multi_sort'])) $b['multi_sort'] = '';

                        if ($a['multi_sort'] == '') $a['multi_sort'] = 1000;
                        if ($b['multi_sort'] == '') $b['multi_sort'] = 1000;

                        if ($a['multi_sort'] >= $b['multi_sort']) {
                            return 1;
                        } else {
                            return -1;
                        }
                    });

                    $this->data['languages'] = $multi;
                }


                $this->registry->set('langmark_multi', $langmark_multi);

                $this->session->data['langmark_multi'] = array();
                $this->session->data['langmark_multi']['name'] = $langmark_multi['name'];
                $this->session->data['langmark_multi']['store_id'] = $langmark_multi['store_id'];

                $this->switchLanguage($this->settings, $language_id, $language_code);

                if (isset($this->settings['hreflang_status']) && $this->settings['hreflang_status']) {
                    if (!empty($array_href)) {
                        $this->registry->set('Lm_hreflang', $array_href);
                    }
                }
            }

            $this->config->set('config_store_id', $current_config_store_id);

            $this->registry->set('langmark_multi', $langmark_multi);

            $this->session->data['langmark_multi'] = array();
            $this->session->data['langmark_multi']['name'] = $langmark_multi['name'];
            $this->session->data['langmark_multi']['store_id'] = $langmark_multi['store_id'];

            $this->data['settings_widget']['bot'] = false;
            $this->data['settings_widget']['autoredirect_delay_mobile'] = 11000;
            $this->data['settings_widget']['autoredirect_delay_desktop'] = 5000;

            $this->bots();
            $this->ex_langs();
            $this->ex_gets();

            if (!empty($this->data['settings_widget']['title'])) {
                foreach ($this->data['settings_widget']['title'] as $language_id => $title) {

                    if ($title != '') {
                        $this->data['settings_widget']['title'][$language_id] = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
                    }
                }
            }
            if (!empty($this->data['settings_widget']['code_custom'])) {
                foreach ($this->data['settings_widget']['code_custom'] as $language_id => $code_custom) {

                    if ($code_custom != '') {
                        $this->data['settings_widget']['code_custom'][$language_id] = html_entity_decode($code_custom, ENT_QUOTES, 'UTF-8');
                    }
                }
            }
            $this->data['html'] = 'Language';

            if (isset($this->data['settings_widget']['bot']) && $this->data['settings_widget']['bot']) {
                $html = '';
            }
            $this->data['language'] = $this->language;
            $this->data['theme'] = $this->seolanglib->theme_folder;

            return $this->data;
        }

        private function ex_langs() {
            if (!isset($this->data['settings_widget']['autopopup'])) {
                $this->data['settings_widget']['autopopup'] = true;
            }
            if (isset($this->data['settings_widget']['autoredirect_langs_ex']) && !empty($this->data['settings_widget']['autoredirect_langs_ex'])) {
                foreach ($this->data['settings_widget']['autoredirect_langs_ex'] as $num => $language_id) {
                    $language_id = (int)$language_id;
                    if ($this->config->get('config_language_id') == $language_id) {
                        $this->data['settings_widget']['autoredirect'] = false;
                        $this->data['settings_widget']['autopopup'] = false;
                        $this->data['settings_widget']['bot'] = true;
                        break;
                    }
                }
            }
        }


        private function ex_gets() {
            if (
                (isset($this->data['settings_widget']['autoredirect']) && $this->data['settings_widget']['autoredirect'] || isset($this->data['settings_widget']['autopopup']) && $this->data['settings_widget']['autopopup'])
                && isset($this->data['settings_widget']['ex_gets_status']) && $this->data['settings_widget']['ex_gets_status']
                && !empty($this->data['seolang_settings']['ex_gets'])
            ) {

                if (!isset($this->data['settings_widget']['cookie_auto']) || $this->data['settings_widget']['cookie_auto'] == '') {
                    $this->data['settings_widget']['cookie_auto'] = 'languageauto';
                }
                if (!isset($this->data['settings_widget']['cookie_auto_days']) || $this->data['settings_widget']['cookie_auto_days'] == '') {
                    $this->data['settings_widget']['cookie_auto_days'] = '30';
                }

                $string_array_explode = explode(PHP_EOL, trim($this->data['seolang_settings']['ex_gets']));
                foreach ($this->request->get as $parameter => $value) {
                    foreach ($string_array_explode as $num => $token_get) {
                        $token_get = trim($token_get);
                        if ($token_get != '' && $token_get[0] != '#' && trim($parameter) == trim($token_get)) {
                            $this->data['settings_widget']['autoredirect'] = false;
                            $this->data['settings_widget']['autopopup'] = false;
                            $this->data['settings_widget']['bot'] = true;
                            $this->request->cookie['languager'] = '1';
                            $this->request->cookie[$this->data['settings_widget']['cookie_auto']] = '1';
                            setcookie('languager', '1', time() + 60 * 60 * 24 * (int)$this->data['settings_widget']['cookie_auto_days'], '/', $this->host);
                            setcookie($this->data['settings_widget']['cookie_auto'], '1', time() + 60 * 60 * 24 * (int)$this->data['settings_widget']['cookie_auto_days'], '/', $this->host);
                            break;
                        }
                    }
                }
            }
        }



        private function bots() {
            if (
                isset($this->http_user_agent)
                && isset($this->data['settings_widget']['autoredirect']) && $this->data['settings_widget']['autoredirect']
                && isset($this->data['settings_widget']['bots_status']) && $this->data['settings_widget']['bots_status']
                && !empty($this->data['seolang_settings']['bots'])
            ) {

                $string_array_explode = explode(PHP_EOL, trim($this->data['seolang_settings']['bots']));

                foreach ($string_array_explode as $num => $token_bot) {
                    $token_bot = trim($token_bot);
                    if ($token_bot != '' && $token_bot[0] != '#' && stripos($this->http_user_agent, $token_bot) !== false) {
                        $this->data['settings_widget']['autoredirect'] = false;
                        $this->data['settings_widget']['autopopup'] = false;
                        $this->data['settings_widget']['bot'] = true;
                        $this->request->cookie['languager'] = '1';
                        setcookie('languager', '1', time() + 60 * 60 * 24 * 30, '/', $this->host);
                        break;
                    }
                }
            }

            if (isset($this->data['settings_widget']['autoredirect'])  && $this->data['settings_widget']['autoredirect']) {
                if (
                    isset($this->data['settings_widget']['bots_status']) && $this->data['settings_widget']['bots_status']
                    && isset($this->data['settings_widget']['bot']) && !$this->data['settings_widget']['bot']
                ) {

                    if (isset($this->data['seolang_settings']['bots_delay_mobile']) && $this->data['seolang_settings']['bots_delay_mobile'] != '') {
                        $bots_delay_mobile = $this->data['seolang_settings']['bots_delay_mobile'];
                    } else {
                        $bots_delay_mobile = 300;
                    }
                    if (isset($this->data['seolang_settings']['bots_delay_desktop']) && $this->data['seolang_settings']['bots_delay_desktop'] != '') {
                        $bots_delay_desktop = $this->data['seolang_settings']['bots_delay_desktop'];
                    } else {
                        $bots_delay_desktop = 300;
                    }


                    $this->data['settings_widget']['autoredirect_delay_mobile'] = $bots_delay_mobile;
                    $this->data['settings_widget']['autoredirect_delay_desktop'] = $bots_delay_desktop;

                    setcookie('languager', '0', time() + 60 * 60 * 24 * 30, '/', $this->host);
                    $this->request->cookie['languager'] = '0';
                } else {
                    $this->data['settings_widget']['autoredirect_delay_mobile'] = 11000;
                    $this->data['settings_widget']['autoredirect_delay_desktop'] = 5000;
                }
            }
        }

        private function setpagination($seo_url) {

            if (isset($this->settings['pagination']) && $this->settings['pagination'] && isset($this->settings['seo_pagination']) && $this->settings['seo_pagination']) {

                if (isset($seo_url) && is_string($seo_url) && strpos($seo_url, 'page=') !== false) {

                    $component = parse_url(str_replace('&amp;', '&', $seo_url));

                    if (isset($component['path'])) {
                        $component['path'] = str_replace('/index.php', '', $component['path']);
                    } else {
                        $component['path'] = '';
                    }
                    if (substr($component['path'], -1) == '/') {
                        $slash_close = '/';
                    } else {
                        $slash_close = '';
                    }
                    $this->registry->set('langmark_slash_close', $slash_close);
                    $data_array = array();
                    if (isset($component['query'])) {
                        parse_str($component['query'], $data_array);
                    }

                    if (count($data_array)) {

                        $seo_url = '';
                        $paging = '';
                        $devider = '/';
                        foreach ($data_array as $key => $value) {

                            if ($key == $this->settings['pagination_prefix'] || $key == 'page') {
                                $key = $this->settings['pagination_prefix'];
                                if ($devider != '/') {
                                    $paging = '/' . $key . '-' . $value;
                                } else {
                                    $paging = $key . '-' . $value;
                                }

                                unset($data_array[$key]);
                                if (isset($data_array['page'])) {
                                    unset($data_array['page']);
                                }
                            }
                        }

                        if (trim($paging, '/') == $this->settings['pagination_prefix'] . '-1') {
                            $paging = '';
                        }

                        if (count($data_array)) {
                            $seo_url .= $paging . '?' . urldecode(http_build_query($data_array, '', '&amp;'));
                        } else {
                            $seo_url .= $paging;
                        }

                        if (trim($component['path']) == '') {
                            $mydel = '';
                        } else {
                            $mydel = '/';
                        }
                        $seo_url = $component['scheme'] . '://' . $component['host'] . '/' . trim($component['path'], '/') . $mydel . $seo_url;

                        if ($paging != '') {
                            $seo_url = rtrim($seo_url, '/');
                        }
                    }
                }
            }
            return $seo_url;
        }

        public function devopencart($output) {

            return $output;
        }

        public function responseseolang($output) {
            $this->mova_api();
            $output = $this->removepageone($output);
            $output = $this->hreflang($output);
            $output = $this->shortcodes($output);



            return $output;
        }


        private function removepageone($output) {

            if (!is_string($output)) return $output;

            if (isset($this->settings['pagination']) && $this->settings['pagination'] && isset($this->settings['seo_pagination']) && $this->settings['seo_pagination']) {
                if (isset($this->settings['pagination_prefix'])) {
                    $pagination_prefix = $this->settings['pagination_prefix'];
                } else {
                    $pagination_prefix = 'page';
                }

                if (strpos($output, '/' . $pagination_prefix . '-{page}') !== false) {
                    $output = str_replace('/' . $pagination_prefix . '-{page}', '/' . $pagination_prefix . '-1', $output);
                }

                if ($this->registry->get('langmark_slash_close')) {
                    $devider = $this->registry->get('langmark_slash_close');
                } else {
                    if (isset($this->settings['url_close_slash']) && $this->settings['url_close_slash']) {
                        $devider = '/';
                    } else {
                        $devider = '';
                    }
                }

                if (strpos($output, '/' . $pagination_prefix . '-1') !== false) {
                    $output = str_replace('/' . $pagination_prefix . "-1'", $devider . "'", $output);
                    $output = str_replace('/' . $pagination_prefix . '-1"', $devider . '"', $output);
                    $output = str_replace('/' . $pagination_prefix . "-1/", $devider, $output);
                    $output = str_replace('/' . $pagination_prefix . "-1?", $devider . "?", $output);
                    $output = str_replace('/' . $pagination_prefix . "-1/?", $devider . "?", $output);
                }

                $output = str_replace("//?", "/?", $output);
            }
            return $output;
        }


        private function getQueryString($exclude = array()) {
            if (!is_array($exclude)) {
                $exclude = array();
            }
            return urldecode(http_build_query(array_diff_key($this->request->get, array_flip($exclude))));
        }


        public function switchLanguage($settings, $language_id, $code) {

            $this->mova_api();

            $ajax = false;

            if (isset($this->request->server['HTTP_ACCEPT'])) {

                if (strpos(strtolower($this->request->server['HTTP_ACCEPT']), strtolower('image')) !== false) {

                    if (strpos(strtolower($this->request->server['HTTP_ACCEPT']), strtolower('html')) !== false) {
                        $ajax = false;
                    } else {
                        $ajax = true;
                    }
                }

                if (strpos(strtolower($this->request->server['HTTP_ACCEPT']), strtolower('js')) !== false) {
                    $ajax = true;
                }

                if (strpos(strtolower($this->request->server['HTTP_ACCEPT']), strtolower('json')) !== false) {
                    $ajax = true;
                }

                if (strpos(strtolower($this->request->server['HTTP_ACCEPT']), strtolower('ajax')) !== false) {
                    $ajax = true;
                }

                if (strpos(strtolower($this->request->server['HTTP_ACCEPT']), strtolower('javascript')) !== false) {
                    $ajax = true;
                }
            }

            if (isset($settings['ex_multilang_route']) && $settings['ex_multilang_route'] != '') {
                $ex_multilang_route = $settings['ex_multilang_route'];
                $ex_multilang_route_array = explode(PHP_EOL, $ex_multilang_route);
                if (isset($this->request->get['route'])) {
                    foreach ($ex_multilang_route_array as $ex_route) {
                        if (trim($ex_route) != '') {
                            if (utf8_strpos(utf8_strtolower($this->request->get['route']), trim($ex_route)) !== false) {
                                $ajax = true;
                            }
                        }
                    }
                }
            }

            if (isset($settings['ex_multilang_uri']) && $settings['ex_multilang_uri'] != '') {
                $ex_multilang_uri = $settings['ex_multilang_uri'];
                $ex_multilang_uri_array = explode(PHP_EOL, $ex_multilang_uri);
                if (isset($this->request->server['REQUEST_URI'])) {
                    foreach ($ex_multilang_uri_array as $ex_uri) {
                        if (trim($ex_uri) != '') {
                            if (utf8_strpos(utf8_strtolower($this->request->server['REQUEST_URI']), trim($ex_uri)) !== false) {
                                $ajax = true;
                            }
                        }
                    }
                }
            }

            if ((isset($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || (isset($this->request->server['X-Requested-With']) && strtolower($this->request->server['X-Requested-With']) == 'xmlhttprequest')) {
                if (!$this->jetcache_buildcache) {
                    $ajax = true;
                }
            }

            if ($code != '' && !$ajax) {
                /*
                if ($settings) {
                    foreach ($settings['multi'] as $name => $settings_multi) {
                        if (isset($settings_multi['name']) && $language_id == $settings_multi['language_id']) {
                            $this->registry->set('langmark_multi', $settings_multi);
                            break;
                        }
                    }
                }
                */
                $this->config->set('config_language_id', $language_id);
                $this->config->set('config_language', $code);
                $this->session->data['language'] = $code;
            }
        }

        private function hreflang($output) {

            if (isset($this->request->get['route']) && $this->request->get['route'] != '') {
                $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->request->get['route']);
            } else {
                $route = 'common/home';
            }

            if (isset($output) && !$this->registry->get('admin_work') && $route != 'error/not_found') {
                if (!is_array($output) && stripos($output, '</head>') !== false && (stripos($output, '<link rel="alternate"') === false || stripos($output, "<link rel='alternate'") === false)) {
                    $lm_hreflang = $this->registry->get('Lm_hreflang');

                    if (!empty($lm_hreflang)) {
                        foreach ($lm_hreflang as $lm_hreflang_code => $lm_hreflang_array) {
                            $output = str_replace("</head>", '<link rel="alternate" hreflang="' . $lm_hreflang_array['hreflang'] . '" href="' . $lm_hreflang_array['href'] . '" />
</head>', $output);
                        }
                    }
                }
            }
            return $output;
        }

        private function shortcodes($output) {

            if (!isset($this->settings['access'])) {
                $this->run_construct();
            }
            if (!isset($this->settings['access']) || !$this->settings['access']) {
                return $output;
            }

            $langmark_multi = $this->getMulti();

            if (isset($output) && is_string($output) && !$this->registry->get('admin_work')) {

                if (!empty($langmark_multi['shortcodes'])) {

                    $output = str_replace(array("\r\n", "\r", "\n", PHP_EOL), '{{BR}}', $output);

                    foreach ($langmark_multi['shortcodes'] as $num => $shortcode) {


                        if ($shortcode['out'] == ' ') $shortcode['out'] = '';

                        $shortcode['in'] = str_replace(array("\r\n", "\r", "\n", PHP_EOL), '{{BR}}', $shortcode['in']);
                        $shortcode['out'] = str_replace(array("\r\n", "\r", "\n", PHP_EOL), '{{BR}}', $shortcode['out']);

                        if (stripos($shortcode['in'], '{{PAGINATION') !== false) {
                            if (isset($this->request->get['page']) && (int)$this->request->get['page'] > 1) {
                            } else {
                                $shortcode['out'] = '';
                            }
                        }

                        if (stripos($shortcode['in'], '{{PAGINATION_PAGE}}') !== false) {
                            if (isset($this->request->get['page']) && (int)$this->request->get['page'] > 1) {
                                if (stripos($shortcode['out'], '{{PAGINATION_NUM}}') !== false) {
                                    $shortcode['out'] = str_replace(array("{{PAGINATION_NUM}}"), (int)$this->request->get['page'], $shortcode['out']);
                                } else {
                                    $shortcode['out'] = (int)$this->request->get['page'] . $shortcode['out'];
                                }
                            } else {
                                $shortcode['out'] = '';
                            }
                        }

                        $output = str_replace(html_entity_decode($shortcode['in'], ENT_QUOTES, 'UTF-8'), html_entity_decode($shortcode['out'], ENT_QUOTES, 'UTF-8'), $output);
                    }

                    if (stripos($output, '{{PAGINATION_PAGE}}') !== false) {

                        if (isset($this->request->get['page']) && (int)$this->request->get['page'] > 1) {
                            $pagination_page = (int)$this->request->get['page'];
                        } else {
                            $pagination_page = '';
                        }

                        $output = str_replace('{{PAGINATION_PAGE}}', $pagination_page, $output);
                    }

                    $output = str_replace('{{BR}}', PHP_EOL, $output);
                }
            }
            return $output;
        }

        public function after($seo_url, $seo_url_route = '') {
            $this->mova_api();
            if (!is_string($seo_url)) {
                return $seo_url;
            }

            if (!isset($this->settings['access'])) {
                $this->run_construct();
            }
            if (!isset($this->settings['access']) || !$this->settings['access']) {
                return $seo_url;
            }

            if (isset($this->request->get['route']) && $this->request->get['route'] != '') {
                $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->request->get['route']);
            } else {
                $route = 'common/home';
            }

            if (SC_VERSION < 30) {
                if ($seo_url_route != '') {

                    if (isset($this->settings['ex_url_route']) && $this->settings['ex_url_route'] != '') {
                        $ex_url_route = $this->settings['ex_url_route'];
                        $ex_url_route_array = explode(PHP_EOL, $ex_url_route);

                        foreach ($ex_url_route_array as $ex_route) {
                            if (trim($ex_route) != '') {
                                if (utf8_strpos(utf8_strtolower($seo_url_route), trim($ex_route)) !== false) {
                                    $ajax = true;
                                    return $seo_url;
                                }
                            }
                        }
                    }
                }
            }

            if (isset($this->settings['ex_url_route']) && $this->settings['ex_url_route'] != '') {
                $ex_url_route = $this->settings['ex_url_route'];
                $ex_url_route_array = explode(PHP_EOL, $ex_url_route);

                if ($route != '') {
                    foreach ($ex_url_route_array as $ex_route) {
                        if (trim($ex_route) != '') {
                            if (utf8_strpos(utf8_strtolower($route), trim($ex_route)) !== false) {
                                $ajax = true;
                                return $seo_url;
                            }
                        }
                    }
                }
            }

            if (isset($this->settings['ex_url_uri']) && $this->settings['ex_url_uri'] != '') {
                $ex_url_uri = $this->settings['ex_url_uri'];
                $ex_url_uri_array = explode(PHP_EOL, $ex_url_uri);

                foreach ($ex_url_uri_array as $ex_uri) {
                    if (trim($ex_uri) != '') {
                        if (utf8_strpos(utf8_strtolower($seo_url), trim($ex_uri)) !== false) {
                            $ajax = true;
                            return $seo_url;
                        }
                    }
                }
            }

            $seo_url = $this->commonhome($seo_url);
            $seo_url = $this->seourl($seo_url);
            $seo_url = $this->setpagination($seo_url);
            $seo_url = $this->twoslaches($seo_url);
            $seo_url = $this->ex_amp($seo_url);

            return $seo_url;
        }

        private function ex_amp($seo_url) {
            $seo_url = str_replace('&', '&amp;', $seo_url);
            return $seo_url;
        }

        public function loadview() {
            $this->mova_api();
            $this->setpagenum();
            $this->setMain();
            $this->remove_vars();
        }

        private function setpagenum() {
            $this->mova_api();
            $langmark_multi = $this->getMulti();

            if (isset($this->settings['pagination']) && $this->settings['pagination']) {

                if (isset($this->request->get['page'])) {
                    $this->registry->set('langmark_page', (int)$this->request->get['page']);
                }

                if ($this->registry->get('langmark_page') != '') {

                    if (!$this->registry->get('langmark_title')) {

                        $title = $this->document->getTitle();
                        if ($title != '' && $langmark_multi['pagination_title'] != '' && utf8_strpos($title, $langmark_multi['pagination_title'] . ' ' . $this->registry->get('langmark_page')) === false) {
                            $this->document->setTitle($title .  ' ' . $langmark_multi['pagination_title'] . ' ' . (int)$this->registry->get('langmark_page'));
                        }
                    }

                    if (!$this->registry->get('langmark_desc')) {
                        $description = $this->document->getDescription();
                        if ($description != '' && $langmark_multi['pagination_title'] != '' && utf8_strpos($description, $langmark_multi['pagination_title'] . ' ' . $this->registry->get('langmark_page')) === false) {
                            $this->document->setDescription($description .  ' ' . $langmark_multi['pagination_title'] . ' ' . (int)$this->registry->get('langmark_page'));
                        }
                    }
                }
            }
        }

        private function seourl($seo_url) {

            $seo_url = str_replace('&amp;', '&', $seo_url);

            // From /system/library/seolang/seolang.php or from switch index()
            $langmark_multi = $this->getMulti();
            if (!is_array($langmark_multi)) {
                return $seo_url;
            }

            if (isset($langmark_multi['prefix'])) {
                $host_from_prefix = substr($langmark_multi['prefix'], 0, strpos($langmark_multi['prefix'], '/'));
            } else {
                $host_from_prefix = '';
            }

            $seo_url_parse = parse_url($seo_url);

            if (isset($seo_url_parse['query']) && $seo_url_parse['query'] != '') {
                $parse_query = '?' . $seo_url_parse['query'];
            } else {
                $parse_query = '';
            }
            $parse_query = str_replace('&amp;', '&', $parse_query);

            if (isset($seo_url_parse['scheme'])) {
                $protocol = $seo_url_parse['scheme'] . '://';
            } else {
                $protocol = 'http://';
                $seo_url_parse['scheme'] = 'http';
            }
            // & check ../folders/
            if (isset($seo_url_parse['host']) && substr_count(HTTP_SERVER, '/') < 4) {
                $host_seo_url = $seo_url_parse['host'];

                if ($seo_url_parse['host'] != $host_from_prefix && $host_from_prefix != '') {
                    $seo_url_parse['host'] = $host_from_prefix;
                }
                $config_url = $protocol . $seo_url_parse['host'] . '/';
            } else {

                if (!isset($seo_url_parse['scheme']) || (isset($seo_url_parse['scheme']) && $seo_url_parse['scheme'] == 'https')) {
                    $conf_ssl = $this->config->get('config_ssl');
                    if (!$conf_ssl) $conf_ssl = HTTPS_SERVER;
                    $config_url = $conf_ssl;
                    if (!isset($seo_url_parse['scheme'])) {
                        $seo_url_parse['scheme'] = 'https';
                    }
                } else {
                    $conf_url = $this->config->get('config_url');
                    if (!$conf_url) $conf_url = HTTP_SERVER;
                    $config_url = $conf_url;
                }
                $host_seo_url = '';
            }

            if ($host_seo_url != '') {
                $seo_url = str_replace($host_seo_url, $host_from_prefix, $seo_url);
            }

            if (strlen($seo_url) < strlen($config_url)) {
                $seo_url = $config_url;
            }

            $seo_url_len = str_replace($config_url, '', $seo_url);

            if (isset($langmark_multi['prefix']) && strlen($seo_url_len) > 0 && substr($langmark_multi['prefix'], -1) != '/' && (isset($seo_url_len[0]) && $seo_url_len[0] != '?')) {
                $divider = '/';
            } else {
                $divider = '';
            }

            if ($langmark_multi['main_prefix_status'] && $seo_url == $config_url . $parse_query) {
                $seo_url = $config_url;
            }

            // Error for generate URL other stores
            if ($seo_url == $config_url && $langmark_multi['main_prefix_status']) {

                if (isset($langmark_multi['store_id']) && $langmark_multi['store_id'] != $this->config->get('config_store_id') && isset($this->data['stores'][$langmark_multi['store_id']]['url'])) {
                    $seo_url = $this->data['stores'][$langmark_multi['store_id']]['url'];
                } else {
                    $seo_url = $config_url . $parse_query;
                }
            } else {

                if (isset($langmark_multi['prefix'])) {
                    $seo_url = str_replace($config_url, $protocol . $langmark_multi['prefix'] . $divider, $seo_url);
                } else {
                    $this->log->write($seo_url . ' -> ' . json_encode($langmark_multi));
                }
            }

            //$seo_url = str_replace('&', '&amp;', $seo_url);

            return $seo_url;
        }

        private function twoslaches($seo_url) {
            if (isset($this->settings['two_status']) && $this->settings['two_status']) {
                $seo_url = preg_replace('/(?<!^[http:]|[https:])[\/]{2,}/', '/', trim($seo_url));
            }
            return $seo_url;
        }

        private function commonhome($seo_url) {

            if (isset($this->settings['commonhome_status']) && $this->settings['commonhome_status']) {

                if (strpos(strtolower($seo_url), 'index.php?route=common/home') !== false) {
                    if ((utf8_strpos(utf8_strtolower($seo_url), '&') !== false) || (utf8_strpos(utf8_strtolower($seo_url), '&amp;') !== false)) {
                        $seo_url = str_replace('&amp;', '&', $seo_url);
                        $seo_url = str_replace('&', '&amp;', $seo_url);
                        $seo_url = str_replace('index.php?route=common/home&amp;', '?', $seo_url);
                    } else {
                        $seo_url = str_replace('index.php?route=common/home', '', $seo_url);
                    }
                }
            }
            return $seo_url;
        }

        private function setMain() {

            $this->langmark_settings  = $this->config->get('asc_langmark_' . $this->config->get('config_store_id'));

            if (!isset($this->langmark_settings['access']) || !$this->langmark_settings['access']) {
                return;
            }

            if (isset($this->request->get['route']) && $this->request->get['route'] != '') {
                $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->request->get['route']);
            } else {
                $route = 'common/home';
            }
            if ($route == 'common/home') {

                $langmark_multi = $this->getMulti();

                if (isset($langmark_multi['main_title']) && $langmark_multi['main_title'] != '') {
                    $this->document->setTitle($langmark_multi['main_title']);
                }

                if (isset($langmark_multi['main_description']) && $langmark_multi['main_description'] != '') {
                    $this->document->setDescription($langmark_multi['main_description']);
                }

                if (isset($langmark_multi['main_keywords']) && $langmark_multi['main_keywords'] != '') {
                    $this->document->setKeywords($langmark_multi['main_keywords']);
                }
            }
        }
    }
}
