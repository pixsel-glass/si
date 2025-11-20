<?php
class ControllerExtensionFeedMerchantFeed extends Controller
{
    private $error = array();
    public function index()
    {
        $this->load->language("extension/feed/merchant_feed");
        $this->document->setTitle($this->language->get("heading_title"));
        $this->load->model("setting/setting");
        $this->load->model("catalog/category");
        $this->load->model("catalog/manufacturer");
        if ($this->request->server["REQUEST_METHOD"] == "POST" && $this->validate()) {
            $this->model_setting_setting->editSetting("feed_merchant_feed", $this->request->post);
            $this->session->data["success"] = $this->language->get("text_success");
            if (isset($this->request->post["reload"])) {
                $this->response->redirect($this->url->link("extension/feed/merchant_feed", "user_token=" . $this->session->data["user_token"], true));
            } else {
                $this->response->redirect($this->url->link("marketplace/extension", "user_token=" . $this->session->data["user_token"] . "&type=feed", true));
            }
        }
        if (isset($this->request->server["HTTPS"]) && ($this->request->server["HTTPS"] == "on" || $this->request->server["HTTPS"] == "1")) {
            $server = HTTPS_CATALOG;
        } else {
            $server = HTTP_CATALOG;
        }
        $data["server"] = $server;
        if (isset($this->error["warning"])) {
            $data["error_warning"] = $this->error["warning"];
        } else {
            $data["error_warning"] = "";
        }
        $count_category = $this->model_catalog_category->getTotalCategories();
        $count_manufacturer = $this->model_catalog_manufacturer->getTotalManufacturers();
        if (ini_get("max_input_vars") < 10000) {
            $data["info"] = sprintf($this->language->get("error_max_input_vars"), ini_get("max_input_vars"));
        } else {
            if (ini_get("max_input_vars") < $count_category + $count_manufacturer) {
                $data["info"] = sprintf($this->language->get("error_max_input_vars_count"), ceil(($count_category + $count_manufacturer) / 1000) * 1000, ini_get("max_input_vars"));
            } else {
                $data["info"] = "";
            }
        }
        if (isset($this->error["key"])) {
            $data["error_warning"] = "";
            $data["no_license"] = "";
        } else {
            $data["error_warning"] = "";
            $data["no_license"] = "";
        }
        if (isset($this->session->data["success"])) {
            $data["success"] = $this->session->data["success"];
            unset($this->session->data["success"]);
        } else {
            $data["success"] = "";
        }
        $data["breadcrumbs"] = array();
        $data["breadcrumbs"][] = array("text" => $this->language->get("text_home"), "href" => $this->url->link("common/dashboard", "user_token=" . $this->session->data["user_token"], true));
        $data["breadcrumbs"][] = array("text" => $this->language->get("text_extension"), "href" => $this->url->link("extension/extension", "user_token=" . $this->session->data["user_token"] . "&type=feed", true));
        $data["breadcrumbs"][] = array("text" => $this->language->get("heading_title"), "href" => $this->url->link("extension/feed/merchant_feed", "user_token=" . $this->session->data["user_token"], true));
        $data["action"] = $this->url->link("extension/feed/merchant_feed", "user_token=" . $this->session->data["user_token"], true);
        $data["cancel"] = $this->url->link("marketplace/extension", "user_token=" . $this->session->data["user_token"] . "&type=feed", true);
        $data["category_page"] = $this->url->link("extension/feed/merchant_feed/category", "user_token=" . $this->session->data["user_token"], true);
        $data["user_token"] = $this->session->data["user_token"];
        if (isset($this->request->post["feed_merchant_feed_status"])) {
            $data["feed_merchant_feed_status"] = $this->request->post["feed_merchant_feed_status"];
        } else {
            $data["feed_merchant_feed_status"] = $this->config->get("feed_merchant_feed_status");
        }
        if (isset($this->request->post["feed_merchant_feed_key"])) {
            $data["feed_merchant_feed_key"] = 1;
        } else {
            $data["feed_merchant_feed_key"] = 1;
        }
        if (isset($this->request->post["feed_merchant_feed_settings"])) {
            $merchant_feed_settings = $this->request->post["feed_merchant_feed_settings"];
        } else {
            $merchant_feed_settings = $this->config->get("feed_merchant_feed_settings");
        }
        $data["feed_merchant_feed_settings"] = array();
        if ($merchant_feed_settings) {
            foreach ($merchant_feed_settings as $tab => $setting) {
                $data["feed_merchant_feed_settings"][$tab]["profil_title"] = $setting["profil_title"] ? $setting["profil_title"] : sprintf($this->language->get("text_profil_name"), $tab);
                $data["feed_merchant_feed_settings"][$tab]["language_id"] = $setting["language_id"];
                $data["feed_merchant_feed_settings"][$tab]["store_id"] = $setting["store_id"];
                $data["feed_merchant_feed_settings"][$tab]["save_file"] = $setting["save_file"];
                $data["feed_merchant_feed_settings"][$tab]["title"] = $setting["title"];
                $data["feed_merchant_feed_settings"][$tab]["description"] = $setting["description"];
                $data["feed_merchant_feed_settings"][$tab]["cron_key"] = $setting["cron_key"];
                $data["feed_merchant_feed_settings"][$tab]["currency"] = $setting["currency"];
                if (!empty($setting["url_prefix"])) {
                    $prefix = trim($setting["url_prefix"]) . "/";
                } else {
                    $prefix = "";
                }
                if (!empty($setting["cron_key"])) {
                    $data["feed_merchant_feed_settings"][$tab]["data_feed_cron"] = "wget -O - -q -t 1 '" . $server . $prefix . "index.php?route=extension/feed/merchant_feed/generateCron&key=" . $setting["cron_key"] . "&feed_id=" . $tab . "'";
                    $data["feed_merchant_feed_settings"][$tab]["data_feed"] = $server . $prefix . "index.php?route=extension/feed/merchant_feed&key=" . $setting["cron_key"] . "&feed_id=" . $tab;
                    $data["feed_merchant_feed_settings"][$tab]["data_feed_hand"] = $server . $prefix . "index.php?route=extension/feed/merchant_feed/generateCron&key=" . $setting["cron_key"] . "&feed_id=" . $tab;
                    $data["feed_merchant_feed_settings"][$tab]["data_feed_reviews"] = $server . $prefix . "index.php?route=extension/feed/merchant_feed/reviewFeed&key=" . $setting["cron_key"];
                    $data["feed_merchant_feed_settings"][$tab]["data_feed_stat"] = $server . "google_feed_" . $tab . "_" . $setting["currency"] . ".xml";
                } else {
                    $data["feed_merchant_feed_settings"][$tab]["data_feed_cron"] = "";
                    $data["feed_merchant_feed_settings"][$tab]["data_feed"] = "";
                    $data["feed_merchant_feed_settings"][$tab]["data_feed_reviews"] = "";
                    $data["feed_merchant_feed_settings"][$tab]["data_feed_hand"] = "";
                    $data["feed_merchant_feed_settings"][$tab]["data_feed_stat"] = "";
                }
                $data["feed_merchant_feed_settings"][$tab]["product_stock"] = $setting["product_stock"];
                if (!empty($setting["product_stock_status"])) {
                    $data["feed_merchant_feed_settings"][$tab]["product_stock_status"] = $setting["product_stock_status"];
                } else {
                    $data["feed_merchant_feed_settings"][$tab]["product_stock_status"] = array();
                }
                $data["feed_merchant_feed_settings"][$tab]["product_gtin"] = $setting["product_gtin"];
                $data["feed_merchant_feed_settings"][$tab]["product_mpn"] = $setting["product_mpn"];
                $data["feed_merchant_feed_settings"][$tab]["product_adult"] = $setting["product_adult"];
                $data["feed_merchant_feed_settings"][$tab]["product_condition"] = $setting["product_condition"];
                $data["feed_merchant_feed_settings"][$tab]["product_id"] = $setting["product_id"];
                $data["feed_merchant_feed_settings"][$tab]["product_descr"] = $setting["product_descr"];
                $data["feed_merchant_feed_settings"][$tab]["product_html"] = $setting["product_html"];
                $data["feed_merchant_feed_settings"][$tab]["product_special"] = $setting["product_special"];
                if (!empty($setting["product_category"])) {
                    $data["feed_merchant_feed_settings"][$tab]["product_category"] = $setting["product_category"];
                } else {
                    $data["feed_merchant_feed_settings"][$tab]["product_category"] = array();
                }
                if (!empty($setting["manufacturer_category"])) {
                    $data["feed_merchant_feed_settings"][$tab]["manufacturer_category"] = $setting["manufacturer_category"];
                } else {
                    $data["feed_merchant_feed_settings"][$tab]["manufacturer_category"] = array();
                }
                $data["feed_merchant_feed_settings"][$tab]["attribute_id"] = $setting["attribute_id"];
                $data["feed_merchant_feed_settings"][$tab]["option_id"] = $setting["option_id"];
                $data["feed_merchant_feed_settings"][$tab]["url_prefix"] = $setting["url_prefix"];
                $data["feed_merchant_feed_settings"][$tab]["product_black_list"] = array();
                if (!empty($setting["product_black_list"])) {
                    $this->load->model("catalog/product");
                    $products = $setting["product_black_list"];
                    foreach ($products as $product_id) {
                        $related_info = $this->model_catalog_product->getProduct($product_id);
                        if ($related_info) {
                            $data["feed_merchant_feed_settings"][$tab]["product_black_list"][] = array("product_id" => $related_info["product_id"], "name" => $related_info["name"]);
                        }
                    }
                }
            }
        }
        $this->load->model("localisation/language");
        $data["languages"] = $this->model_localisation_language->getLanguages();
        $this->load->model("setting/store");
        $data["stores"] = $this->model_setting_store->getStores();
        $data["manufacturers"] = $this->model_catalog_manufacturer->getManufacturers();
        $this->load->model("catalog/option");
        $data["options"] = $this->model_catalog_option->getOptions();
        $this->load->model("catalog/attribute");
        $data["attributes"] = $this->model_catalog_attribute->getAttributes();
        $this->load->model("localisation/stock_status");
        $data["stock_statuses"] = $this->model_localisation_stock_status->getStockStatuses();
        $this->load->model("localisation/currency");
        $data["currencies"] = $this->model_localisation_currency->getCurrencies();
        $filter_data = array("sort" => "name", "order" => "ASC");
        $data["categories"] = $this->model_catalog_category->getCategories($filter_data);
        $match = "";
        $key = preg_match("#(?<=\\/\\/).+?(?=\\/)#", $server, $match);
        if (isset($this->request->server["HTTPS"]) && ($this->request->server["HTTPS"] == "on" || $this->request->server["HTTPS"] == "1")) {
            $data["server"] = HTTPS_CATALOG;
        } else {
            $data["server"] = HTTP_CATALOG;
        }
        $data["header"] = $this->load->controller("common/header");
        $data["column_left"] = $this->load->controller("common/column_left");
        $data["footer"] = $this->load->controller("common/footer");
        $this->response->setOutput($this->load->view("extension/feed/merchant_feed", $data));
    }
    public function category()
    {
        $this->load->language("extension/feed/merchant_feed");
        $this->load->model("catalog/category");
        $this->load->model("setting/setting");
        $this->document->setTitle($this->language->get("heading_title_category"));
        if ($this->request->server["REQUEST_METHOD"] == "POST") {
            $this->model_setting_setting->editSetting("feed_merchant_feed_category", $this->request->post);
            $this->session->data["success"] = $this->language->get("text_success");
            $this->response->redirect($this->url->link("extension/feed/merchant_feed/category", "user_token=" . $this->session->data["user_token"], true));
        }
        if (isset($this->session->data["success"])) {
            $data["success"] = $this->session->data["success"];
            unset($this->session->data["success"]);
        } else {
            $data["success"] = "";
        }
        $count_category = $this->model_catalog_category->getTotalCategories();
        if (ini_get("max_input_vars") < 10000) {
            $data["info"] = sprintf($this->language->get("error_max_input_vars"), ini_get("max_input_vars"));
        } else {
            if (ini_get("max_input_vars") < $count_category) {
                $data["info"] = sprintf($this->language->get("error_max_input_vars_count"), ceil($count_category / 1000) * 1000, ini_get("max_input_vars"));
            } else {
                $data["info"] = "";
            }
        }
        $data["breadcrumbs"] = array();
        $data["breadcrumbs"][] = array("text" => $this->language->get("text_home"), "href" => $this->url->link("common/dashboard", "user_token=" . $this->session->data["user_token"], true));
        $data["breadcrumbs"][] = array("text" => $this->language->get("text_extension"), "href" => $this->url->link("extension/extension", "user_token=" . $this->session->data["user_token"] . "&type=feed", true));
        $data["breadcrumbs"][] = array("text" => $this->language->get("heading_title"), "href" => $this->url->link("extension/feed/merchant_feed", "user_token=" . $this->session->data["user_token"], true));
        $data["breadcrumbs"][] = array("text" => $this->language->get("heading_title_category"), "href" => $this->url->link("extension/feed/merchant_feed/category", "user_token=" . $this->session->data["user_token"], true));
        $data["action"] = $this->url->link("extension/feed/merchant_feed/category", "user_token=" . $this->session->data["user_token"], true);
        $data["cancel"] = $this->url->link("extension/feed/merchant_feed", "user_token=" . $this->session->data["user_token"] . "&type=feed", true);
        $data["user_token"] = $this->session->data["user_token"];
        $filter_data = array("sort" => "name", "order" => "ASC");
        $data["categories"] = $this->model_catalog_category->getCategories($filter_data);
        if (isset($this->request->post["feed_merchant_feed_category_items"])) {
            $data["feed_merchant_feed_category_items"] = $this->request->post["feed_merchant_feed_category_items"];
        } else {
            if ($this->config->get("feed_merchant_feed_category_items")) {
                $data["feed_merchant_feed_category_items"] = $this->config->get("feed_merchant_feed_category_items");
            } else {
                $data["feed_merchant_feed_category_items"] = array();
            }
        }
        $data["header"] = $this->load->controller("common/header");
        $data["column_left"] = $this->load->controller("common/column_left");
        $data["footer"] = $this->load->controller("common/footer");
        $this->response->setOutput($this->load->view("extension/feed/merchant_feed_category", $data));
    }
    public function activate()
    {
        $this->load->language("extension/feed/merchant_feed");
        if (isset($this->request->server["HTTPS"]) && ($this->request->server["HTTPS"] == "on" || $this->request->server["HTTPS"] == "1")) {
            $server = str_replace("www.", "", HTTPS_CATALOG);
        } else {
            $server = str_replace("www.", "", HTTP_CATALOG);
        }
        $json = array();
        if (isset($this->request->post["key"]) && !empty($this->request->post["key"])) {
            $match = "";
            $key = preg_match("#(?<=\\/\\/).+?(?=\\/)#", $server, $match);
            if (md5($match[0] . "gmerchant") != $this->request->post["key"]) {
                $json["error"] = $this->language->get("error_key");
            }
        } else {
            $json["error"] = $this->language->get("error_key");
        }
        if (!$json) {
            $json["success"] = $this->language->get("text_key_success");
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    protected function validate()
    {
        if (isset($this->request->server["HTTPS"]) && ($this->request->server["HTTPS"] == "on" || $this->request->server["HTTPS"] == "1")) {
            $server = str_replace("www.", "", HTTPS_CATALOG);
        } else {
            $server = str_replace("www.", "", HTTP_CATALOG);
        }
        if (!$this->user->hasPermission("modify", "extension/feed/merchant_feed")) {
            $this->error["warning"] = $this->language->get("error_permission");
        }
        return !$this->error;
    }
    public function install()
    {
        $this->load->model("extension/feed/merchant_feed");
        $this->model_extension_feed_merchant_feed->install();
    }
    public function uninstall()
    {
        $this->load->model("extension/feed/merchant_feed");
        $this->model_extension_feed_merchant_feed->uninstall();
    }
    public function autocomplete()
    {
        $json = array();
        if (isset($this->request->get["filter_text"])) {
            $this->load->model("extension/feed/merchant_feed");
            if (isset($this->request->get["filter_text"])) {
                $filter_text = $this->request->get["filter_text"];
            } else {
                $filter_text = "";
            }
            if (isset($this->request->get["limit"])) {
                $limit = $this->request->get["limit"];
            } else {
                $limit = 15;
            }
            $filter_data = array("filter_text" => $filter_text, "start" => 0, "limit" => $limit);
            $results = $this->model_extension_feed_merchant_feed->getGoogleCategory($filter_data);
            foreach ($results as $result) {
                $json[] = array("google_id" => $result["google_id"], "text" => strip_tags(html_entity_decode($result["text"], ENT_QUOTES, "UTF-8")));
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
}

?>