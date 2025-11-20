<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerExtensionModuleSeoLang', false)) {
	class ControllerExtensionModuleSeoLang extends Controller {
		private $error = array();
		public function __construct($registry) {
			parent::__construct($registry);
			$this->load->model('seolang/seolang');
		}
		public function index() {
				$this->model_seolang_seolang->control('seolang/seolang');
				$this->controller_seolang_seolang->index($this->registry);
		}
		public function uninstall() {
			if ($this->validate()) {
				$this->model_seolang_seolang->control('seolang/seolang');
				$this->controller_seolang_seolang->uninstall($this->registry);
			}
		}
		public function install() {
			$this->model_seolang_seolang->control('seolang/seolang');
			$this->controller_seolang_seolang->install($this->registry);
		}
		protected function validate() {
			if (!$this->user->hasPermission('modify', 'extension/module/seolang')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
			return !$this->error;
		}
 	}
}
