<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerSeoLangSeoLang', false)) {
    class ControllerSeoLangSeoLang extends Controller {
        protected $data;
        protected $template;
        protected $route_seolang = false;
        protected $route;
        protected $server_protocol = 'HTTP/1.1';
        protected $module = 'seolang/seolang';

        public function __construct($registry) {
            parent::__construct($registry);
            if (version_compare(phpversion(), '5.3.0', '<') == true) {
                exit('PHP 5.3+ Required');
            }
            if (!defined('VERSION')) {
                exit('Where const VERSION?');
            }
            if (!defined('SC_VERSION')) {
                define('SC_VERSION', (int) substr(str_replace('.', '', VERSION), 0, 2));
            }
            if ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) == 'on'))) {
                $this->data['protocol'] = 'https';
                $this->data['url_link_ssl'] = true;
            } else {
                $this->data['protocol'] = 'http';
                if (SC_VERSION < 20) {
                    $this->data['url_link_ssl'] = 'NONSSL';
                } else {
                    $this->data['url_link_ssl'] = false;
                }
            }
            if (SC_VERSION > 23) {
                $this->data['template_engine'] = $this->config->get('template_engine');
            }

            if (!$this->config->get('seolang_seolang_settings')) {
                $this->data['seolang_settings'] = $this->model_seolang_seolang->getSetting('seolang_settings');
                $this->config->set('seolang_seolang_settings', $this->data['seolang_settings']);
            } else {
                $this->data['seolang_settings'] = $this->config->get('seolang_seolang_settings');
            }

            $this->data['index'] = false;
        }

        private function seolang_route() {
            if (!$this->route_seolang) {
                if (isset($this->registry->get('request')->get['route'])) {
                    $this->route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string) $this->registry->get('request')->get['route']);
                } else {
                    $this->route = 'common/home';
                }
                if (isset($this->route) && stripos($this->route, $this->module) !== false) {
                    $this->route_seolang = false;
                    if (isset($this->registry->get('request')->server['SERVER_PROTOCOL']) && $this->registry->get('request')->server['SERVER_PROTOCOL'] != '') {
                        $this->server_protocol = $this->registry->get('request')->server['SERVER_PROTOCOL'];
                    } else {
                        $this->server_protocol = 'HTTP/1.1';
                    }
                    header($this->server_protocol . ' ' . '404 Not Found');
                    header('Status: 404 Not Found');
                    exit();
                }
                $this->route_seolang = true;
            }
        }

        public function index($setting = array()) {
            $this->seolang_route();
            return $this
                ->start()
                ->load_model()
                ->load_lib()
                ->load_settings($setting)
                ->load_access()
                ->load_widget()
                ->load_view_output()
                ->load_output();
        }

        private function start() {
            if (!isset($this->data['seolang_settings']['status']) || !$this->data['seolang_settings']['status']) {
                $this->data['index'] = false;
            } else {
                $this->data['index'] = true;
            }
            if (!defined('VERSION') || !defined('SC_VERSION')) {
                $this->data['index'] = false;
            }
            return $this;
        }

        private function load_model() {
            if (!$this->data['index']) {
                return $this;
            }

            if (!is_object($this->model_seolang_seolang)) {
                $this->load->model($this->module);
            }
            return $this;
        }

        private function load_lib() {
            if (!$this->data['index']) {
                return $this;
            }

            if (!is_object($this->controller_seolang_seolanglib)) {
                $this->model_seolang_seolang->control('seolang/seolanglib');
            }
            if (!$this->registry->get('seolanglib')) {
                $this->registry->set('seolanglib', $this->controller_seolang_seolanglib);
                if (SC_VERSION < 20) {
                    $this->config->set('seolanglib', $this->controller_seolang_seolanglib);
                }
            }
            return $this;
        }

        private function load_get_store() {
            if (!$this->data['index']) {
                return $this;
            }

            $this->data['store_id'] = $this->config->get('config_store_id');
            return $this;
        }

        private function load_settings($setting) {

            if (!$this->data['index']) {
                return $this;
            }

            if (!is_array($setting)) {
                return $this;
            }

            if (!isset($this->data['store_id'])) {
                $this->load_get_store();
            }

            if (!$this->config->get('seolang_seolang_settings_' . $this->data['store_id'])) {
                $this->data['seolang_settings_store'] = $this->data['seolang_settings_' . $this->data['store_id']] = $this->model_seolang_seolang->getSetting('seolang_settings_' . $this->data['store_id'], $this->data['store_id']);
                $this->config->set('seolang_seolang_settings_' . $this->data['store_id'], $this->data['seolang_settings_' . $this->data['store_id']]);
            } else {
                $this->data['seolang_settings_store'] = $this->data['seolang_settings_' . $this->data['store_id']] = $this->config->get('seolang_seolang_settings_' . $this->data['store_id']);
            }

            if (!isset($setting['status'])) {
                if (isset($setting['name']) && $setting['name'] != '' && isset($this->data['seolang_settings_store']['multi'][$setting['name']])) {
                    $setting = array_merge($setting, $this->data['seolang_settings_store']['multi'][$setting['name']]);
                }
            }

            $this->data['setting'] = $setting;

            return $this;
        }

        private function load_access() {
            if (!$this->data['index']) {
                return $this;
            }

            if (!isset($this->data['seolang_settings_store']['access']) || !$this->data['seolang_settings_store']['access']) {
                $this->data['index'] = false;
            }
            return $this;
        }

        private function load_widget() {
            if (!$this->data['index']) {
                return $this;
            }

            if (!isset($this->data['setting']['widget']) || (!isset($this->data['seolang_settings']['widget_' . $this->data['setting']['widget'] . '_status']) || !$this->data['seolang_settings']['widget_' . $this->data['setting']['widget'] . '_status'])) {
                $this->data['index'] = false;
            } else {
                $this->data['index'] = true;

                $this->data['controller_widget'] = 'controller_seolang_' . $this->data['setting']['widget'] . '_' . $this->data['setting']['widget'];

                if (!is_object($this->{$this->data['controller_widget']})) {
                    $this->model_seolang_seolang->control('seolang/' . $this->data['setting']['widget'] . '/' . $this->data['setting']['widget']);
                }
                $this->data = $this->{$this->data['controller_widget']}->index($this->data);
            }
            return $this;
        }

        private function load_view_output() {
            if (!$this->data['index']) {
                return $this;
            }

            $this->registry->set('seolang_output', '');

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

                $this->response->setOutput($this->data['output']);
            }

            return $this;
        }

        private function load_output() {

            if (!$this->data['index']) {
                return '';
            }

            if (isset($this->data['output']) && $this->data['output'] != '') {
                return $this->data['output'];
            } else {
                return '';
            }
        }

        private function debug_backtrace_string() {
            $stack = '';
            $i = 1;
            $trace = debug_backtrace();
            unset($trace[0]); //Remove call to this function from stack trace
            foreach ($trace as $node) {
                $stack .= "#$i " . $node['file'] . "(" . $node['line'] . "): ";
                if (isset($node['class'])) {
                    $stack .= $node['class'] . "->";
                }
                $stack .= $node['function'] . "()" . PHP_EOL;
                $i++;
            }
            return $stack;
        }
    }
}
