<?php
class ControllerExtensionModuleXDSCategoryWall extends Controller {

    public function index($setting) {
        static $module = 0;

        // Models / Languages
        $this->load->model('localisation/language');
        $this->load->language('extension/module/xds_category_wall');
        $this->load->language('extension/module/frametheme/ft_global');

        $this->load->model('tool/image');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('setting/setting');

        $language_id = (int)$this->config->get('config_language_id');
        $store_id    = (int)$this->config->get('config_store_id');

        // Theme settings
        $ft_settings = $this->model_setting_setting->getSetting('theme_frame', $store_id);

        $data = array();
        $data['lazyload_imgs'] = !empty($ft_settings['t1_catalog_page_lazy']);

        // Assets for carousel (type==0)
        $type = isset($setting['type']) ? (int)$setting['type'] : 0;
        if ($type === 0) {
            $theme_dir = $this->config->get('theme_frame_directory');
            $this->document->addStyle('catalog/view/theme/' . $theme_dir . '/javascript/owl-carousel/owl.carousel.min.css');
            $this->document->addScript('catalog/view/theme/' . $theme_dir . '/javascript/owl-carousel/owl.carousel.min.js');
        }

        // Settings -> view data
        $data['type']           = $type;
        $data['click_action']   = $setting['click_action'] ?? '';
        $data['heading_title']  = !empty($setting['title'][$language_id]) ? $setting['title'][$language_id] : '';
        $data['allcat']         = !empty($setting['allcat']);
        $data['catline']        = $setting['catline'] ?? '';
        $data['cssclass']       = $setting['cssclass'] ?? '';
        $data['controls']       = $setting['controls'] ?? array();
        $data['autoplay']       = !empty($setting['autoplay']);
        $data['autoplay_speed'] = isset($setting['autoplay_speed']) ? (int)$setting['autoplay_speed'] : 5000;
        $data['items']          = isset($setting['items']) ? (int)$setting['items'] : 1;

        // Responsive items
        $data['responsive_items'] = array();
        $responsive_items = $setting['responsive_items'] ?? array();
        foreach ($responsive_items as $item) {
            if (!empty($item['breakpoint']) && !empty($item['amount'])) {
                $data['responsive_items'][] = array(
                    'breakpoint' => (int)$item['breakpoint'],
                    'amount'     => (int)$item['amount']
                );
            }
        }

        // Current category from URL
        $parts = array();
        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
        }
        $data['category_id'] = isset($parts[0]) ? (int)$parts[0] : 0;
        $data['child_id']    = isset($parts[1]) ? (int)$parts[1] : 0;

        // Image sizes (as-is)
        $image_width  = isset($setting['width'])  ? (int)$setting['width']  : 200;
        $image_height = isset($setting['height']) ? (int)$setting['height'] : 200;

        // -------------------------
        // CACHE (your requested key)
        // -------------------------
        $cache_key = 'xds_category_wall.' . $store_id . '_' . $language_id . '_' . (int)$data['allcat'];

        $cached_categories = $this->cache->get($cache_key);

        if (is_array($cached_categories) && $cached_categories) {
            $data['categories'] = $cached_categories;
        } else {
            // Build categories list
            $categories = array();

            if (!empty($setting['allcat'])) {
                // All categories (as in original logic)
                $categories = $this->model_catalog_category->getCategories();

                // Якщо колись захочеш ліміт для allcat — додай окремий setting, НЕ catline.
                // Наприклад: $setting['limit_allcat']
                if (!empty($setting['limit_allcat'])) {
                    $categories = array_slice($categories, 0, (int)$setting['limit_allcat']);
                }
            } else {
                // Selected categories
                $categories_list = $setting['category'] ?? array();

                foreach ($categories_list as $item) {
                    // support both formats: [12,15] or [['category_id'=>12], ...]
                    $category_id = is_array($item) ? (int)($item['category_id'] ?? 0) : (int)$item;

                    if ($category_id) {
                        $category_info = $this->model_catalog_category->getCategory($category_id);
                        if ($category_info) {
                            $categories[] = $category_info;
                        }
                    }
                }
            }

            // Prepare output array (do NOT cache "active")
            $data['categories'] = array();

            foreach ($categories as $category) {
                if (!empty($category['image'])) {
                    $thumb = $this->model_tool_image->resizeWc($category['image'], $image_width, $image_height);
                } else {
                    $thumb = $this->model_tool_image->resize('placeholder.png', $image_width, $image_height);
                }

                $thumb_holder = $this->model_tool_image->resize('catalog/frametheme/src_holder.png', $image_width, $image_height);

                $data['categories'][] = array(
                    'category_id'  => (int)$category['category_id'],
                    'thumb'        => $thumb,
                    'img_width'    => $image_width . 'px',
                    'img_height'   => $image_height . 'px',
                    'thumb_holder' => $thumb_holder,
                    'svg'          => html_entity_decode($category['svg'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'active'       => false, // важливо: active НЕ кешуємо
                    'name'         => $category['name'],
                    'href'         => $this->url->link('product/category', 'path=' . (int)$category['category_id'])
                );
            }

            $this->cache->set($cache_key, $data['categories']);
        }

        // active depends on current page path => set after cache
        foreach ($data['categories'] as &$c) {
            $c['active'] = ((int)$c['category_id'] === (int)$data['category_id']);
        }
        unset($c);

        $data['module'] = $module++;

        if (!empty($data['categories'])) {
            return $this->load->view('extension/module/xds_category_wall', $data);
        }

        return '';
    }
}
