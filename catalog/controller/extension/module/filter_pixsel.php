<?php
class ControllerExtensionModuleFilterPixsel extends Controller {

    private const IMG_W_SMALL = 160;
    private const IMG_H_SMALL = 90;

    private const IMG_W_BIG   = 320;
    private const IMG_H_BIG   = 219;

    public function index() {
        $this->load->language('extension/module/filter_pixsel');

        $this->load->model('tool/image');
        $this->load->model('catalog/category');

        $data = array();

        $data['bg_mask'] = $this->config->get('t1_category_mask_toggle');

        $detect_mb = new Mobile_Detect();
        $data['mobile'] = $detect_mb->isMobile() ? 1 : 0;

		$lang = $this->language->get('code');
		if ($lang == 'en') {
			$data['lang'] = 'eng';
		} else {
			$data['lang'] = $lang;
		}

        $host = $this->request->server['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
        $data['lite'] = (strpos($host, 'catalog.') !== false || strpos($host, 'price.') !== false) ? 1 : 0;

        $route = $this->request->get['route'] ?? '';
        $data['ishome'] = (!$route || $route === 'common/home') ? 1 : 0;

        $scheme = (!empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $data['server'] = $scheme . $host;

        $parts = array();
        if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
        }

        $url_tail = '';
        if (isset($this->request->get['sort']))  $url_tail .= '&sort='  . $this->request->get['sort'];
        if (isset($this->request->get['order'])) $url_tail .= '&order=' . $this->request->get['order'];
        if (isset($this->request->get['limit'])) $url_tail .= '&limit=' . $this->request->get['limit'];

        // filter selected
        if (isset($this->request->get['filter'])) {
            $data['filter_category'] = explode(',', $this->request->get['filter']);
        } else {
            $data['filter_category'] = array();
        }

        // HOME / no path -> show top categories grid
        if (count($parts) === 0) {
            $data['marks'] = $this->getTopCategoriesWithThumb(self::IMG_W_BIG, self::IMG_H_BIG);

            if ($data['lite'] <= 0) {
                return $this->load->view('extension/module/filter_home_pixsel', $data);
            }
            return $this->load->view('extension/module/filter_listing_pixsel', $data);
        }

        // If path exists:
        $category_id = (int)end($parts);
        $category_info = $this->model_catalog_category->getCategory($category_id);
        if (!$category_info) {
            return ''; // ����, �� ������
        }

        // action url
        if (isset($this->request->get['path'])) {
            $data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url_tail));
        } else {
            $data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', $url_tail));
        }

        // ---------------------------
        // CASE: 1 or 2 levels
        // parts = [mark] or [mark, model]
        // ---------------------------
        if (count($parts) === 1 || count($parts) === 2) {

            // For 2 levels: top is parent, sub is current
            if (count($parts) === 2) {
                $sub_info = $category_info;
                $parent_info = $this->model_catalog_category->getCategory((int)$sub_info['parent_id']);
                if ($parent_info) {
                    $category_info = $parent_info;
                    $data['subcategory_id'] = (int)$sub_info['category_id'];
                }
                $data['seccat'] = 1;
            } else {
                $data['seccat'] = 0;
            }

            // Selected ids
            $data['category_id'] = (int)$category_info['category_id'];

            // Top category media
            $top_media = $this->buildCategoryMedia($category_info, self::IMG_W_SMALL, self::IMG_H_SMALL);

            $data['category_thumb'] = $top_media['thumb'];
            $data['category_thumb_top'] = $top_media['thumb']; // � ���� ���� ���� ���� � �� ����� ��������
            $data['category_img_width'] = self::IMG_W_SMALL;
            $data['category_img_height'] = self::IMG_H_SMALL;
            $data['category_thumb_holder'] = $this->model_tool_image->resize('catalog/frametheme/src_holder.png', 50, 50);
            $data['category_svg'] = html_entity_decode($category_info['svg'] ?? '', ENT_QUOTES, 'UTF-8');

            // Marks (top categories list) � ��� �������� ��� (�� ���� � ������� ����)
            $data['marks'] = $this->getTopCategoriesSimple();

            // Second categories under top
            $data['models'] = $this->getChildrenSimple((int)$category_info['category_id']);

            // Third categories if 2 levels (children of subcategory)
            if (count($parts) === 2 && !empty($sub_info)) {
                $data['years'] = $this->getChildrenRich((int)$sub_info['category_id'], self::IMG_W_SMALL, self::IMG_H_SMALL);
            }

            return $this->load->view('extension/module/filter_pixsel', $data);
        }

        // ---------------------------
        // CASE: 3 levels (mark/model/year)
        // ---------------------------
        if (count($parts) === 3) {
            $data['category_id'] = (int)$category_info['category_id'];

            $top_media = $this->buildCategoryMedia($category_info, self::IMG_W_SMALL, self::IMG_H_SMALL);

            $data['category_thumb'] = $top_media['thumb'];
            $data['category_img_width'] = self::IMG_W_SMALL;
            $data['category_img_height'] = self::IMG_H_SMALL;
            $data['category_thumb_holder'] = $this->model_tool_image->resize('catalog/frametheme/src_holder.png', 50, 50);
            $data['category_svg'] = html_entity_decode($category_info['svg'] ?? '', ENT_QUOTES, 'UTF-8');

            $data['marks'] = $this->getTopCategoriesWithThumb(self::IMG_W_SMALL, self::IMG_H_SMALL);

            // Second categories under mark (parts[0])
            $data['models'] = $this->getChildrenSimple((int)$parts[0]);

            // Third categories under model (parts[1])
            $data['years']  = $this->getChildrenRich((int)$parts[1], self::IMG_W_SMALL, self::IMG_H_SMALL);

            // Selected values
            $mark_id  = (int)$parts[0];
            $model_id = (int)$parts[1];
            $year_id  = (int)$parts[2];

            $mark_info  = $this->model_catalog_category->getCategory($mark_id);
            $model_info = $this->model_catalog_category->getCategory($model_id);
            $year_info  = $this->model_catalog_category->getCategory($year_id);

            $data['mark_id']   = $mark_id;
            $data['mark_name'] = $mark_info['name'] ?? '';
            $data['mark_img']  = $this->buildCategoryMedia($mark_info, self::IMG_W_SMALL, self::IMG_H_SMALL)['thumb'];

            $data['mark_svg']  = html_entity_decode($mark_info['svg'] ?? '', ENT_QUOTES, 'UTF-8');

            $data['model_id']   = $model_id;
            $data['model_name'] = $model_info['name'] ?? '';

            $data['year_id']   = $year_id;
            $data['year_name'] = $year_info['name'] ?? '';

            return $this->load->view('extension/module/filter_listing_pixsel', $data);
        }

        return '';
    }

    // -----------------------------
    // AJAX: getSubcat
    // -----------------------------
    public function getSubcat() {
        $this->load->model('catalog/category');
        $this->load->model('tool/image');

        $category_id = isset($this->request->post['category_id']) ? (int)$this->request->post['category_id'] : 0;
        if (!$category_id) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(array()));
            return;
        }

        $items = $this->getChildrenRich($category_id, self::IMG_W_BIG, self::IMG_H_BIG);

        // empty path ?
        foreach ($items as &$it) {
            $it['href'] = $this->url->link('product/category', 'path=');
        }
        unset($it);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($items));
    }

    // -----------------------------
    // AJAX: getSvg
    // -----------------------------
    public function getSvg() {
        $this->load->model('catalog/category');

        $category_id = isset($this->request->post['category_id']) ? (int)$this->request->post['category_id'] : 0;
        $catinfo = $category_id ? $this->model_catalog_category->getCategory($category_id) : null;

        $svg = $catinfo ? html_entity_decode($catinfo['svg'] ?? '', ENT_QUOTES, 'UTF-8') : '';

        $this->response->addHeader('Content-Type: text/plain; charset=utf-8');
        $this->response->setOutput($svg);
    }

    // -----------------------------
    // Helpers
    // -----------------------------
    private function buildCategoryMedia($category_info, $w, $h) {
        $img = !empty($category_info['image']) ? $category_info['image'] : 'placeholder.png';
        return array(
            'thumb' => $this->model_tool_image->resizeWc($img, (int)$w, (int)$h)
        );
    }

    private function getTopCategoriesSimple() {
        $all = $this->model_catalog_category->getCategories();
        $out = array();

        foreach ($all as $row) {
            $out[] = array(
                'category_id' => (int)$row['category_id'],
                'name'        => $row['name'],
                'href'        => $this->url->link('product/category', 'path=' . (int)$row['category_id']),
            );
        }
        return $out;
    }

    private function getTopCategoriesWithThumb($w, $h) {
        $all = $this->model_catalog_category->getCategories();
        $out = array();

        foreach ($all as $row) {
            $thumb = $this->buildCategoryMedia($row, $w, $h)['thumb'];

            $out[] = array(
                'category_id'     => (int)$row['category_id'],
                'name'            => $row['name'],
                'href'            => $this->url->link('product/category', 'path=' . (int)$row['category_id']),
                'category_thumb'  => $thumb,
                'category_svg'    => html_entity_decode($row['svg'] ?? '', ENT_QUOTES, 'UTF-8'),
            );
        }
        return $out;
    }

    private function getChildrenSimple($parent_id) {
        $rows = $this->model_catalog_category->getCategories((int)$parent_id);
        $out = array();

        foreach ($rows as $row) {
            $out[] = array(
                'category_id' => (int)$row['category_id'],
                'name'        => $row['name'],
            );
        }
        return $out;
    }

    private function getChildrenRich($parent_id, $w, $h) {
        $rows = $this->model_catalog_category->getCategories((int)$parent_id);
        $out = array();

        foreach ($rows as $row) {
            $thumb = $this->buildCategoryMedia($row, $w, $h)['thumb'];

            $out[] = array(
                'category_id'          => (int)$row['category_id'],
                'name'                 => $row['name'],
                'category_thumb'       => $thumb,
                'category_img_width'   => (int)$w,
                'category_img_height'  => (int)$h,
                'category_thumb_holder'=> $this->model_tool_image->resize('catalog/frametheme/src_holder.png', 50, 50),
                'category_svg'         => html_entity_decode($row['svg'] ?? '', ENT_QUOTES, 'UTF-8'),
            );
        }
        return $out;
    }
}
