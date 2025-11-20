<?php 
class ModelExtensionModuleTelnotification extends Model {
	public function setSettings($data) {
		$this->load->model('setting/setting');
		
		if (isset($data['module_telnotification_recipients'])) {
			$data['module_telnotification_recipients'] = json_encode($data['module_telnotification_recipients']);
		}

		$this->model_setting_setting->editSetting('module_telnotification', $data);
	}
}