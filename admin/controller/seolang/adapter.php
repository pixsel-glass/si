<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerSeoLangAdapter', false)) {
    class ControllerSeoLangAdapter extends Controller {
        private $error = array();
        protected $data;
        protected $template;
        protected $children;
        protected $lm_host = '';
        protected $server;
        protected $dir_template_front;
        protected $dir_main;
        protected $directory = false;
        protected $template_ext;

        public function __construct($registry) {
            parent::__construct($registry);
            if (version_compare(phpversion(), '5.3.0', '<') == true) {
                exit('PHP5.3+ Required');
            }

            if (!defined('SC_VERSION') && defined('VERSION')) {
                define('SC_VERSION', (int) substr(str_replace('.', '', VERSION), 0, 2));
            }

            if (defined('SC_VERSION')) {
                if (!class_exists('PHP_Exceptionizer', false)) {
                    if (function_exists('modification')) {
                        require_once modification(DIR_SYSTEM . 'library/exceptionizer.php');
                    } else {
                        require_once DIR_SYSTEM . 'library/exceptionizer.php';
                    }
                }
                if (SC_VERSION > 23) {
                    $this->data['token_name'] = 'user_token';
                } else {
                    $this->data['token_name'] = 'token';
                }
                if (isset($this->session->data[$this->data['token_name']])) {
                    $this->data['token'] = $this->session->data[$this->data['token_name']];
                } else {
                    $this->data['token'] = '';
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
                if (isset($this->registry->get('request')->server['HTTP_HOST']) && $this->registry->get('request')->server['HTTP_HOST'] != '') {
                    $this->lm_host = $this->registry->get('request')->server['HTTP_HOST'];
                } else {
                    $this->lm_host = '';
                }

                if (SC_VERSION > 23) {
                    $this->data['template_engine'] = $this->config->get('template_engine');
                }

                if ($this->data['protocol'] == 'https') {
                    $config_url_0 = $this->server = HTTPS_CATALOG;
                } else {
                    $config_url_0 = $this->server = HTTP_CATALOG;
                }

                $config_content_front = file_get_contents(DIR_SYSTEM . '../config.php');

                $config_content_front_dir_template_pattern = '/define[(][\']DIR_TEMPLATE[\'][\,].*?[\'](.*?)[\'][)][;]/is';
                preg_match_all($config_content_front_dir_template_pattern, $config_content_front, $config_content_front_dir_template_matches);

                $config_content_front_dir_application_pattern = '/define[(][\']DIR_APPLICATION[\'][\,].*?[\'](.*?)[\'][)][;]/is';
                preg_match_all($config_content_front_dir_application_pattern, $config_content_front, $config_content_front_dir_application_matches);


                if (SC_VERSION > 23) {
                    $this->dir_template_front = $config_content_front_dir_application_matches[1][0] . $config_content_front_dir_template_matches[1][0];
                } else {
                    $this->dir_template_front = $config_content_front_dir_template_matches[1][0];
                }
                if (stripos($this->dir_template_front, DIR_CATALOG) === false) {
                    $this->dir_template_front =  DIR_CATALOG . 'view/theme/';
                }

                $this->dir_main = $this->data['dir_main'] = str_ireplace('system/', '', DIR_SYSTEM);


                if (SC_VERSION > 23) {
                    $this->template_ext = 'twig';
                } else {
                    $this->template_ext = 'tpl';
                }

                if ($this->config->get('config_admin_language')) {
                    $this->data['config_admin_language'] = $this->config->get('config_admin_language');
                } else {
                    $this->data['config_admin_language'] = '';
                }

                $this->data['stores'][0] = array(
                    'store_id' => 0,
                    'name' => $this->config->get('config_name'),
                    'url' => $config_url_0,
                );

                $this->load->model('setting/store');
                $stores = $this->model_setting_store->getStores();

                foreach ($stores as $result) {
                    if ($this->data['protocol'] == 'https' && isset($result['ssl']) && $result['ssl'] != '') {
                        $store_url = $result['ssl'];
                    } else {
                        $store_url = $result['url'];
                    }

                    $this->data['stores'][$result['store_id']] = array(
                        'store_id' => $result['store_id'],
                        'name' => $result['name'],
                        'url' => $store_url,
                    );
                }

                if (isset($this->request->get['directory']) && $this->request->get['directory'] != '') {
                    $this->directory = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', (string)$this->request->get['directory']);
                } else {
                    $this->directory = '';
                }


                $this->data['config_language_id'] = $this->config->get('config_language_id');
                $this->data['thisis'] = $this;

                $this->data['html'] = '';
            }
        }

        public function index() {
            if (defined('SC_VERSION')) {
                $this
                    ->load_model()
                    ->load_start()
                    ->load_get_store()
                    ->load_url_link()
                    ->getDirectories()
                    ->getThemes()
                    ->load_languages()
                    ->load_language()
                    ->load_language_get()
                    ->save_settings()
                    ->load_settings()
                    ->load_scripts()
                    ->load_setTitle()
                    ->load_set()
                    ->load_messages()
                    ->load_form()
                    ->load_view_settings()
                    ->load_view()
                    ->load_view_output();
            }
        }


        private function load_ajax_form_language() {
            $content['success'] = true;
            $theme_file = 'langmark.' . $this->template_ext;
            if (file_exists($this->data['file_language_path_full'])) {
                $content['text'] = html_entity_decode(file_get_contents($this->data['file_language_path_full']), ENT_QUOTES, 'UTF-8');
                $content['file'] = str_ireplace($this->dir_main, '', '/' . $this->data['file_language_path_full']);
                $content['filename'] = $theme_file;
            } else {
                $content['file'] = str_ireplace($this->dir_main, '', '/' . $this->data['file_language_path_full']);
                $content['filename'] = $theme_file;
                $content['success'] = false;
                $content['error'] =  $this->language->get('text_adapter_error_access_not_find_file') . $content['file'] ;
            }
            return $content;
        }

        private function load_ajax_form_theme() {
            $content['success'] = true;

            if (isset($this->data['file_theme_path_full']) && file_exists($this->data['file_theme_path_full'])) {
                $file_theme_path_full = $this->data['file_theme_path_full'];
            } else {
                $file_theme_path_full = $this->data['file_theme_default_path_full'];
            }


            if (isset($this->data['folder_theme_path_full']) && file_exists($this->data['folder_theme_path_full'])) {
                $folder_theme_path_full = $this->data['folder_theme_path_full'];
            } else {
                $folder_theme_path_full = $this->data['folder_theme_default_path_full'];
            }


            if (isset($this->request->post['theme_file']) && $this->request->post['theme_file'] != '') {
                $theme_file = preg_replace('/[^a-zA-Z0-9_\-\.\/]/', '', (string)$this->request->post['theme_file']);
            } else {
                $theme_file = 'langmark.' . $this->template_ext;
            }

            $file_theme_path_full = $folder_theme_path_full . $theme_file;


            if (file_exists($file_theme_path_full)) {

                $content['text'] = html_entity_decode(file_get_contents($file_theme_path_full), ENT_QUOTES, 'UTF-8');
                $content['file'] = str_ireplace($this->dir_main, '', '/' . $file_theme_path_full);
                $content['filename'] = $theme_file;
            } else {
                
                $content['file'] = str_ireplace($this->dir_main, '', '/' . $file_theme_path_full);
                $content['filename'] = $theme_file;
                $content['success'] = false;
                $content['error'] =  $this->language->get('text_adapter_error_access_not_find_file') . $content['file'] ;
            }


            return $content;
        }

        private function load_ajax_form_themes() {
            $content['success'] = true;

            if (isset($this->data['folder_themes_path_full']) && file_exists($this->data['folder_themes_path_full'])) {
                $folder_themes_path_full = $this->data['folder_themes_path_full'];
            } else {
                $folder_themes_path_full = $this->data['folder_themes_default_path_full'];
            }

            if (isset($this->request->post['themes_file']) && $this->request->post['themes_file'] != '') {
                $themes_file = preg_replace('/[^a-zA-Z0-9_\-\.\/]/', '', (string)$this->request->post['themes_file']);
            } else {
                $themes_file = 'langmark.' . $this->template_ext;
            }

            $file_themes_path_full = $folder_themes_path_full . $themes_file;

            if (file_exists($file_themes_path_full)) {

                $content['text'] = html_entity_decode(file_get_contents($file_themes_path_full), ENT_QUOTES, 'UTF-8');
                $content['file'] = str_ireplace($this->dir_main, '', '/' . $file_themes_path_full);
                $content['filename'] = $themes_file;
            } else {
                $content['text'] = '';
                $content['file'] = str_ireplace($this->dir_main, '', '/' . $file_themes_path_full);
                $content['filename'] = $themes_file;
                $content['success'] = false;
                $content['error'] =  $this->language->get('text_adapter_error_access_not_find_file') . $content['file'] ;
            }
            return $content;
        }



        private function get_theme_folder() {

            foreach ($this->data['directories'] as $directory => $directory_array) {
                if ($directory == $this->directory) {
                    $current_theme = $directory_array;
                }
                if ($directory == 'default') {
                    $default_theme = $directory_array;
                }
            }

            if (isset($this->request->get['type']) && $this->request->get['type'] == 'theme') {
                if (isset($current_theme['folder_theme_path_full']) && is_dir($current_theme['folder_theme_path_full'])) {
                    return $current_theme['folder_theme_path_full'];
                } else {
                    return $default_theme['folder_theme_path_full'];
                }
            }


            if (isset($this->request->get['type']) && $this->request->get['type'] == 'themes') {
                if (isset($current_theme['folder_themes_path_full']) && is_dir($current_theme['folder_themes_path_full'])) {
                    return $current_theme['folder_themes_path_full'];
                } else {
                    return $default_theme['folder_themes_path_full'];
                }
            }
        }


        private function get_template_files($theme_folder, $filter_name) {
            $files_result = array();
            $template_info  = pathinfo($filter_name);

            if (isset($template_info['extension']) && $template_info['extension'] != '') {
                $filter_name = $template_info['filename'];
            }
            if (SC_VERSION > 23) {
                $ext = '{twig,TWIG}';
            } else {
                $ext = '{tpl,TPL}';
            }

            $directory = $theme_folder;


            $files = glob($directory . $filter_name . '*.' . $ext, GLOB_BRACE);

            if (!$files) {
                $files = array();
            }

            $theme_folder = str_ireplace($this->dir_main, '', $theme_folder);
            foreach ($files as $result) {
                $files_result[$result] = $theme_folder;
            }

            return $files_result;
        }

        public function autotemplate() {
            $this
                ->load_model()
                ->load_start()
                ->load_get_store()
                ->load_url_link()
                ->getDirectories()
                ->getThemes()
                ->load_languages()
                ->load_language()
                ->load_language_get()

                ->load_form();

            $json = array();
            $files_default = $files_theme = $files_result =  array();

            if (isset($this->request->get['filter_name'])) {

                $filter_name = htmlspecialchars(strip_tags(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8')), ENT_COMPAT, 'UTF-8');

                $theme_folder = $this->get_theme_folder();

                $files_theme = $this->get_template_files($theme_folder, $filter_name);

                if (!$files_theme) {
                    $files_theme = array();
                }

                if ($theme_folder != 'default') {
                    $theme_folder = 'default';
                    $files_default = $this->get_template_files($theme_folder, $filter_name);
                }
                if (!$files_default) {
                    $files_default = array();
                }
                $files = array_merge($files_default, $files_theme);

                foreach ($files as $result => $theme) {
                    $template_info  = pathinfo($result);
                    $name = $template_info['filename'] . '.' . $template_info['extension'];
                    $files_result[$name] = $theme;
                }


                foreach ($files_result as $result => $theme) {
                    $template_info  = pathinfo($result);
                    $name = $template_info['filename'] . '.' . $template_info['extension'];
                    if (SC_VERSION > 15) {
                        $three_nbsp = '&nbsp;&nbsp;&nbsp;';
                    } else {
                        $three_nbsp = '';
                    }
                    $json[] = array(
                        'label' => $name . $three_nbsp . ' > ' . $three_nbsp . $theme,
                        'name' => $name
                    );
                }
            }

            $sort_order = array();

            foreach ($json as $key => $value) {
                $sort_order[$key] = $value['name'];
            }

            array_multisort($sort_order, SORT_ASC, $json);

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        }



        public function load_ajax_form() {
            $this
                ->load_model()
                ->load_start()
                ->load_get_store()
                ->load_url_link()
                ->getDirectories()
                ->getThemes()
                ->load_languages()
                ->load_language()
                ->load_language_get()
                ->load_form();

            $content['success'] = false;

            if ($this->validate()) {

                if (isset($this->request->get['type']) && $this->request->get['type'] == 'language') {
                    $content = $this->load_ajax_form_language();
                }
                if (isset($this->request->get['type']) && $this->request->get['type'] == 'theme') {
                    $content = $this->load_ajax_form_theme();
                }
                if (isset($this->request->get['type']) && $this->request->get['type'] == 'themes') {
                    $content = $this->load_ajax_form_themes();
                }
            } else {
                $content['error'] = $this->language->get('text_adapter_error_access');
                $content['success'] = false;
                $content['text'] = '';
                $content['file'] = '';
                $content['error'] =  $this->language->get('text_adapter_error_access') ;
            }

            if (is_array($content)) {
                $this->response->setOutput(json_encode($content));
            } else {
                $this->response->setOutput($content);
            }
        }



        private function load_form() {
            if (isset($this->directory) && $this->directory != '') {


                foreach ($this->data['directories'] as $directory => $directory_array) {
                    if ($directory == $this->directory) {
                        $current_theme = $directory_array;
                    }
                    if ($directory == 'default') {
                        $default_theme = $directory_array;
                    }
                }

                if (isset($current_theme['file_language_path_full']) && $current_theme['file_language_path_full'] != '' && file_exists($current_theme['file_language_path_full'])) {
                    $this->data['file_language_path_full'] = $current_theme['file_language_path_full'];
                } else {
                    if (isset($default_theme['file_language_path_full']) && $default_theme['file_language_path_full'] != '' && file_exists($default_theme['file_language_path_full'])) {
                        $this->data['file_language_path_full'] = $default_theme['file_language_path_full'];
                    } else {
                        $this->data['file_language_path_full'] = '';
                    }
                }


                if (isset($current_theme['folder_theme_path_full'])) {

                    $this->data['folder_theme_path_full'] = $current_theme['folder_theme_path_full'];
                    $this->data['folder_theme'] = $current_theme['folder_theme_path_full'];
                    $this->data['form_folder'] = str_ireplace($this->dir_main, '', '/' . $current_theme['folder_theme_path_full']);
                } else {
                    $this->data['folder_theme_path_full'] = $default_theme['folder_theme_path_full'];
                    $this->data['folder_theme'] = $default_theme['folder_theme_path_full'];
                    $this->data['form_folder'] = str_ireplace($this->dir_main, '', '/' . $default_theme['folder_theme_path_full']);                    
                }





                if (isset($current_theme['folder_theme_path_full']) && isset($current_theme['files_theme_count']) && $current_theme['files_theme_count'] > 0) {

                    $this->data['folder_theme_path_full'] = $current_theme['folder_theme_path_full'];
                } else {
                    if (isset($default_theme['folder_theme_path_full']) && isset($default_theme['files_theme_count'])  && $default_theme['files_theme_count'] > 0) {

                        $this->data['folder_theme_default_path_full'] = $default_theme['folder_theme_path_full'];
                    }
                }



                if (isset($current_theme['folder_themes_path_full']) && $current_theme['files_themes_count'] > 0) {
                    $this->data['folder_themes_path_full'] = $current_theme['folder_themes_path_full'];
                } else {
                    if (isset($default_theme['folder_themes_path_full']) && $default_theme['files_themes_count'] > 0) {
                        $this->data['folder_themes_default_path_full'] = $default_theme['folder_themes_path_full'];
                    }
                }


                if (isset($current_theme['file_theme_path_full']) && $current_theme['file_theme_path_full'] != '' && file_exists($current_theme['file_theme_path_full'])) {
                    $this->data['form_content'] = html_entity_decode(file_get_contents($current_theme['file_theme_path_full']), ENT_QUOTES, 'UTF-8');
                    $this->data['file_theme_path_full'] = $current_theme['file_theme_path_full'];


                    $this->data['form_file'] = str_ireplace($this->dir_main, '', '/' . $this->data['file_theme_path_full']);
                } else {
                    if (file_exists($this->data['file_language_path_full'])) {

                        $this->data['form_file'] = str_ireplace($this->dir_main, '', '/' . $this->data['file_language_path_full']);


                        $this->data['form_content'] = html_entity_decode(file_get_contents($this->data['file_language_path_full']), ENT_QUOTES, 'UTF-8');
                    } else {
                        $this->data['form_content'] = '';
                        $this->data['form_file'] = '';
                    }
                    if (isset($default_theme['file_theme_path_full']) && file_exists($default_theme['file_theme_path_full'])) {
                        $this->data['file_theme_default_path_full'] = $default_theme['file_theme_path_full'];
                    }
                }
                if (SC_VERSION > 23) {
                    $this->data['file_theme'] = 'langmark.twig';
                } else {
                    $this->data['file_theme'] = 'langmark.tpl';
                }
            }
            return $this;
        }

        private function getDirectories() {
            $this->data['directories'] = array();
            $directories = glob($this->dir_template_front . '*', GLOB_ONLYDIR);
            foreach ($directories as $directory) {
                $this->data['directories'][basename($directory)] = basename($directory);
            }
            return $this;
        }

        private function getThemes() {
            $directories = array();
            $this->data['config_template'] = $this->config->get('config_template');
            
            if (!defined('SC_VERSION3') && defined('VERSION')) {
                define('SC_VERSION3', (int) substr(str_replace('.', '', VERSION), 0, 3));
            }            

            if (SC_VERSION > 21) {
                $this->data['config_theme'] = $this->config->get('config_theme');
            } else {
                $this->data['config_theme'] = $this->config->get('config_template');
            }

            if (SC_VERSION3 > 300) {
                $this->load->language('extension/theme/' . 'default', 'directory');
                $name_default = $this->language->get('directory')->get('heading_title');
            } else {
                if (SC_VERSION > 15) {
                    $this->load->language('extension/theme/theme_' . 'default');
                    $this->load->language('extension/theme/' . 'default');
                }
                $name_default = $this->language->get('heading_title');
            }


            if (SC_VERSION > 15) {
                $language_file = 'common/language';
            } else {
                $language_file = 'module/language';
            }

            foreach ($this->data['directories'] as $num => $directory) {
                $name = '';

                $template_theme_folder = $this->dir_template_front . $directory . '/template/agootemplates/record/';
                $template_theme_file = $template_theme_folder . 'langmark.' . $this->template_ext;

                $files_theme = glob($template_theme_folder . '*.' . $this->template_ext);
                if ($files_theme) {
                    $directories[$directory]['files_theme_count'] = count($files_theme);
                }
                $directories[$directory]['folder_theme_path_full'] = $template_theme_folder;
                if (file_exists($template_theme_file)) {
                    $directories[$directory]['file'] = str_ireplace($this->dir_main, '', $template_theme_file);
                    $directories[$directory]['file_theme_path_full'] = $template_theme_file;
                    $directories[$directory]['folder_theme_path_full'] = $template_theme_folder;
                }

                $template_language_folder = $this->dir_template_front . $directory . '/template/';

                $theme_language_template = $template_language_folder . $language_file . '.' . $this->template_ext;
                if (!file_exists($theme_language_template)) {
                    $template_language_folder = $this->dir_template_front .  'default/template/';
                    $theme_language_template = $template_language_folder . $language_file . '.' . $this->template_ext;
                }
                $directories[$directory]['file_language_path_full'] = $theme_language_template;
                //$directories[$directory]['folder_language_path_full'] = $template_language_folder;

                $folder_themes_path_full = DIR_TEMPLATE . 'seolang/themes/' . $directory . '/';

                $files_themes = glob($folder_themes_path_full . '*.' . $this->template_ext);
                if ($files_themes) {
                    $directories[$directory]['folder_themes_path_full'] = $folder_themes_path_full;
                    $directories[$directory]['files_themes_count'] = count($files_themes);
                }


                $directories[$directory]['directory'] = $directory;
                $directories[$directory]['action'] = $this->data['url_action'] . '&directory=' . $directory;
                $directories[$directory]['name'] = $directory;

                if ($this->data['config_theme'] == $directory || $this->data['config_theme'] == 'theme_' . $directory) {
                    $directories[$directory]['status'] = true;
                }

                if ($this->config->get('theme_' . $directory . '_directory')) {
                    $directories[$directory]['current'] = $this->config->get('theme_' . $directory . '_directory');
                }

                if (SC_VERSION3 > 300) {
                    $this->load->language('extension/theme/' . $directory, 'directory');
                    $name = $this->language->get('directory')->get('heading_title');
                } else {
                    if (SC_VERSION > 22) {
                        $this->language->set('heading_title', '');
                    }
                    if (SC_VERSION > 15) {
                        $this->load->language('extension/theme/theme_' . $directory);
                        $this->load->language('extension/theme/' . $directory);
                    }
                    $name = $this->language->get('heading_title');
                }

                if ($name != 'heading_title' && $name != '') {
                    if (SC_VERSION > 22) {
                        $directories[$directory]['name'] = $name;
                    }
                } else {
                    if ($name_default == 'heading_title' || $name_default == '') {
                        $directories[$directory]['name'] = $directory;
                    } else {
                        $directories[$directory]['name'] = $name_default;
                    }
                }

                if (SC_VERSION > 21) {
                    if (is_file($this->dir_template_front . $directory . '/image/' . $directory . '.png')) {
                        $directories[$directory]['image'] = $this->server . 'catalog/view/theme/' . $directory . '/image/' . $directory . '.png';
                    }
                }
                if (SC_VERSION < 22) {
                    if (is_file(DIR_IMAGE . 'templates/' . $directory . '.png')) {
                        $directories[$directory]['image'] = $this->server . 'image/templates/' . $directory . '.png';
                    }
                }
            }

            foreach ($this->data['directories'] as $num => $dir) {

                foreach ($directories as $number => $directory) {

                    if (isset($directory['current']) && $dir = $directory['current']) {

                        $directories[$dir]['current'] = $dir;

                        if (isset($directory['name'])) {
                            $directories[$dir]['name'] = $directory['name'];
                        }
                        if (isset($directory['status'])) {
                            $directories[$dir]['status'] = $directory['status'];
                        }
                        if ($directory['current'] != $directory['directory']) {
                            if (isset($directories[$directory['directory']]['status'])) {
                                unset($directories[$directory['directory']]['status']);
                            }
                        }
                    }
                }
            }

            $this->data['directories'] = $directories;
            $this->data['current_directory'] = '';
            $this->data['current_name'] = '';
            foreach ($this->data['directories'] as $directory => $directory_array) {
                if ($directory == $this->directory) {
                    $this->data['current_directory'] = $directory_array['directory'];
                    $this->data['current_name'] = $directory_array['name'];
                }
            }



            return $this;
        }


        public function load_model() {
            $this->load->model('setting/setting');
            $this->load->model('localisation/language');
            $this->load->model('tool/image');
            $this->load->model('seolang/seolang');

            return $this;
        }


        public function load_start() {
            if (isset($this->request->get['lm_save']) && $this->request->get['lm_save'] == 1) {
                $this->data['lm_save'] = true;
            } else {
                $this->data['lm_save'] = false;
            }
            if (isset($this->request->get['lm_restore']) && $this->request->get['lm_restore'] == 1) {
                $this->data['lm_restore'] = true;
            } else {
                $this->data['lm_restore'] = false;
            }

            return $this;
        }



        public function load_language() {
            $this->language->load('seolang/seolang');
            $this->language->load('seolang/langmark');
            $this->language->load('seolang/adapter');

            $this->data['language'] = $this->language;

            return $this;
        }

        public function load_language_get() {
            $this->data['seolang_version'] = $this->language->get('seolang_version');
            $this->data['heading_title'] = $this->language->get('heading_title');
            $this->data['heading_title_seolang'] = $this->language->get('heading_title_seolang');
            $this->data['heading_dev'] = $this->language->get('heading_dev');
            $this->data['ico_seolang'] = $this->language->get('ico_seolang');

            $this->data['seolang_text_seolang_success'] = $this->language->get('text_seolang_success');

            $this->data['entry_adapter_column_directory'] = $this->language->get('entry_adapter_column_directory');
            $this->data['entry_adapter_column_name'] = $this->language->get('entry_adapter_column_name');
            $this->data['entry_adapter_column_image'] = $this->language->get('entry_adapter_column_image');
            $this->data['entry_adapter_column_action'] = $this->language->get('entry_adapter_column_action');
            $this->data['entry_adapter_column_file'] = $this->language->get('entry_adapter_column_file');
            $this->data['entry_adapter_column_files_themes_count'] = $this->language->get('entry_adapter_column_files_themes_count');
            $this->data['entry_adapter_load_ajax_themes'] = $this->language->get('entry_adapter_load_ajax_themes');

            return $this;
        }


        public function load_setTitle() {

            $this->document->setTitle(strip_tags($this->data['heading_title']));

            return $this;
        }

        public function save_settings() {

            if ($this->request->server['REQUEST_METHOD'] == 'POST' && !$this->validate()) {
                $this->request->post = array();
                $this->data['error_warning'] = $this->session->data['error_warning'] =  $this->language->get('error_text_seolang_modify');
                return $this;
            }

            if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
                if (isset($this->directory) && $this->directory != '' && isset($this->request->get['save_form']) && $this->request->get['save_form'] == 1) {


                    $path = $this->request->post['folder_theme'] . $this->request->post['file_theme'];

                    $form_content = html_entity_decode($this->request->post['form_content'], ENT_QUOTES, 'UTF-8');

                    if ($this->mkdirs($path)) {
                        file_put_contents($path, $form_content);

                        if (file_exists($path)) {
                            $content_new = file_get_contents($path);
                            if ($content_new == $form_content) {
                                $this->session->data['success'] = $this->data['success'] = $this->language->get('succes_text_adapter_adapt');
                            } else {
                                $this->session->data['error_warning'] = $this->data['error_warning'] = $this->language->get('error_text_adapter_adapt');
                            }
                        } else {
                            $this->session->data['error_warning'] = $this->data['error_warning'] = $this->language->get('error_text_adapter_adapt');    
                        }
                    } else {
                        $this->data['error_warning'] = $this->session->data['error_warning'] =  $this->language->get('error_text_adapter_adapt');
                    }
                }
            }

            return $this;
        }

        public function load_settings() {

            if (isset($this->request->get['seolang_save'])) {
                $this->data['seolang_save'] = true;
            }

            if (isset($this->data['lm_restore']) && $this->data['lm_restore']) {
                $this->session->data['success'] = $this->data['text_lm_restore_success'];
            }

            return $this;
        }

        private function load_scripts_css() {
            if (file_exists(DIR_APPLICATION . 'view/stylesheet/seolang/seolang.css')) {
                $this->document->addStyle('view/stylesheet/seolang/seolang.css?v=' . $this->data['seolang_version']);
            }
            if (file_exists(DIR_APPLICATION . 'view/stylesheet/seolang/icons.css')) {
                $this->document->addStyle('view/stylesheet/seolang/icons.css?v=' . $this->data['seolang_version']);
            }
            if (file_exists(DIR_APPLICATION . 'view/javascript/seolang/magnific/magnific-popup.css')) {
                $this->document->addStyle('view/javascript/seolang/magnific/magnific-popup.css?v=' . $this->data['seolang_version']);
            }
        }
        
        public function load_scripts() {

            $this->load_scripts_css();

            if (file_exists(DIR_APPLICATION . 'view/javascript/jquery/tabs.js')) {
                $this->document->addScript('view/javascript/jquery/tabs.js');
            } else {
                if (file_exists(DIR_APPLICATION . 'view/javascript/seolang/tabs.js')) {
                    $this->document->addScript('view/javascript/seolang/tabs.js?v=' . $this->data['seolang_version']);
                }
            }
            if (SC_VERSION < 20) {
                $this->document->addStyle('view/javascript/seolang/bootstrap/css/bootstrap.css');
                $this->document->addStyle('view/javascript/seolang/font-awesome/css/font-awesome.css?v=' . $this->data['seolang_version']);
            }

            if (file_exists(DIR_APPLICATION . 'view/javascript/seolang/magnific/jquery.magnific-popup.min.js')) {
                $this->document->addScript('view/javascript/seolang/magnific/jquery.magnific-popup.min.js?v=' . $this->data['seolang_version']);
            }

            return $this;
        }

        public function load_get_store() {

            $this->load->model('setting/store');

            if (isset($this->request->get['store_id'])) {
                $this->data['store_id'] = (int) $this->request->get['store_id'];
            } else {
                if (isset($this->request->post['store_id'])) {
                    $this->data['store_id'] = (int) $this->request->post['store_id'];
                } else {
                    $this->data['store_id'] = 0;
                }
            }

            $flag_store = false;
            foreach ($this->data['stores'] as $store) {
                if ($store['store_id'] == $this->data['store_id']) {
                    $flag_store = true;
                }
            }
            if (!$flag_store) {
                $this->data['store_id'] = 0;
            }

            return $this;
        }

        public function load_url_link() {

           

            $this->data['url_action'] = str_replace('&amp;', '&', $this->url->link('seolang/adapter', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->data['url_link_ssl']));
            $this->data['url_seolang'] = str_replace('&amp;', '&', $this->url->link('seolang/adapter', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->data['url_link_ssl']));
            $this->data['url_seolang_seolang_adapter'] = str_replace('&amp;', '&', $this->url->link('seolang/adapter', $this->data['token_name'] . '=' . $this->data['token'], $this->data['url_link_ssl']));

            $this->data['url_ajax_language'] = str_replace('&amp;', '&', $this->url->link('seolang/adapter/load_ajax_form', $this->data['token_name'] . '=' . $this->data['token'] . '&directory=' . $this->directory . '&type=language', $this->data['url_link_ssl']));
            $this->data['url_ajax_theme'] = str_replace('&amp;', '&', $this->url->link('seolang/adapter/load_ajax_form', $this->data['token_name'] . '=' . $this->data['token'] . '&directory=' . $this->directory . '&type=theme', $this->data['url_link_ssl']));
            $this->data['url_ajax_themes'] = str_replace('&amp;', '&', $this->url->link('seolang/adapter/load_ajax_form', $this->data['token_name'] . '=' . $this->data['token'] . '&directory=' . $this->directory . '&type=themes', $this->data['url_link_ssl']));

            $this->data['url_ajax_themes_autotemplate'] = str_replace('&amp;', '&', $this->url->link('seolang/adapter/autotemplate', $this->data['token_name'] . '=' . $this->data['token'] . '&directory=' . $this->directory . '&type=themes', $this->data['url_link_ssl']));
            $this->data['url_ajax_theme_autotemplate'] = str_replace('&amp;', '&', $this->url->link('seolang/adapter/autotemplate', $this->data['token_name'] . '=' . $this->data['token'] . '&directory=' . $this->directory . '&type=theme', $this->data['url_link_ssl']));

            $this->data['url_seolang_seolang_options'] = str_replace('&amp;', '&', $this->url->link('seolang/seolang', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->data['url_link_ssl']));
            $this->data['url_seolang_langmark_options'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->data['url_link_ssl']));

            if (isset($this->directory) && $this->directory != '') {
                $this->data['url_action_form'] = $this->data['url_action'] . '&directory=' . $this->directory . '&save_form=1';
            } else {
                $this->data['url_action_form'] = '';
            }

            return $this;
        }

        public function load_languages() {

            $this->data['languages'] = $this->model_localisation_language->getLanguages();

            foreach ($this->data['languages'] as $code => $language) {
                $this->data['languages'][$code]['code_short'] = substr($code, 0, 2);

                if (!isset($language['image']) || SC_VERSION > 21) {
                    $this->data['languages'][$code]['image'] = 'language/' . $code . '/' . $code . '.png';
                    if (!file_exists(DIR_APPLICATION . $this->data['languages'][$code]['image'])) {
                        $this->data['languages'][$code]['image'] = 'view/image/flags/' . $language['image'];
                    }
                } else {
                    $this->data['languages'][$code]['image'] = 'view/image/flags/' . $language['image'];
                    if (!file_exists(DIR_APPLICATION . $this->data['languages'][$code]['image'])) {
                        $this->data['languages'][$code]['image'] = 'language/' . $code . '/' . $code . '.png';
                    }
                }

                if (!file_exists(DIR_APPLICATION . $this->data['languages'][$code]['image'])) {
                    $this->data['languages'][$code]['image'] = '';
                }
            }

            $this->data['config_language_id'] = $this->config->get('config_language_id');

            return $this;
        }



        public function load_messages() {

            if ((int)substr(str_replace('.', '', VERSION), 0, 3) > 300) {
                unset($this->language->data['error_warning']);
             }
            if (isset($this->error['warning'])) {
                $this->data['error_warning'] = $this->error['warning'];
            } else {
                $this->data['error_warning'] = '';
            }
            if (isset($this->session->data['success'])) {
                $this->data['success'] = $this->session->data['success'];
                unset($this->session->data['success']);
            } else {
                $this->data['success'] = '';
            }

            if (isset($this->session->data['error_warning'])) {
                $this->data['error_warning'] = $this->session->data['error_warning'];
                unset($this->session->data['error_warning']);
            } else {
                unset($this->session->data['error_warning']);
                unset($this->data['error_warning']);
            }

            return $this;
        }

        public function load_settings_access() {

            if (!isset($this->data['seolang_settings_store']['access'])) {
                $this->data['seolang_settings_store']['access'] = true;
            }
            return $this;
        }

        public function switchLanguage($language_id, $language_code, $this_data) {
            $this->data = $this_data;

            $this->config->set('config_admin_language', $language_code);

            if (SC_VERSION > 21) {
                $language_construct = $language_code;
            } else {
                $language_construct = $this->data['languages'][$language_code]['directory'];
            }

            $language = new Language($language_construct);

            if (SC_VERSION > 15) {
                if (SC_VERSION > 21) {
                    $language->load($language_code);
                } else {
                    $language->load('default');
                    $language->load($language_construct);
                }
            } else {
                $language->load($this->data['languages'][$language_code]['filename']);
            }

            $this->registry->set('language', $language);

            $this->load_language($this->data);
        }


        public function load_set() {

            if (!isset($this->data['seolang_settings']['status'])) {
                $this->data['seolang_settings']['status'] = false;
            }

            return $this;
        }

        public function load_view_settings() {

            if (SC_VERSION < 20) {
                $this->data['column_left'] = '';
            } else {
                if (SC_VERSION > 23) {
                    $this->config->set('template_engine', $this->data['template_engine']);
                }

                $this->data['header'] = $this->load->controller('common/header');
                $this->data['footer'] = $this->load->controller('common/footer');
                $this->data['column_left'] = $this->load->controller('common/column_left');
            }
            if (isset($this->directory) && $this->directory != '') {
                $this->data['template'] = 'seolang/adapter_form';
            } else {
                $this->data['template'] = 'seolang/adapter_list';
            }

            return $this;
        }

        public function load_view() {

            if ((int)substr(str_replace('.', '', VERSION), 0, 3) > 300) {
                unset($this->language->data['error_warning']);
             }

            if (file_exists(DIR_TEMPLATE . $this->data['template'] . '.tpl')) {
                $flag_load_view = true;
            } else {
                $flag_load_view = false;
            }
            if ($flag_load_view) {

                if (SC_VERSION < 30) {
                    $this->data['template'] = $this->data['template'] . '.tpl';
                }

                if (SC_VERSION < 20) {
                    $this->children = array();
                    $this->template = $this->data['template'];
                    $this->data['html'] = $this->render();
                } else {

                    if (SC_VERSION > 23) {
                        $this->config->set('template_engine', 'template');
                    }

                    $this->data['html'] = $this->load->view($this->data['template'], $this->data);

                    if (SC_VERSION > 23) {
                        $this->config->set('template_engine', $this->data['template_engine']);
                    }
                }
            } else {
                $this->data['html'] = '';
            }

            return $this;
        }

        public function load_view_output() {

            if (SC_VERSION > 15) {
                $this->response->setOutput($this->data['html']);
            } else {
                $this->children = array(
                    'common/header',
                    'common/footer',
                );
                $this->template = $this->data['template'];
                $this->data['html'] = $this->render();

                echo $this->data['html'];
            }

            return $this;
        }

        public function validate() {

            if (is_callable(array($this->user, 'hasPermission'))) {
                if (!$this->user->hasPermission('modify', 'seolang/adapter')) {
                    $this->error['warning'] = $this->language->get('error_text_seolang_modify');
                } else {
                }
            } else {
                $this->error['warning'] = $this->language->get('error_text_seolang_modify');
            }
            if (!$this->error) {
                return true;
            } else {
                $this->request->post = array();
                $html = $this->language->get('error_text_seolang_modify');
                $this->response->setOutput($html);
                return false;
            }
        }

        private function mkdirs($pathname, $mode = 0755, $index = FALSE) {
            $flag_save = false;
            $path_file = dirname($pathname);
            $name_file = basename($pathname);
            if (is_dir(dirname($path_file))) {
            } else {
                $this->mkdirs(dirname($pathname), $mode, $index);
            }
            if (is_dir($path_file)) {
                if (file_exists($path_file)) {
                    $flag_save = true;
                }
            } else {
                umask(0);
                @mkdir($path_file, $mode);
                if (file_exists($path_file)) {
                    $flag_save = true;
                }
                if ($index) {
                    $accessFile = $path_file . "/" . $name_file;
                    touch($accessFile);
                    $accessWrite = fopen($accessFile, "wb");
                    fwrite($accessWrite, 'access denied');
                    fclose($accessWrite);
                    if (file_exists($accessFile)) {
                        $flag_save = true;
                    } else {
                        $flag_save = false;
                    }
                }
            }
            return $flag_save;
        }

        /***************************************/
    }
}

if (!function_exists('printmy')) {
    function printmy($data) {
        print_r('<pre>');
        print_r($data);
        print_r('</pre>');
    }
}
