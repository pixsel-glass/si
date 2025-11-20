<?php
class ModelExtensionModulePixselParser extends Model
{


  public function getCategoriesExisting()
  {
    $existing_categories_query = $this->db->query("SELECT category_id, pixsel_category_id, pixsel_date_modified, image, svg FROM " . DB_PREFIX . "category");

    return $existing_categories_query->rows;
  }

  public function getProductsExisting()
  {
    $existing_products_query = $this->db->query("SELECT product_id, pixsel_product_id, pixsel_date_modified FROM " . DB_PREFIX . "product");

    return $existing_products_query->rows;
  }


  public function getCategoryPixselById($pixsel_category_id)
  {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE pixsel_category_id = '" . (int)$pixsel_category_id . "'");
    return $query->row;
  }

  public function getProductPixselById($pixsel_product_id)
  {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE pixsel_product_id = '" . (string)$pixsel_product_id . "'");
    return $query->row;
  }



  public function getAllLanguageIds()
  {
    $query = $this->db->query("SELECT language_id, code FROM " . DB_PREFIX . "language");
    $language_ids = array();
    foreach ($query->rows as $row) {
      $language_ids[$row['code']] = $row['language_id'];
    }
    return $language_ids;
  }


  public function linkImageToCategory($image_path, $category_id)
  {
    $relative_path = substr($image_path, strpos($image_path, 'pixsel/'));

    if (strtolower(substr(strrchr($image_path, '.'), 1)) == 'svg') {
      $this->db->query("UPDATE " . DB_PREFIX . "category SET svg = '" . $this->db->escape($relative_path) . "' WHERE category_id = '" . (int)$category_id . "'");
    } else {
      $this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape($relative_path) . "' WHERE category_id = '" . (int)$category_id . "'");
    }
  }

  public function editCategory($category_id, $category_data)
  {
    $parent_id = 0;
    $top = 0;
    $status = 1;

    $main_category = $this->config->get('module_pixsel_parser_main_category_id') ?: 0;

    if (isset($category_data['parent_id'])) {
      $parent_id = $this->getOriginalCategoryId($category_data['parent_id']);
    } else if ($main_category > 0) {
      $parent_id = $main_category;
    }


    if ($parent_id == 0) {
      $top = 1;
    }

    $sql = "UPDATE " . DB_PREFIX . "category SET 
    parent_id = '" . (int)$parent_id . "', 
    pixsel_date_modified = '" . $this->db->escape($category_data['last_modified']) . "', 
    status = '" . (int)$status . "', 
    top = '" . (int)$top . "', 
    sort_order = '" . (int)$category_data['position'] . "', 
    date_modified = NOW()";

    $sql .= " WHERE category_id = '" . (int)$category_id . "'";
    $this->db->query($sql);

    $deleteSql = "DELETE FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'";
    $this->db->query($deleteSql);

    foreach ($category_data['names'] as $language_id => $name) {
      $insertSql = "INSERT INTO " . DB_PREFIX . "category_description (category_id, language_id, name) VALUES ('" . (int)$category_id . "', '" . (int)$language_id . "', '" . $this->db->escape($name) . "')";
      $this->db->query($insertSql);
    }


    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");
    if (!$query->num_rows) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store (category_id, store_id) VALUES ('" . (int)$category_id . "', '0')");
    }

    // старі записи
    $this->db->query("DELETE FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$category_id . "'");
    // нові записи
    $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET category_id = '" . (int)$category_id . "', path_id = '" . (int)$category_id . "', level = '0'");
    if ($parent_id > 0) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$parent_id . "' ORDER BY level ASC");

      foreach ($query->rows as $result) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET category_id = '" . (int)$category_id . "', path_id = '" . (int)$result['path_id'] . "', level = '" . ((int)$result['level'] + 1) . "'");
      }
    }
  }

  public function deleteCategoryById($category_id)
  {
    $this->db->query("DELETE FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$category_id . "'");
  }

  public function deleteProductById($product_id)
  {

    $this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");

    $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

    // Видалення опцій продукту
    $queryOption = $this->db->query("SELECT product_option_id FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
    foreach ($queryOption->rows as $row) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$row['product_option_id'] . "'");
    }
    $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
  }


  public function addCategory($category_data)
  {
    $parent_id = 0;
    $top = 0;
    $status = 1;

    $main_category = $this->config->get('module_pixsel_parser_main_category_id') ?: 0;

    if (isset($category_data['parent_id'])) {
      $parent_id = $this->getOriginalCategoryId($category_data['parent_id']);
    } else if ($main_category > 0) {
      $parent_id = $main_category;
    }


    if ($parent_id == 0) {
      $top = 1;
    }

    // Основна інформація про категорію
    $this->db->query(
      "INSERT INTO " . DB_PREFIX . "category SET 
          parent_id = '" . (int)$parent_id . "', 
          top = '" . (int)$top . "',
          sort_order = '" . (int)$category_data['position'] . "', 
          status = '" . (int)$status . "',
          date_modified = NOW(), 
          date_added = NOW(), 
          pixsel_category_id = '" . (int)$category_data['pixsel_category_id'] . "', 
          pixsel_date_modified = '" . $this->db->escape($category_data['last_modified']) . "'"
    );

    $category_id = $this->db->getLastId();

    // Імена категорій для різних мов
    foreach ($category_data['names'] as $language_id => $name) {
      $this->db->query(
        "INSERT INTO " . DB_PREFIX . "category_description SET 
              category_id = '" . (int)$category_id . "', 
              language_id = '" . (int)$language_id . "', 
              name = '" . $this->db->escape($name) . "'"
      );
    }


    $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store (category_id, store_id) VALUES ('" . (int)$category_id . "', '0')");

    // Додавання самої категорії до шляху
    $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET category_id = '" . (int)$category_id . "', path_id = '" . (int)$category_id . "', level = '0'");

    // Якщо ця категорія має батька, додаємо шлях до батька
    if ($parent_id > 0) {
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$parent_id . "' ORDER BY level ASC");

      foreach ($query->rows as $result) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "category_path SET category_id = '" . (int)$category_id . "', path_id = '" . (int)$result['path_id'] . "', level = '" . ((int)$result['level'] + 1) . "'");
      }
    }

    // return $category_id;  // повертаємо ID новоствореної категорії

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");

    return $query->row;  // повертаємо весь рядок новоствореної категорії
  }


  public function reset_cache()
  {

    $this->cache->delete('category');
    $this->cache->delete('product');

    if ($this->config->get('config_seo_pro')) {
      $this->cache->delete('seopro');
    }
  }




  private function getOriginalCategoryId($pixsel_category_id)
  {
    $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE pixsel_category_id = '" . (int)$pixsel_category_id . "'");

    if ($query->num_rows > 0) {
      return $query->row['category_id'];
    }

    return 0;
  }




  public function removeProductImages($product_id)
  {
    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET image = ''  WHERE `product_id` = '" . (int)$product_id . "'");
    $this->db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE `product_id` = '" . (int)$product_id . "'");
  }

  public function linkImageToProduct($image_path, $product_id, $index)
  {
    // If the path saved in the database starts directly from 'image/', then no 'catalog/' is required
    $relative_path = substr($image_path, strpos($image_path, 'pixsel/'));

    if ($index == 0) {
      $this->db->query("UPDATE `" . DB_PREFIX . "product` SET `image` = '" . $this->db->escape($relative_path) . "' WHERE `product_id` = '" . (int)$product_id . "'");
    } else {
      $this->db->query("INSERT INTO `" . DB_PREFIX . "product_image` (`product_id`, `image`, `sort_order`) VALUES ('" . (int)$product_id . "', '" . $this->db->escape($relative_path) . "', '" . (int)$index . "')");
    }
  }


  public function getProductImagesByProductId($product_id)
  {
    // Fetch the main image from oc_product
    $main_image_query = $this->db->query("SELECT `image` FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . (int)$product_id . "'");

    $result = [];

    // If there's a main image, add it to the results
    if ($main_image_query->num_rows) {
      $result[] = $main_image_query->row['image'];
    }

    // Fetch the additional images from oc_product_image
    $additional_images_query = $this->db->query("SELECT `image` FROM `" . DB_PREFIX . "product_image` WHERE `product_id` = '" . (int)$product_id . "'");

    foreach ($additional_images_query->rows as $row) {
      $result[] = $row['image'];
    }

    return $result;
  }



  // public function getCategory($category_id) {
  // 	$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id) AS path FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

  // 	return $query->row;
  // }



  public function addProduct($data)
  {

    $in_stock = $data['available'] ? 7 : 5;
    $cat_id = $this->getOriginalCategoryId($data['category_id']);
    $shipping = 1;
    $price = 0.00;
    $length_class_id = 1;
    $is_visible = $data['visible'] ? 1 : 0;

    $article = isset($data['params']['Article']) ? $this->db->escape($data['params']['Article']) : '';
    $priceType = isset($data['params']['Price']) ? $this->db->escape($data['params']['Price']) : '';
    $diagonal = isset($data['params']['Diagonal']) ? $this->db->escape($data['params']['Diagonal']) : '';
    $area = isset($data['params']['Area']) ? $this->db->escape($data['params']['Area']) : '';
    $box = isset($data['params']['Box']) ? $this->db->escape($data['params']['Box']) : '';
    $position = isset($data['position']) ? $this->db->escape($data['position']) : '';
    $sku = isset($data['sku']) ? $this->db->escape($data['sku']) : '';
    $wheel = isset($data['wheel']) ? $this->db->escape($data['wheel']) : '';


    $this->db->query("
    INSERT INTO " . DB_PREFIX . "product 
    SET 
        model = '" . $article . "',
        sku = '" . $sku . "',
        upc = '',
        ean = '',
        jan = '',
        isbn = '',
        mpn = '',
        location = '',
        quantity = '1000',
        minimum = '',
        subtract = '0',
        stock_status_id = '" . (int)$in_stock . "',
        date_available = '',
        manufacturer_id = '" . (int)$cat_id . "',
        shipping = '" . (int)$shipping . "',
        price = '" . (float)$price . "',
        points = '',
        weight = '',
        weight_class_id = '',
        length = '',
        width = '',
        height = '',
        length_class_id = '" . (int)$length_class_id . "',
        status = '" . (int)$is_visible . "',
        noindex = '" . (int)1 . "',
        tax_class_id = '',
        sort_order = '" . (int)$position . "',
        pixsel_product_id = '" . $this->db->escape($data['pixsel_product_id']) . "',
        pixsel_price_type = '" . $priceType . "',
        pixsel_diagonal = '" . $diagonal . "',
        pixsel_area = '" . $area . "',
        pixsel_box = '" . $box . "',
        pixsel_wheel = '" . $wheel . "',
        pixsel_date_modified = '" . $this->db->escape($data['last_modified']) . "',
        date_added = NOW(),
        date_modified = NOW()
  ");

    $product_id = $this->db->getLastId();


    foreach ($data['names'] as $language_id => $name) {

      $short_name = $data['short_names'][$language_id] ?? "";
      $description = $data['descriptions'][$language_id] ?? "";

      $insertSql = "INSERT INTO " . DB_PREFIX . "product_description 
      (product_id, language_id, name, pixsel_short_name, description) 
      VALUES 
      ('" . (int)$product_id . "', 
      '" . (int)$language_id . "', 
      '" . $this->db->escape($name) . "', 
      '" . $this->db->escape($short_name) . "', 
      '" . $this->db->escape($description) . "')";

      $this->db->query($insertSql);
    }


    $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store (product_id, store_id) VALUES ('" . (int)$product_id . "', '0')");

    if (isset($data['category_id'])) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$cat_id . "', main_category = 1");
    }


    if (isset($data['variants'])) {

      $option_id = $this->config->get('module_pixsel_parser_option');
      // Додати головний запис опції продукту
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', required = 1"); // Встановити required=1 за умовчанням
      $product_option_id = $this->db->getLastId();

      foreach ($data['variants'] as $option_value_id => $value) {
        // Значення за умовчанням
        $default_quantity = 0;
        $default_subtract = 0;
        $default_price = 0.0;
        $default_price_prefix = '+';
        $default_points = 0;
        $default_points_prefix = '+';
        $default_weight = 0.0;
        $default_weight_prefix = '+';

        $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$option_value_id . "', quantity = '" . (int)$default_quantity . "', subtract = '" . (int)$default_subtract . "', price = '" . (float)$default_price . "', price_prefix = '" . $this->db->escape($default_price_prefix) . "', points = '" . (int)$default_points . "', points_prefix = '" . $this->db->escape($default_points_prefix) . "', weight = '" . (float)$default_weight . "', weight_prefix = '" . $this->db->escape($default_weight_prefix) . "', pixsel_sku = '" . $this->db->escape($value) . "'");
      }
    }

    // manufacturer set
    $this->setManufacturerByNameForProduct($product_id, $data['manufacturer_name']);

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");

    return $query->row;
  }

  public function editProduct($product_id, $data)
  {


    $option_id = $this->config->get('module_pixsel_parser_option');
    $cat_id = $this->getOriginalCategoryId($data['category_id']);

    $sqlSetArr = [];
    if (isset($data['params']['Article'])) {
      $sqlSetArr[] = "model = '" . $this->db->escape($data['params']['Article']) . "'";
    }
    if (isset($data['sku'])) {
      $sqlSetArr[] = "sku = '" . $this->db->escape($data['sku']) . "'";
    }
    if (isset($data['wheel'])) {
      $sqlSetArr[] = "pixsel_wheel = '" . $this->db->escape($data['wheel']) . "'";
    }
    if (isset($data['position'])) {
      $sqlSetArr[] = "sort_order = '" . $this->db->escape($data['position']) . "'";
    }
    if (isset($data['available'])) {
      $in_stock = $data['available'] ? 7 : 5;
      $sqlSetArr[] = "stock_status_id = '" . (int)$in_stock . "'";
    }

    if (isset($data['category_id'])) {
      $sqlSetArr[] = "manufacturer_id = '" . (int)$cat_id . "'";
    }

    if (isset($data['visible'])) {
      $is_visible = $data['visible'] ? 1 : 0;
      $sqlSetArr[] = "status = '" . (int)$is_visible . "'";
    }

    if (isset($data['pixsel_product_id'])) {
      $sqlSetArr[] = "pixsel_product_id = '" . $this->db->escape($data['pixsel_product_id']) . "'";
    }

    if (isset($data['params']['Price'])) {
      $sqlSetArr[] = "pixsel_price_type = '" . $this->db->escape($data['params']['Price']) . "'";
    }

    if (isset($data['params']['Diagonal'])) {
      $sqlSetArr[] = "pixsel_diagonal = '" . $this->db->escape($data['params']['Diagonal']) . "'";
    }

    if (isset($data['params']['Area'])) {
      $sqlSetArr[] = "pixsel_area = '" . $this->db->escape($data['params']['Area']) . "'";
    }

    if (isset($data['params']['Box'])) {
      $sqlSetArr[] = "pixsel_box = '" . $this->db->escape($data['params']['Box']) . "'";
    }

    if (isset($data['last_modified'])) {
      $sqlSetArr[] = "pixsel_date_modified = '" . $this->db->escape($data['last_modified']) . "'";
    }



    if (!empty($sqlSetArr)) {
      $sqlSetString = implode(", ", $sqlSetArr);

      // Update the product table
      $this->db->query("
              UPDATE " . DB_PREFIX . "product 
              SET $sqlSetString
              WHERE product_id = '" . (int)$product_id . "'
          ");
    }

    // Update the product description for each language
    if (isset($data['names'])) {
      foreach ($data['names'] as $language_id => $name) {
        $short_name = $data['short_names'][$language_id] ?? "";
        $description = $data['descriptions'][$language_id] ?? "";

        $this->db->query("
              UPDATE " . DB_PREFIX . "product_description 
              SET name = '" . $this->db->escape($name) . "', 
                  pixsel_short_name = '" . $this->db->escape($short_name) . "', 
                  description = '" . $this->db->escape($description) . "'
              WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'
          ");
      }
    }


    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "' AND store_id = '0'");

    if ($query->num_rows) {
      // Якщо продукт вже є в магазині, можна виконати оновлення (за потреби). У цьому прикладі оновлення нічого не робить.
      // $this->db->query("UPDATE " . DB_PREFIX . "product_to_store SET ... WHERE product_id = '" . (int)$product_id . "' AND store_id = '0'");
    } else {
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store (product_id, store_id) VALUES ('" . (int)$product_id . "', '0')");
    }

    if (isset($data['category_id'])) {

      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
      if ($query->num_rows) {
        $this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = 1 WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$cat_id . "'");
      } else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$cat_id . "', main_category = 1");
      }
    }



    // Обробка варіантів опції:
    if (isset($data['variants'])) {
      $existingOptions = [];
      $queryOption = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");

      if ($queryOption->num_rows) {
        $product_option_id = $queryOption->row['product_option_id'];

        // Отримуємо існуючі варіанти з бази:
        $queryValues = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option_id . "'");
        foreach ($queryValues->rows as $row) {
          $existingOptions[$row['option_value_id']] = $row;
        }

        // Проходимося по варіантах з $data:
        foreach ($data['variants'] as $option_value_id => $value) {
          if (isset($existingOptions[$option_value_id])) {
            // Оновлення:
            $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET pixsel_sku = '" . $this->db->escape($value) . "' WHERE product_option_id = '" . (int)$product_option_id . "' AND option_value_id = '" . (int)$option_value_id . "'");
            unset($existingOptions[$option_value_id]);
          } else {
            // Вставка нового варіанта:
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$option_value_id . "', quantity = '0', subtract = '0', price = '0.0', price_prefix = '+', points = '0', points_prefix = '+', weight = '0.0', weight_prefix = '+', pixsel_sku = '" . $this->db->escape($value) . "'");
          }
        }

        // Видалення варіантів, які більше не існують:
        foreach ($existingOptions as $option_value_id => $value) {
          $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option_id . "' AND option_value_id = '" . (int)$option_value_id . "'");
        }
      } else {
        // Якщо опції немає - вставляємо нову:
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', required = 1");
        $product_option_id = $this->db->getLastId();

        foreach ($data['variants'] as $option_value_id => $value) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$option_value_id . "', quantity = '0', subtract = '0', price = '0.0', price_prefix = '+', points = '0', points_prefix = '+', weight = '0.0', weight_prefix = '+', pixsel_sku = '" . $this->db->escape($value) . "'");
        }
      }
    }

    // manufacturer set
    $this->setManufacturerByNameForProduct($product_id, $data['manufacturer_name']);
  }


  public function getDistinctPixelPriceTypes()
  {
    $query = $this->db->query("SELECT DISTINCT pixsel_price_type FROM " . DB_PREFIX . "product WHERE pixsel_price_type != '' AND pixsel_price_type IS NOT NULL ORDER BY pixsel_price_type ASC");

    return $query->rows;
  }


  public function getDistinctPixelBox()
  {
    $query = $this->db->query("SELECT DISTINCT pixsel_box FROM " . DB_PREFIX . "product WHERE pixsel_box != '' AND pixsel_box IS NOT NULL ORDER BY pixsel_box ASC");

    return $query->rows;
  }


  public function savePrice($data)
  {

    $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "pixsel_price`");

    foreach ($data as $pixelPriceType => $groupPrices) {
      foreach ($groupPrices as $customerGroupId => $price) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "pixsel_price` SET pixel_price_type = '" . $this->db->escape($pixelPriceType) . "', customer_group_id = '" . (int)$customerGroupId . "', price = '" . (float)$price . "'");
      }
    }
  }

  public function savePixselBox($data)
  {
    $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "pixsel_box`");

    foreach ($data as $pixselBox => $price) {
      $this->db->query("INSERT INTO `" . DB_PREFIX . "pixsel_box` SET pixsel_box = '" . $this->db->escape($pixselBox) . "', price = '" . (float)$price . "'");
    }
  }

  public function savePixselMaterial($data)
  {

    $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "pixsel_material`");

    foreach ($data as $optionId => $values) {
      foreach ($values as $optionValueId => $price) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "pixsel_material` SET option_id = '" . (int)$optionId . "', option_value_id = '" . (int)$optionValueId . "', price = '" . (float)$price . "'");
      }
    }
  }


  public function getPixselPrices()
  {
    $result = array();
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pixsel_price`");

    foreach ($query->rows as $row) {
      if (!isset($result[$row['pixel_price_type']])) {
        $result[$row['pixel_price_type']] = array();
      }
      $result[$row['pixel_price_type']][$row['customer_group_id']] = $row['price'];
    }

    return $result;
  }

  public function getPixselBoxPrices()
  {
    $result = array();
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pixsel_box`");

    foreach ($query->rows as $row) {
      $result[$row['pixsel_box']] = $row['price'];
    }

    return $result;
  }

  public function getPixelMaterialPrices()
  {
    $result = array();
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pixsel_material`");

    foreach ($query->rows as $row) {
      if (!isset($result[$row['option_id']])) {
        $result[$row['option_id']] = array();
      }
      $result[$row['option_id']][$row['option_value_id']] = $row['price'];
    }

    return $result;
  }



  public function areAllPricesSet()
  {
    $this->load->model('customer/customer_group');
    $this->load->model('catalog/option');
    $this->load->model('setting/setting');


    $module_status = $this->config->get('module_pixsel_parser_status');
    if (!$module_status) {
      return false;
    }

    $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'pixsel_price_type'");
    if ($query->num_rows == 0) {
      return false;
    }

    $optionId = $this->config->get('module_pixsel_parser_option') ?: '';
    $optionValues = [];
    $materialPrices = [];
    if ($optionId) {
      $materialPrices = $this->model_extension_module_pixsel_parser->getPixelMaterialPrices();
      $optionValues = $this->model_catalog_option->getOptionValues($optionId);
    }

    $types = $this->getDistinctPixelPriceTypes();
    $boxes = $this->getDistinctPixelBox();
    $groups = $this->model_customer_customer_group->getCustomerGroups();
    $prices = $this->getPixselPrices();
    $boxPrices = $this->getPixselBoxPrices();

    foreach ($types as $type) {
      foreach ($groups as $group) {
        if (!isset($prices[$type['pixsel_price_type']][$group['customer_group_id']]) || $prices[$type['pixsel_price_type']][$group['customer_group_id']] <= 0) {
          return false;
        }
      }
    }

    foreach ($boxes as $box) {
      if (!isset($boxPrices[$box['pixsel_box']]) || $boxPrices[$box['pixsel_box']] <= 0) {
        return false;
      }
    }

    foreach ($optionValues as $value) {
      if (!isset($materialPrices[$optionId][$value['option_value_id']]) || $materialPrices[$optionId][$value['option_value_id']] <= 0) {
        return false;
      }
    }

    return true;
  }



  public function importProductDiscounts()
  {

    $productPrices = $this->getPixselPrices();
    // Вибірка всіх товарів, де вказано pixsel_price_type
    $query = $this->db->query("SELECT product_id, pixsel_price_type FROM " . DB_PREFIX . "product WHERE pixsel_price_type != '' AND pixsel_price_type IS NOT NULL");

    foreach ($query->rows as $product) {
      $priceType = $product['pixsel_price_type'];

      if (isset($productPrices[$priceType])) {
        foreach ($productPrices[$priceType] as $customerGroupId => $price) {
          // Перевірка, чи існує
          $checkQuery = $this->db->query("SELECT product_discount_id FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product['product_id'] . "' AND customer_group_id = '" . (int)$customerGroupId . "'");

          if ($price <= 0) {
            if ($checkQuery->num_rows) {
              // Видалення запису, якщо ціна дорівнює нулю
              $this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_discount_id = '" . (int)$checkQuery->row['product_discount_id'] . "'");
            }
            continue;
          }


          if ($checkQuery->num_rows) {
            // Оновлення існуючого запису
            $this->db->query("UPDATE " . DB_PREFIX . "product_discount SET price = '" . (float)$price . "' WHERE product_discount_id = '" . (int)$checkQuery->row['product_discount_id'] . "'");
          } else {
            // Створення нового запису
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product['product_id'] . "', customer_group_id = '" . (int)$customerGroupId . "', quantity = 1, priority = 999, price = '" . (float)$price . "', date_start = '0000-00-00', date_end = '0000-00-00'");
          }
        }
      }
    }
  }


  public function importProductDesc($descs)
  {
    $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE pixsel_product_id != '' AND pixsel_product_id != '0'");

    foreach ($query->rows as $product) {
      foreach ($descs as $language_id => $description) {


        $checkQuery = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product['product_id'] . "' AND language_id = '" . (int)$language_id . "'");

        if ($checkQuery->num_rows) {

          $this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($description['description']) . "' WHERE product_id = '" . (int)$product['product_id'] . "' AND language_id = '" . (int)$language_id . "'");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_description (product_id, language_id, description) VALUES ('" . (int)$product['product_id'] . "', '" . (int)$language_id . "', '" . $this->db->escape($description['description']) . "')");
        }
      }
    }
  }

  public function importProductMaterial()
  {
    $this->load->model('setting/setting');

    $optionId = $this->config->get('module_pixsel_parser_option') ?: '';

    $rate = $this->config->get('module_pixsel_price_rate') ?: 1;

    if ($optionId) {
      $query = $this->db->query("SELECT product_id, pixsel_area FROM " . DB_PREFIX . "product WHERE pixsel_product_id != '' AND pixsel_product_id != '0'");

      $materialPrices = $this->model_extension_module_pixsel_parser->getPixelMaterialPrices();

      foreach ($query->rows as $product) {
        // Вибірка всіх значень опцій для кожного товару
        $optionValueQuery = $this->db->query("SELECT product_option_value_id, option_value_id FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product['product_id'] . "' AND option_id = '" . (int)$optionId . "'");

        foreach ($optionValueQuery->rows as $optionValue) {
          $area = $product['pixsel_area'];
          $optionValueId = $optionValue['option_value_id'];

          if (isset($materialPrices[$optionId][$optionValueId])) {
            $price = $area * ((float)$materialPrices[$optionId][$optionValueId] * (float)$rate);

            // Оновлення поля pixsel_price_material
            $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET pixsel_price_material = '" . (float)$price . "' WHERE product_option_value_id = '" . (int)$optionValue['product_option_value_id'] . "'");
          }
        }
      }
    }
  }


  // manufacturer set product
  public function setManufacturerByNameForProduct($product_id, $manufacturer_name)
  {

    $manufacturer_id = $this->_getManufacturerIdByName($manufacturer_name);

    if (!$manufacturer_id) {
      $manufacturer_id = $this->_createManufacturer($manufacturer_name);
    }
    if ($manufacturer_id && $product_id) {
      $this->_updateProductManufacturer($product_id, $manufacturer_id);
    }
  }

  private function _getManufacturerIdByName($manufacturer_name)
  {
    $query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "manufacturer WHERE name = '" . $this->db->escape($manufacturer_name) . "'");

    if ($query->num_rows) {
      return $query->row['manufacturer_id'];
    }

    return null;
  }

  private function _createManufacturer($manufacturer_name)
  {

    $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($manufacturer_name) . "', sort_order = 0");

    $manufacturer_id = $this->db->getLastId();

    $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = 0");

    return $manufacturer_id;
  }

  private function _updateProductManufacturer($product_id, $manufacturer_id)
  {
    $this->db->query("UPDATE " . DB_PREFIX . "product SET manufacturer_id = '" . (int)$manufacturer_id . "' WHERE product_id = '" . (int)$product_id . "'");
  }

  public function get_order_products_stats($filter = array())
  {
    $lang_id = $this->config->get('config_language_id');
    $whereParts = [];

    if (!empty($filter['start_date'])) {
      $startDate = $filter['start_date'];
      $whereParts[] = "o.date_added >= '" . $this->db->escape($startDate) . " 00:00:00'";
    }

    if (!empty($filter['end_date'])) {
      $endDate = $filter['end_date'];
      $whereParts[] = "o.date_added <= '" . $this->db->escape($endDate) . " 23:59:59'";
    }

    if (!empty($filter['order_id'])) {
      $order_id = $filter['order_id'];
      $whereParts[] = "o.order_id = '" . $this->db->escape($order_id) . "'";
    } elseif (!empty($filter['order_ids']) && is_array($filter['order_ids'])) {
      $order_ids = implode(',', array_map(function ($id) {
        return (int)$id;
      }, $filter['order_ids']));
      $whereParts[] = "o.order_id IN (" . $order_ids . ")";
    }

    $whereParts[] = "o.order_status_id != '0'";

    $where = !empty($whereParts) ? ' WHERE ' . implode(' AND ', $whereParts) : '';

    $sql = "SELECT o.order_status_id, op.*, 
            oo.product_option_value_id, oo.name AS option_name, oo.value AS option_value, 
            ob.quantity AS box_quantity, ob.name AS box_name, ob.pixsel_box_id,
            pov.pixsel_price_material, pov.option_value_id,
            p.pixsel_area,
            pb.price AS box_price,
            ovd.name AS option_value_name
            FROM `oc_order` o
            INNER JOIN `oc_order_product` op ON o.order_id = op.order_id
            LEFT JOIN `oc_order_option` oo ON op.order_product_id = oo.order_product_id
            LEFT JOIN `oc_order_box` ob ON oo.product_option_value_id = ob.product_option_value_id AND oo.order_id = ob.order_id
            LEFT JOIN `oc_product_option_value` pov ON oo.product_option_value_id = pov.product_option_value_id
            LEFT JOIN `oc_product` p ON op.product_id = p.product_id 
            LEFT JOIN `oc_pixsel_box` pb ON ob.pixsel_box_id = pb.pixsel_box_id 
            LEFT JOIN `oc_option_value_description` ovd ON pov.option_value_id = ovd.option_value_id AND ovd.language_id = '" . $lang_id . "'" .
      $where .
      " ";

    $query = $this->db->query($sql);

    return $query->rows;
  }

  public function get_stats($products)
  {

    $totalQuantity = 0;
    $optionTypes = [];
    $boxes = [];
    $totalPrice = 0.0;
    $uniqueOrderIds = [];
    $totalCostPrice = 0;

    $rate = $this->config->get('module_pixsel_price_rate');

    foreach ($products as $product) {
      if (!in_array($product['order_id'], $uniqueOrderIds)) {
        $uniqueOrderIds[] = $product['order_id'];
      }

      $totalQuantity += (int)$product['quantity'];

      if (!array_key_exists($product['option_value_name'], $optionTypes)) {
        $optionTypes[$product['option_value_name']]['qty'] = (int)$product['quantity'];
        $optionTypes[$product['option_value_name']]['area'] = (float)$product['pixsel_area'] * $optionTypes[$product['option_value_name']]['qty'];
      } else {
        $optionTypes[$product['option_value_name']]['qty'] += (int)$product['quantity'];
        $optionTypes[$product['option_value_name']]['area'] += (float)$product['pixsel_area'] * $optionTypes[$product['option_value_name']]['qty'];
      }

      if (!array_key_exists($product['box_name'], $boxes)) {
        $boxes[$product['box_name']] = (int)$product['box_quantity'];
      } else {
        $boxes[$product['box_name']] += (int)$product['box_quantity'];
      }


      $totalPrice += (float)$product['total'];
      
      $totalCostPrice += ((float)$product['pixsel_price_material'] * (int)$product['quantity']) + ((int)$product['box_quantity'] * ($rate * $product['box_price']));
    }

    $totalOrders = count($uniqueOrderIds);

    $totalGlass = 0;

    foreach ($optionTypes as $type) {
      if (isset($type['qty'])) {
        $totalGlass += $type['qty'];
      }
    }

    $percentages = [];
    foreach ($optionTypes as $type => $item) {
      $percentages[$type] = round(($item['qty'] / $totalGlass) * 100, 2);
    }


    $currentCurrencyCode = $this->session->data['currency'] ?? $this->config->get('config_currency');

    $formattedPrice = $this->currency->format($totalPrice, $currentCurrencyCode, '', true);
    $formattedCostPrice = $this->currency->format($totalCostPrice, $currentCurrencyCode, '', true);
    $formattedWinPrice = $this->currency->format(($totalPrice - $totalCostPrice), $currentCurrencyCode, '', true);

    return [
      'order' => $totalOrders,
      'products' => $totalQuantity,
      'optionTypes' => $optionTypes,
      'percentages' => $percentages,
      'totalPrice' => $formattedPrice,
      'totalCostPrice' => $formattedCostPrice,
      'totalWinPrice' => $formattedWinPrice,
      'boxes' => $boxes
    ];
  }
}
