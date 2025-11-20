<?php
class ControllerExtensionModulePixselPrice extends Controller
{
  public function index()
  {

    $this->load->language('extension/module/pixsel_parser');
    $this->load->model('setting/setting');

    $this->document->setTitle($this->language->get('heading_title_price'));

    // Breadcrumbs
    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title_price'),
      'href' => $this->url->link('extension/module/pixsel_price', 'user_token=' . $this->session->data['user_token'], true)
    );

    // Button actions
    $data['action'] = $this->url->link('extension/module/pixsel_price/save', 'user_token=' . $this->session->data['user_token'], true);
    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

    $this->load->model('catalog/product');
    $this->load->model('customer/customer_group');
    $this->load->model('extension/module/pixsel_parser');

    $data['prices'] = $this->model_extension_module_pixsel_parser->getPixselPrices();
    $data['pixsel_price_types'] = $this->model_extension_module_pixsel_parser->getDistinctPixelPriceTypes();
    $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

    $data['pixsel_box_price'] = $this->model_extension_module_pixsel_parser->getPixselBoxPrices();
    $data['pixsel_box'] = $this->model_extension_module_pixsel_parser->getDistinctPixelBox();


    $data['option_id'] = $this->config->get('module_pixsel_parser_option') ?: '';

    if ($data['option_id']) {
      $this->load->model('catalog/option');

      $data['pixsel_material_price'] = $this->model_extension_module_pixsel_parser->getPixelMaterialPrices();

      $data['option_values'] = $this->model_catalog_option->getOptionValues($data['option_id']);
    }

    $this->load->model('localisation/language');

    $data['languages'] = $this->model_localisation_language->getLanguages();


    if (isset($this->request->post['module_pixsel_price_desc'])) {
      $data['module_pixsel_price_desc'] = $this->request->post['module_pixsel_price_desc'];
    } else {
      $data['module_pixsel_price_desc'] = $this->config->get('module_pixsel_price_desc');
    }

    if (isset($this->request->post['module_pixsel_price_is_desc'])) {
      $data['module_pixsel_price_is_desc'] = $this->request->post['module_pixsel_price_is_desc'];
    } else {
      $data['module_pixsel_price_is_desc'] = $this->config->get('module_pixsel_price_is_desc');
    }


    if (isset($this->request->post['module_pixsel_price_rate'])) {
      $data['module_pixsel_price_rate'] = $this->request->post['module_pixsel_price_rate'];
    } else {
      $data['module_pixsel_price_rate'] = $this->config->get('module_pixsel_price_rate');
    }

    if (isset($this->request->post['module_pixsel_price_tax_on'])) {
      $data['module_pixsel_price_tax_on'] = $this->request->post['module_pixsel_price_tax_on'];
    } else {
      $data['module_pixsel_price_tax_on'] = $this->config->get('module_pixsel_price_tax_on');
    }

    if (isset($this->request->post['module_pixsel_price_tax_rate'])) {
      $data['module_pixsel_price_tax_rate'] = $this->request->post['module_pixsel_price_tax_rate'];
    } else {
      $data['module_pixsel_price_tax_rate'] = $this->config->get('module_pixsel_price_tax_rate');
    }

    if (isset($this->request->post['module_pixsel_price_tax_znak'])) {
      $data['module_pixsel_price_tax_znak'] = $this->request->post['module_pixsel_price_tax_znak'];
    } else {
      $data['module_pixsel_price_tax_znak'] = $this->config->get('module_pixsel_price_tax_znak');
    }

    if (isset($this->request->post['module_pixsel_price_tax_names_with'])) {
      $data['module_pixsel_price_tax_names_with'] = $this->request->post['module_pixsel_price_tax_names_with'];
    } else {
      $data['module_pixsel_price_tax_names_with'] = $this->config->get('module_pixsel_price_tax_names_with');
    }

    if (isset($this->request->post['module_pixsel_price_tax_names_without'])) {
      $data['module_pixsel_price_tax_names_without'] = $this->request->post['module_pixsel_price_tax_names_without'];
    } else {
      $data['module_pixsel_price_tax_names_without'] = $this->config->get('module_pixsel_price_tax_names_without');
    }


    $filePath = $this->config->get('module_pixsel_price_file_rate');
    $data['file_exists'] = is_file($filePath);
    $data['file_path'] = $data['file_exists'] ? $filePath : '';
    $data['file_name'] = $data['file_exists'] ? basename($filePath) : '';


    $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
    unset($this->session->data['success']);

    $data['user_token'] = $this->session->data['user_token'];

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');


    $this->response->setOutput($this->load->view('extension/module/pixsel_price', $data));
  }

  public function save()
  {

    $this->load->language('extension/module/pixsel_parser');
    $this->load->model('extension/module/pixsel_parser');
    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

      $this->model_extension_module_pixsel_parser->savePrice($this->request->post['prices']);
      $this->model_extension_module_pixsel_parser->savePixselBox($this->request->post['pixsel_box']);
      $this->model_extension_module_pixsel_parser->savePixselMaterial($this->request->post['pixsel_material']);


      $this->model_setting_setting->editSetting('module_pixsel_price', [
        'module_pixsel_price_desc'              => $this->request->post['module_pixsel_price_desc'],
        'module_pixsel_price_is_desc'           => $this->request->post['module_pixsel_price_is_desc'],
        'module_pixsel_price_rate'              => $this->request->post['module_pixsel_price_rate'],
        'module_pixsel_price_file_rate'         => $this->config->get('module_pixsel_price_file_rate'),

        'module_pixsel_price_tax_on'            => $this->request->post['module_pixsel_price_tax_on'] ?? '',
        'module_pixsel_price_tax_rate'          => $this->request->post['module_pixsel_price_tax_rate'],
        'module_pixsel_price_tax_znak'          => $this->request->post['module_pixsel_price_tax_znak'],
        'module_pixsel_price_tax_names_with'    => $this->request->post['module_pixsel_price_tax_names_with'],
        'module_pixsel_price_tax_names_without' => $this->request->post['module_pixsel_price_tax_names_without'],
      ]);


      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('extension/module/pixsel_price', 'user_token=' . $this->session->data['user_token'], true));
    }

    $this->index();
  }


  public function save_ajax()
  {
    $json = [];
    try {
      if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {

        $this->load->language('extension/module/pixsel_parser');
        $this->load->model('extension/module/pixsel_parser');
        $this->load->model('setting/setting');

        $this->model_extension_module_pixsel_parser->savePrice($this->request->post['prices']);
        $this->model_extension_module_pixsel_parser->savePixselBox($this->request->post['pixsel_box']);
        $this->model_extension_module_pixsel_parser->savePixselMaterial($this->request->post['pixsel_material']);


        $is_desc = isset($this->request->post['module_pixsel_price_is_desc']) ? $this->request->post['module_pixsel_price_is_desc'] : '';
        $this->model_setting_setting->editSetting('module_pixsel_price', [
          'module_pixsel_price_desc' => $this->request->post['module_pixsel_price_desc'],
          'module_pixsel_price_is_desc' => $is_desc,
          'module_pixsel_price_rate' => $this->request->post['module_pixsel_price_rate'],
          'module_pixsel_price_file_rate' => $this->config->get('module_pixsel_price_file_rate'),

          'module_pixsel_price_tax_on'            => $this->request->post['module_pixsel_price_tax_on'] ?? '',
          'module_pixsel_price_tax_rate'          => $this->request->post['module_pixsel_price_tax_rate'],
          'module_pixsel_price_tax_znak'          => $this->request->post['module_pixsel_price_tax_znak'],
          'module_pixsel_price_tax_names_with'    => $this->request->post['module_pixsel_price_tax_names_with'],
          'module_pixsel_price_tax_names_without' => $this->request->post['module_pixsel_price_tax_names_without'],
        ]);

        if ($this->import()) {
          $json['success'] = $this->language->get('text_success_import' . $is_desc);
        } else {
          $json['error'] = $this->language->get('error_import');
        }
      } else {
        $json['error'] = $this->language->get('error_permission') ?: 'Помилка доступу!';
      }
    } catch (Exception $e) {
      $json['error'] = $e->getMessage();
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }


  public function import()
  {

    $this->load->model('extension/module/pixsel_parser');

    $this->model_extension_module_pixsel_parser->importProductDiscounts();

    $descs = $this->config->get('module_pixsel_price_desc');
    $is_descs = $this->config->get('module_pixsel_price_is_desc');
    if ($is_descs && is_array($descs)) {
      $this->model_extension_module_pixsel_parser->importProductDesc($descs);
    }

    $this->model_extension_module_pixsel_parser->importProductMaterial();

    $this->load->controller('extension/module/seo_url_generator/actionMassGenerateFnURL', 'product');
    $this->load->controller('extension/module/seo_url_generator/actionMassGenerateFnURL', 'category');

    return true;
  }

  protected function validate()
  {
    if (!$this->user->hasPermission('modify', 'extension/module/pixsel_price')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    return !$this->error;
  }


  public function rate_file()
  {
    $this->load->language('extension/module/pixsel_price');

    $json = array();

    if ($this->request->server['REQUEST_METHOD'] == 'POST') {
      $action = isset($this->request->get['action']) ? $this->request->get['action'] : '';

      $uploadPath = DIR_UPLOAD . 'rate/';

      if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
      }

      switch ($action) {
        case 'create':
        case 'edit':
          if (!empty($this->request->files['file']['name'])) {
            $this->deleteExistingFile($uploadPath);
            $filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));
            $targetFile = $uploadPath . $filename;

            if (move_uploaded_file($this->request->files['file']['tmp_name'], $targetFile)) {
              $json['success'] = $this->language->get('text_success_upload');

              $this->load->model('setting/setting');
              $this->model_setting_setting->editSettingValue('module_pixsel_price', 'module_pixsel_price_file_rate', $targetFile);
            } else {
              $json['error'] = $this->language->get('text_error_upload');
            }
          }
          break;
        case 'delete':
          // Обробка видалення файлу
          if ($this->deleteExistingFile($uploadPath)) {
            $json['success'] = $this->language->get('text_success_delete');
          } else {
            $json['error'] = $this->language->get('text_error_delete');
          }
          break;
        default:
          $json['error'] = $this->language->get('text_invalid_action');
          break;
      }
    } else {
      $json['error'] = $this->language->get('text_invalid_request');
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  private function deleteExistingFile($path)
  {
    $files = glob($path . '*');

    foreach ($files as $file) {
      if (is_file($file)) {
        unlink($file);
        return true;
      }
    }

    return false;
  }

  public function downloadFile()
  {

 
    $fileName = isset($this->request->get['file']) ? $this->request->get['file'] : '';

    $filePath = DIR_UPLOAD . 'rate/' . basename($fileName);
    
    if (!empty($fileName) && is_file($filePath) && file_exists($filePath)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
      header('Content-Transfer-Encoding: binary');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filePath));
      flush();
      readfile($filePath);
      exit();
    } else {
      $this->response->redirect($this->url->link('error/not_found', '', true));
    }
  }


  function get_tax_inform() {

    $is_on_tax = $this->config->get('module_pixsel_price_tax_on');
    if(!$is_on_tax) return '';

    $data = array();
    $data['rate'] = $this->config->get('module_pixsel_price_tax_rate');
    $data['znak'] = $this->config->get('module_pixsel_price_tax_znak');

    $current_lang = $this->config->get('config_language');
    $lang_with = $this->config->get('module_pixsel_price_tax_names_with');
    $lang_without = $this->config->get('module_pixsel_price_tax_names_with');

    $data['lang_with'] = $lang_with[$current_lang];
    $data['lang_without'] = $lang_without[$current_lang];

    return $data;

  }

  function calculate_tax($data)
  {
    $rate = $data['rate'];
    $znak = $data['znak'];
    $price = $data['price'];

    $tax_amount = $price * ($rate / 100);

    if ($znak == 'plus') {
      $final_price = $price + $tax_amount;
    } elseif ($znak == 'minus') {
      $final_price = $price - $tax_amount;
    } else {
      return $price;
    }

    return $final_price;
  }

}
