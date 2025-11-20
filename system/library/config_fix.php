<?php
class ConfigFix extends Config {
    public function get($key) {
        if ($key === 'config_language_id' && isset($this->session->data['language'])) {
            $this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();
            $code = $this->session->data['language'];
            
            if (isset($languages[$code])) {
                return $languages[$code]['language_id'];
            }
        }
        return parent::get($key);
    }
}