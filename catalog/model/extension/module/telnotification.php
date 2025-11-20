<?php 
/**
 * 
 */
class ModelExtensionModuleTelnotification extends Model {
	
	public function getStatus(){
		return $this->config->get('module_telnotification_status');
	}

	public function getRecipients() {
		return $recipients = json_decode( $this->config->get('module_telnotification_recipients'), true);
	}
	
	public function getBotId() {
		return $this->config->get('module_telnotification_bot_id');
	}

    public function getMessage($message_id) {
		return $this->config->get('module_telnotification_'.$message_id);
	}
	
}