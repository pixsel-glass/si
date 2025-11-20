<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerExtensionModuleSeoLang', false)) {
	class ControllerExtensionModuleSeoLang extends Controller {
		public function index($setting) {
				if (!is_object($this->model_seolang_seolang)) {
					$this->load->model('seolang/seolang');
				}
				if (!is_object($this->controller_seolang_seolang)) {
			    	$this->model_seolang_seolang->control('seolang/seolang');
			    }
				$html = $this->controller_seolang_seolang->index($setting);
				return $html;
		}
	}
}
