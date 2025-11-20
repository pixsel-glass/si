<?php

class ControllerExtensionModulePixselParser extends Controller
{
  private $error = array();

  public function index()
  {
    $this->load->language('extension/module/pixsel_parser');
    $this->document->setTitle($this->language->get('heading_title'));
    $this->load->model('setting/setting');

    $data['user_token'] = $this->session->data['user_token'];

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

      if (isset($this->request->post['module_pixsel_parser_main_category_id']) && $this->request->post['module_pixsel_parser_main_category_id'] === '0') {
        $this->request->post['module_pixsel_parser_main_category'] = '';
      }

      $this->model_setting_setting->editSetting('module_pixsel_parser', $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success') ?: 'Налаштування збережено!';

      $this->response->redirect($this->url->link('extension/module/pixsel_parser', 'user_token=' . $this->session->data['user_token'], true));
    }

    $data['heading_title'] = $this->language->get('heading_title');

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_extension'),
      'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('extension/module/pixsel_parser', 'user_token=' . $this->session->data['user_token'], true)
    );


    $data['action'] = $this->url->link('extension/module/pixsel_parser', 'user_token=' . $this->session->data['user_token'], true);
    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);




    if (isset($this->request->post['module_pixsel_parser_status'])) {
      $data['module_pixsel_parser_status'] = $this->request->post['module_pixsel_parser_status'];
    } else {
      $data['module_pixsel_parser_status'] = $this->config->get('module_pixsel_parser_status');
    }

    if (isset($this->request->post['module_pixsel_parser_iterations'])) {
      $data['module_pixsel_parser_iterations'] = $this->request->post['module_pixsel_parser_iterations'];
    } else {
      $data['module_pixsel_parser_iterations'] = $this->config->get('module_pixsel_parser_iterations');
    }

    if (isset($this->request->post['module_pixsel_parser_feed_url'])) {
      $data['module_pixsel_parser_feed_url'] = $this->request->post['module_pixsel_parser_feed_url'];
    } else {
      $data['module_pixsel_parser_feed_url'] = $this->config->get('module_pixsel_parser_feed_url');
    }

    if (isset($this->request->post['module_pixsel_parser_prefixes'])) {
      $data['module_pixsel_parser_prefixes'] = $this->request->post['module_pixsel_parser_prefixes'];
    } else {
      $data['module_pixsel_parser_prefixes'] = $this->config->get('module_pixsel_parser_prefixes');
    }

    if (isset($this->request->post['module_pixsel_parser_option'])) {
      $data['module_pixsel_parser_option'] = $this->request->post['module_pixsel_parser_option'];
    } else {
      $data['module_pixsel_parser_option'] = $this->config->get('module_pixsel_parser_option');
    }

    if (isset($this->request->post['module_pixsel_parser_option_variants'])) {
      $data['module_pixsel_parser_option_variants'] = $this->request->post['module_pixsel_parser_option_variants'];
    } else {
      $data['module_pixsel_parser_option_variants'] = $this->config->get('module_pixsel_parser_option_variants');
    }

    if (isset($this->request->post['module_pixsel_parser_login'])) {
      $data['module_pixsel_parser_login'] = $this->request->post['module_pixsel_parser_login'];
    } else {
      $data['module_pixsel_parser_login'] = $this->config->get('module_pixsel_parser_login');
    }

    if (isset($this->request->post['module_pixsel_parser_password'])) {
      $data['module_pixsel_parser_password'] = $this->request->post['module_pixsel_parser_password'];
    } else {
      $data['module_pixsel_parser_password'] = $this->config->get('module_pixsel_parser_password');
    }

    if (isset($this->request->post['module_pixsel_parser_main_category_id'])) {
      $data['module_pixsel_parser_main_category_id'] = $this->request->post['module_pixsel_parser_main_category_id'];
    } else {
      $data['module_pixsel_parser_main_category_id'] = $this->config->get('module_pixsel_parser_main_category_id');
    }

    if (isset($this->request->post['module_pixsel_parser_main_category'])) {
      $data['module_pixsel_parser_main_category'] = $this->request->post['module_pixsel_parser_main_category'];
    } else {
      $data['module_pixsel_parser_main_category'] = $this->config->get('module_pixsel_parser_main_category');
    }



    if (!empty($data['module_pixsel_parser_login']) && !empty($data['module_pixsel_parser_password'])) {
      $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
      $protocol = $isHttps ? "https://" : "http://";
      $base_url = $protocol . $_SERVER['HTTP_HOST'];
      $data['cron_command'] = "0 0 * * * curl -X POST -d 'login=" . $data['module_pixsel_parser_login'] . "&password=" . $data['module_pixsel_parser_password'] . "' " . $base_url . "/pixsel_cron_import.php";
    } else {
      $data['cron_command'] = '';
    }




    $this->load->model('catalog/option');
    $options = $this->model_catalog_option->getOptions();
    $data['options'] = $options;


    if ($data['module_pixsel_parser_option']) {
      $this->load->model('catalog/option');
      $data['option_values'] = $this->model_catalog_option->getOptionValues($data['module_pixsel_parser_option']);
    }


    // language prefixes
    $this->load->model('localisation/language');
    $data['languages'] = $this->model_localisation_language->getLanguages();


    //log
    $logFile = DIR_LOGS . 'pixsel_parser_import.log';
    $data['logData'] = $this->_getLastLogLines($logFile, 10);

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
    unset($this->session->data['success']);

    $this->response->setOutput($this->load->view('extension/module/pixsel_parser', $data));
  }
  protected function validate()
  {
    if (!$this->user->hasPermission('modify', 'extension/module/pixsel_parser')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    return !$this->error;
  }

  public function install()
  {

    $this->load->model('user/user_group');
    $adminGroupId = 1;
    $this->model_user_user_group->addPermission($adminGroupId, 'access', 'extension/module/pixsel_parser');
    $this->model_user_user_group->addPermission($adminGroupId, 'modify', 'extension/module/pixsel_parser');

    $this->model_user_user_group->addPermission($adminGroupId, 'access', 'extension/module/pixsel_price');
    $this->model_user_user_group->addPermission($adminGroupId, 'modify', 'extension/module/pixsel_price');


    $this->load->model('setting/setting');
    $this->model_setting_setting->editSetting('module_pixsel_parser', ['module_pixsel_parser_status' => 1]);

    // Check and add new columns to oc_product table
    $columns = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product`");
    $existing_columns = array_column($columns->rows, 'Field');

    $fields_to_add = [
      'pixsel_product_id' => 'varchar(64) NOT NULL',
      'pixsel_date_modified' => 'datetime NOT NULL',
      'pixsel_price_type' => 'varchar(255) NOT NULL',
      'pixsel_diagonal' => 'varchar(255) NOT NULL',
      'pixsel_area' => 'decimal(15,8) NOT NULL',
      'pixsel_box' => 'varchar(255) NOT NULL',
      'pixsel_wheel' => 'varchar(255) NOT NULL',
    ];

    foreach ($fields_to_add as $field => $definition) {
      if (!in_array($field, $existing_columns)) {
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `{$field}` {$definition};");
      }
    }

    // Check and add new column to oc_product_option_value table
    $columns = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_option_value`");
    $existing_columns = array_column($columns->rows, 'Field');
    if (!in_array('pixsel_sku', $existing_columns)) {
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` ADD `pixsel_sku` varchar(255) NOT NULL;");
    }
    if (!in_array('pixsel_price_material', $existing_columns)) {
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` ADD `pixsel_price_material` decimal(15,2) NOT NULL;");
    }

    // Check and add new column to oc_product_description table
    $columns = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_description`");
    $existing_columns = array_column($columns->rows, 'Field');
    if (!in_array('pixsel_short_name', $existing_columns)) {
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_description` ADD `pixsel_short_name` varchar(255) NOT NULL;");
    }

    // Check and add new columns to oc_category table
    $columns = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "category`");
    $existing_columns = array_column($columns->rows, 'Field');

    $fields_to_add = [
      'pixsel_category_id' => 'int(11) NOT NULL',
      'pixsel_date_modified' => 'datetime NOT NULL',
      'svg' => 'varchar(255) NULL'
    ];

    foreach ($fields_to_add as $field => $definition) {
      if (!in_array($field, $existing_columns)) {
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "category` ADD `{$field}` {$definition};");
      }
    }

    // pixsel price type
    $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pixsel_price` (
      `pixsel_price_id` int(11) NOT NULL AUTO_INCREMENT,
      `pixel_price_type` varchar(255) NOT NULL,
      `customer_group_id` int(11) NOT NULL,
      `price` decimal(15,2) NOT NULL,
      PRIMARY KEY (`pixsel_price_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

    $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pixsel_box` (
      `pixsel_box_id` int(11) NOT NULL AUTO_INCREMENT,
      `pixsel_box` varchar(255) NOT NULL,
      `price` decimal(15,2) NOT NULL,
       PRIMARY KEY (`pixsel_box_id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

    $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pixsel_material` (
        `pixsel_material_id` int(11) NOT NULL AUTO_INCREMENT,
        `option_id` int(11) NOT NULL,
        `option_value_id` int(11) NOT NULL,
        `price` decimal(15,2) NOT NULL,
        PRIMARY KEY (`pixsel_material_id`),
        KEY `option_id` (`option_id`),
        KEY `option_value_id` (`option_value_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

    $this->db->query("
      CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "order_box` (
          `order_box_id` int(11) NOT NULL AUTO_INCREMENT,
          `product_id` int(11) NOT NULL,
          `order_id` int(11) NOT NULL,
          `product_option_value_id` int(11) NOT NULL,
          `pixsel_box_id` int(11) NOT NULL,
          `name` varchar(255) NOT NULL,
          `quantity` int(4) NOT NULL,
          PRIMARY KEY (`order_box_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
    ");


    // my sklad

    $columns = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer_group`");
    $existing_columns = array_column($columns->rows, 'Field');
    if (!in_array('customer_group_my_sklad', $existing_columns)) {
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_group` ADD `customer_group_my_sklad` varchar(255) NOT NULL;");
    }

    $columns = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "category`");
    $existing_columns = array_column($columns->rows, 'Field');
    if (!in_array('category_my_sklad', $existing_columns)) {
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "category` ADD `category_my_sklad` varchar(255) NOT NULL;");
    }

    $columns = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer`");
    $existing_columns = array_column($columns->rows, 'Field');
    if (!in_array('customer_my_sklad', $existing_columns)) {
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD `customer_my_sklad` varchar(255) NOT NULL;");
    }
   

  }


  public function uninstall()
  {
    $this->load->model('setting/setting');
    $this->load->model('extension/module/pixsel_parser');

    $this->model_setting_setting->deleteSetting('module_pixsel_parser');
    $this->model_setting_setting->deleteSetting('module_pixsel_price');

    // Remove columns from oc_product table
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` 
          DROP COLUMN `pixsel_product_id`, 
          DROP COLUMN `pixsel_date_modified`, 
          DROP COLUMN `pixsel_price_type`, 
          DROP COLUMN `pixsel_diagonal`, 
          DROP COLUMN `pixsel_area`, 
          DROP COLUMN `pixsel_wheel`, 
          DROP COLUMN `pixsel_box`;");

    // Remove column from oc_product_option_value table
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` DROP COLUMN `pixsel_sku`;");

    $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` DROP COLUMN `pixsel_price_material`;");

    // Remove column from oc_product_description table
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_description` DROP COLUMN `pixsel_short_name`;");

    // Remove columns from oc_category table
    $this->db->query("ALTER TABLE `" . DB_PREFIX . "category` 
    DROP COLUMN `pixsel_category_id`, 
    DROP COLUMN `svg`, 
    DROP COLUMN `pixsel_date_modified`;");

    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pixsel_price`;");
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pixsel_box`;");
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pixsel_material`;");

     // my sklad
     $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer_group` DROP COLUMN `customer_group_my_sklad`;");
     $this->db->query("ALTER TABLE `" . DB_PREFIX . "category` DROP COLUMN `category_my_sklad`;");
     $this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` DROP COLUMN `customer_my_sklad`;");


  }

  public function importData($type = '')
  {

    ini_set('display_errors', 0);

    $this->load->language('extension/module/pixsel_parser');
    $this->load->model('setting/setting');
    $this->load->model('extension/module/pixsel_parser');
    $this->load->model('localisation/language');

    $lang_ids = $this->model_extension_module_pixsel_parser->getAllLanguageIds();

    $prefixs = $this->config->get('module_pixsel_parser_prefixes'); // [uk-ua => '_uk', 'en-gb' => '_en']

    $languages = $this->model_localisation_language->getLanguages();
    $languageKeys = array_flip(array_column($languages, 'code'));
    $prefixs = array_intersect_key($prefixs, $languageKeys);


    $variants = $this->config->get('module_pixsel_parser_option_variants');

    $iteration = $this->config->get('module_pixsel_parser_iterations') !== null && (int)$this->config->get('module_pixsel_parser_iterations') > 0 ? $this->config->get('module_pixsel_parser_iterations') : 50;
    $offset = isset($this->request->post['offset']) ? $this->request->post['offset'] : 0;
    $type = isset($this->request->post['type']) ? $this->request->post['type'] : $type;

    $json = array();

    if (!$type || $type === 'all') {
      $filePath = $this->downloadFeed();
    } else {
      $filePath = $this->downloadFeed(false);
    }

    if ($filePath) {
      $xml = simplexml_load_file($filePath);

      if (!$xml) {
        $json['error'] = $this->language->get('text_feed_xml_error');
        $this->out($json);
        return;
      }

      // $type = $type ? $type : 'categories';
      $type = $type ? $type : 'categories';

      if ($type === 'categories') {
        $new_array_categories = $this->parserCategory($xml, $prefixs, $lang_ids);
        $all_categories_count = count($new_array_categories);
        // $all_categories_count = 100;
        $process = $this->importCategory($new_array_categories, $offset, $iteration);


        if ($process['offset'] >= $all_categories_count) {
          // $type = 'products';
          // $offset = 0;

          $deleted = $this->deleteOldCategories($new_array_categories);

          $json['complete'] = [
            'type' => $type,
            'title' => 'created/updated categories:',
            'offset' => $offset,
            'updated' => $process['updated'],
            'created' => $process['created'],
            'deleted' => $deleted,
            'next' => 'products'
          ];

          $this->out($json);
          return;
        } else {

          $json['process'] = [
            'type' => $type,
            'offset' => $process['offset'],
            'all' => $all_categories_count,
            'updated' => $process['updated'],
            'created' => $process['created'],
          ];

          $this->out($json);
          return;
        }
      }

      if ($type === 'products') {

        $arr_new_products = $this->parserProduct($xml, $prefixs, $lang_ids, array_flip($variants));
        $all_products_count = count($arr_new_products);
        // $all_products_count = 100;

        $process = $this->importProduct($arr_new_products, $offset, $iteration);

        if ($process['offset'] >= $all_products_count) {

          // If you have a similar deleteOldProducts() method for products:
          $deleted = $this->deleteOldProducts($arr_new_products);
          // $deleted = 0;

          $json['complete'] = [
            'type' => $type,
            'title' => 'created/updated products:',
            'updated' => $process['updated'],
            'created' => $process['created'],
            'deleted' => $deleted,
          ];
        } else {
          $json['process'] = [
            'type' => $type,
            'offset' => $process['offset'],
            'all' => $all_products_count,
            'updated' => $process['updated'],
            'created' => $process['created'],
          ];

          $this->out($json);
          return;
        }
      }


      if ($type === 'all') {
        $new_array_categories = $this->parserCategory($xml, $prefixs, $lang_ids);
        $resCategory = $this->importCategory($new_array_categories, 0, 0);

        $arr_new_products = $this->parserProduct($xml, $prefixs, $lang_ids, array_flip($variants));
        $resProduct = $this->importProduct($arr_new_products, 0, 0);

        $resProduct['deleted'] = $this->deleteOldProducts($arr_new_products);
        $resCategory['deleted'] = $this->deleteOldCategories($new_array_categories);

        $this->load->controller('extension/module/pixsel_price/import');
        $this->model_extension_module_pixsel_parser->reset_cache();

        return [
          'Updated Categories' => $resCategory['updated'],
          'Created Categories' => $resCategory['created'],
          'Deleted Categories' => $resCategory['deleted'],
          'Updated Products' => $resProduct['updated'],
          'Created Products' => $resProduct['created'],
          'Deleted Products' => $resProduct['deleted'],
        ];
      }

      $json['success'] = 'ok';
    } else {
      $json['error'] = $this->language->get('text_feed_download_error');
    }

    $this->load->controller('extension/module/seo_url_generator/actionMassGenerateFnURL', 'product');
    $this->load->controller('extension/module/seo_url_generator/actionMassGenerateFnURL', 'category');

    $this->load->controller('extension/module/pixsel_price/import');
    $this->model_extension_module_pixsel_parser->reset_cache();
    $this->out($json);
  }

  // functions ALL
  private function deleteOldCategories($all_categories)
  {
    $existingCategories = $this->model_extension_module_pixsel_parser->getCategoriesExisting();
    $deleted = 0;
    // Створюємо масив із pixsel_category_id з $all_categories
    $pixsel_category_ids = array_column($all_categories, 'pixsel_category_id');

    foreach ($existingCategories as $category) {
      if (!in_array($category['pixsel_category_id'], $pixsel_category_ids) && $category['pixsel_category_id'] != 0 && $category['pixsel_category_id'] != '') {

        if ($category['image']) {
          $full_image_path = DIR_IMAGE . $category['image'];
          $this->deleteOldImages($full_image_path);
        }
        if ($category['svg']) {
          $full_image_path = DIR_IMAGE . $category['svg'];
          $this->deleteOldImages($full_image_path);
        }
        $this->model_extension_module_pixsel_parser->deleteCategoryById($category['category_id']);
        $deleted++;
      }
    }
    return $deleted;
  }


  private function deleteOldProducts($all_products)
  {
    $existingProducts = $this->model_extension_module_pixsel_parser->getProductsExisting();
    $deleted = 0;

    // Створюємо масив із pixsel_product_id з $all_products
    $pixsel_product_ids = array_column($all_products, 'pixsel_product_id');

    foreach ($existingProducts as $product) {
      if (!in_array($product['pixsel_product_id'], $pixsel_product_ids) && !empty($product['pixsel_product_id'])) {

        $folder_path = DIR_IMAGE . 'pixsel/products/' . $product['product_id'];

        if (is_dir($folder_path)) {
          $this->recursiveDelete($folder_path);
        }

        $this->model_extension_module_pixsel_parser->deleteProductById($product['product_id']);

        $deleted++;
      }
    }

    return $deleted;
  }


  private function out($json)
  {
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }
  private function downloadFeed($notDownload = true)
  {

    $feedUrl = $this->config->get('module_pixsel_parser_feed_url');

    if (empty($feedUrl)) {
      return false;
    }

    $filePath = DIR_DOWNLOAD . 'pixsel_feed.xml';


    if (file_exists($filePath)) {
      if (!$notDownload) {
        return $filePath;
      }
      unlink($filePath);
    }

    $feedContent = @file_get_contents($feedUrl);
    if ($feedContent === false) {
      return false;
    }

    $saveResult = @file_put_contents($filePath, $feedContent);
    if ($saveResult === false) {
      return false;
    }
    return $filePath;
  }
  private function parserCategory($xml, $prefixs, $lang_ids)
  {
    $categories = array();

    foreach ($xml->catalog->category as $category) {

      $pixsel_category_id = (string)$category['id'];
      $last_modify = (string)$category['last_modify'];

      $categoryArray = array(
        'pixsel_category_id' => $pixsel_category_id,
        'last_modified' => $last_modify,
      );

      if (isset($category['parentId'])) {
        $categoryArray['parent_id'] = (string)$category['parentId'];
      }
      if (isset($category['image'])) {
        $categoryArray['image'] = (string)$category['image'];
      }else{

      }
      if (isset($category['img_svg']) && !empty($category['img_svg'])){
        $categoryArray['img_svg'] = (string)$category['img_svg'];
      }
      if (isset($category['position'])) {
        $categoryArray['position'] = (int)$category['position'];
      }

      foreach ($prefixs as $lang_code => $prefix) {
        if(isset($category['name' . $prefix])){
          $categoryArray['names'][$lang_ids[$lang_code]] = (string)$category['name' . $prefix];
        }
      }

      $categories[] = $categoryArray;
    }

    return $categories;
  }

  private function parserProduct($xml, $prefixs, $lang_ids, $variants)
  {
    $products = array();


    foreach ($xml->items->item as $product) {

      $pixsel_product_id = (string)$product['id'];
      $last_modify = (string)$product['last_modify'];
      $available = (string)$product['available'] === "true" ?? false;
      $visible = (string)$product['visible'] === "true" ?? false;
      $position = (int)$product->position ?? '';
      $sku = (string)$product->cod ?? '';
      $wheel = (string)$product->wheel ?? '';
      $manufacturer_name = (string)$product->vendor ?? '';

      $productArray = array(
        'pixsel_product_id' => $pixsel_product_id,
        'last_modified' => $last_modify,
        'available' => $available,
        'visible' => $visible,
        'position' => $position,
        'sku' => $sku,
        'wheel' => $wheel,
        'manufacturer_name' => $manufacturer_name
      );

      foreach ($prefixs as $lang_code => $prefix) {
        if (isset($product->{'name' . $prefix})) {
          $productArray['names'][$lang_ids[$lang_code]] = (string)$product->{'name' . $prefix};
        }
        if (isset($product->{'name_short' . $prefix})) {
          $productArray['short_names'][$lang_ids[$lang_code]] = (string)$product->{'name_short' . $prefix};
        }
        if (isset($product->{'description' . $prefix})) {
          $productArray['descriptions'][$lang_ids[$lang_code]] = (string)$product->{'description' . $prefix};
        }
      }

      $productArray['category_id'] = (string)$product->categoryId;

      // Images
      $productArray['images'] = [];
      foreach ($product->image as $image) {
        $productArray['images'][] = (string)$image;
      }

      $productArray['vendor'] = (string)$product->vendor;

      // Parameters
      foreach ($product->param as $param) {
        $name = (string)$param['name'];
        $productArray['params'][$name] = (string)$param;
      }

      // Variants
      $productArray['variants'] = [];
      foreach ($product->variant as $variant) {
        $type = (string)$variant['type'];
        $typeId = isset($variants[$type]) ? $variants[$type] : '';
        if (!$typeId) continue;
        $productArray['variants'][$typeId] = (string)$variant;
      }

      $products[] = $productArray;
    }

    return $products;
  }


  private function importCategory($xml, $offset = 0, $iteration = 0)
  {
    $updated_categories = 0;
    $created_categories = 0;

    if ($iteration > 0) {
      $xml = array_slice($xml, $offset, $iteration);
    }

    foreach ($xml as $category_data) {
      $pixsel_category_id = $category_data['pixsel_category_id'];
      $last_modified = strtotime($category_data['last_modified']);

      $existing_category = $this->model_extension_module_pixsel_parser->getCategoryPixselById($pixsel_category_id);

      $images = [];
      if (isset($category_data['image'])) {
        $images[] = $category_data['image'];
      }
      if (isset($category_data['img_svg'])) {
        $images[] = $category_data['img_svg'];
      }

      if ($existing_category) {

        $existing_last_modified = strtotime($existing_category['pixsel_date_modified']);
        
        if ($last_modified > $existing_last_modified) {
          $this->model_extension_module_pixsel_parser->editCategory($existing_category['category_id'], $category_data);
          $this->processImages($images, $existing_category, 'category');
          $updated_categories++;
        }

      } else {
        // Категории нет, создаем новую
        $item = $this->model_extension_module_pixsel_parser->addCategory($category_data);
        $this->processImages($images, $item, 'category');
        $created_categories++;
      }
    }

    return [
      'offset' => $offset + count($xml),
      'updated' => $updated_categories,
      'created' => $created_categories
    ];
  }

  private function importProduct($products, $offset = 0, $iteration = 0)
  {
    $updated_products = 0;
    $created_products = 0;

    if ($iteration > 0) {
      $products = array_slice($products, $offset, $iteration);
    }

    foreach ($products as $product_data) {
      $pixsel_product_id = $product_data['pixsel_product_id'];
      $last_modified = strtotime($product_data['last_modified']);

      $existing_product = $this->model_extension_module_pixsel_parser->getProductPixselById($pixsel_product_id);

      if ($existing_product) {
        $existing_last_modified = strtotime($existing_product['pixsel_date_modified']);

        if ($last_modified > $existing_last_modified) {
          $this->model_extension_module_pixsel_parser->editProduct($existing_product['product_id'], $product_data);


          $this->processImages($product_data['images'], $existing_product, 'product');

          $updated_products++;
        }
      } else {
        // Товару немає, створюємо новий
        $item = $this->model_extension_module_pixsel_parser->addProduct($product_data);

        // Додаткова обробка зображень, якщо необхідно
        $this->processImages($product_data['images'], $item, 'product');

        $created_products++;
      }
    }

    return [
      'offset' => $offset + count($products),
      'updated' => $updated_products,
      'created' => $created_products
    ];
  }


  private function processImages($images = array(), $item, $item_type)
  {
    $image_path = DIR_IMAGE . 'pixsel/';

    if ($item_type == 'category') {
      if (!is_dir($image_path . 'categories/')) {
        mkdir($image_path . 'categories/', 0755, true);
      }
      $image_path .= 'categories/';

      if ($item['image']) {
        $this->deleteOldImages($image_path . basename($item['image']));
      }
    } elseif ($item_type == 'product') {
      if (!is_dir($image_path . 'products/' . $item['product_id'] . '/')) {
        mkdir($image_path . 'products/' . $item['product_id'] . '/', 0755, true);
      }
      $image_path .= 'products/' . $item['product_id'] . '/';

      $old_product_images = $this->model_extension_module_pixsel_parser->getProductImagesByProductId($item['product_id']);

      foreach ($old_product_images as $old_image) {
        $this->deleteOldImages($image_path . basename($old_image));
      }
      $this->model_extension_module_pixsel_parser->removeProductImages($item['product_id']);
    }

    foreach ($images as $index => $image_url) {
      $new_image_path = $this->uploadAndProcessImage($image_url, $image_path, $item_type);

      // devrvk: добавил замену полного пути на пустоту

      if ($new_image_path) {
        if ($item_type == 'category') {
          $this->model_extension_module_pixsel_parser->linkImageToCategory(str_replace(DIR_IMAGE, "", $new_image_path), $item['category_id']);
        } elseif ($item_type == 'product') {
          $this->model_extension_module_pixsel_parser->linkImageToProduct(str_replace(DIR_IMAGE, "", $new_image_path), $item['product_id'], $index);
        }
      }
    }
  }


  private function uploadAndProcessImage($image_url, $image_path, $item_type)
  {
    $image_data = $this->getImageDataFromURL($image_url);

    if ($image_data !== false) {
      $extension = $this->getFileExtensionFromURL($image_url);

      if ($extension) {
        $new_image_path = $image_path . $item_type . '_' . uniqid() . $extension;
        file_put_contents($new_image_path, $image_data);

        return $new_image_path;
      }
    }

    return false;
  }

  private function deleteOldImages($image_path)
  {
    if (is_file($image_path)) {
      unlink($image_path);
    }
  }

  private function getImageDataFromURL($image_url)
  {
    $max_attempts = 5;
    $attempt = 0;
    $image_data = false;

    while ($attempt < $max_attempts) {
      $ch = curl_init($image_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $image_data = curl_exec($ch);

      if (curl_errno($ch) || !$image_data) {

        $attempt++;

        curl_close($ch);

        sleep(3);
        continue;
      }

      // Закрити поточний сеанс cURL
      curl_close($ch);
      break;
    }

    return $image_data;
  }


  private function getFileExtensionFromURL($image_url)
  {
    $extension = pathinfo($image_url, PATHINFO_EXTENSION);
    return $extension ? '.' . $extension : '';
  }

  private function recursiveDelete($dir)
  {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir . "/" . $object) == "dir") {
            $this->recursiveDelete($dir . "/" . $object);
          } else {
            unlink($dir . "/" . $object);
          }
        }
      }
      reset($objects);
      rmdir($dir);
    }
  }


  // delete all data

  public function deleteAllData()
  {

    $this->load->model('extension/module/pixsel_parser');

    $onlyProduct = $this->request->get['only_product'] == '1' ?? false;

    $existingProducts = $this->model_extension_module_pixsel_parser->getProductsExisting();
    foreach ($existingProducts as $product) {
      if (!empty($product['pixsel_product_id'])) {

        $folder_path = DIR_IMAGE . 'pixsel/products/' . $product['product_id'];

        if (is_dir($folder_path)) {
          $this->recursiveDelete($folder_path);
        }
        $this->model_extension_module_pixsel_parser->deleteProductById($product['product_id']);
      }
    }

    if(!$onlyProduct) {
      $existingCategories = $this->model_extension_module_pixsel_parser->getCategoriesExisting();

      foreach ($existingCategories as $category) {
        if ($category['pixsel_category_id'] != 0 && $category['pixsel_category_id'] != '') {
          if ($category['image']) {
            $full_image_path = DIR_IMAGE . $category['image'];
            $this->deleteOldImages($full_image_path);
          }
          $this->model_extension_module_pixsel_parser->deleteCategoryById($category['category_id']);
        }
      }
    }


    $this->model_extension_module_pixsel_parser->reset_cache();

    $json['success'] = 'ok';

    $this->out($json);
  }


  public function autocomplete()
  {
    $json = array();

    if (isset($this->request->get['filter_name'])) {
      $this->load->model('catalog/category');

      $filter_data = array(
        'filter_name' => $this->request->get['filter_name'],
        'start'       => 0,
        'limit'       => 10
      );

      $results = $this->model_catalog_category->getCategories($filter_data);

      foreach ($results as $result) {
        $json[] = array(
          'category_id' => $result['category_id'],
          'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
        );
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }


  private function _getLastLogLines($file, $lines = 10) {
    if (!file_exists($file) || !is_readable($file)) {
      return '';
    }

    $content = '';
    $fp = fopen($file, 'rb');
    $block = 4096;
    $max = filesize($file);

    if ($max === 0) {
      return '';
    }

    for ($len = 0; $len < $max; $len += $block) {
      $seekSize = ($max - $len > $block) ? $block : $max - $len;
      fseek($fp, -$len - $seekSize, SEEK_END);
      $content = fread($fp, $seekSize) . $content;
      if (substr_count($content, "\n") >= $lines + 1) {
        preg_match("!(.*?\n){".($lines+1)."}$!s", $content, $match);
        fclose($fp);
        return $match[0];
      }
    }
    fclose($fp);
    return $content;
  }


  private function _writeToLog($message) {
    $logFile = DIR_LOGS . 'pixsel_parser_import.log';
    $currentDate = date('Y-m-d H:i:s');
    file_put_contents($logFile, '(site) ' . $currentDate . ' - ' . $message . PHP_EOL, FILE_APPEND);
  }

  public function logger(){

    $data = $this->request->post;

    $logString = '';

    if(!$data){
      return;
    }
    foreach ($data as $key => $value) {

      $nameType = $key == 'category' ? 'Categories' : 'Products';

      foreach ($value as $name => $val) {

        $logString .=ucfirst($name) .' '. $nameType . ": " . $val . "; ";
      }
    }
    $logString = rtrim($logString, '; ') . '.';

    $this->_writeToLog($logString);
  }

}
