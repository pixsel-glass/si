<?php
/**
 * 2018 Paysera
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@paysera.com so we can send you a copy immediately.
 *
 *  @author    Paysera <plugins@paysera.com>
 *  @copyright 2018 Paysera
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Paysera
 */

require_once DIR_SYSTEM . 'library/paysera/vendor/autoload.php';

/**
 * Class ControllerExtensionPaymentPaysera
 */
class ControllerExtensionPaymentPaysera extends Controller
{
    /**
     * Default currency
     */
    const PAYSERA_CURRENCY = 'EUR';

    /**
     * Default language code
     */
    const PAYSERA_DEFAULT_LANG = 'en';

    /**
     * Empty value
     */
    const PAYSERA_EMPTY_VALUE = '';

    /**
     * Default method
     */
    const REQUEST_METHOD_TYPE = 'POST';

    /**
     * Default project id
     */
    const PAYSERA_DEFAULT_PROJECT_ID = 1;

    /**
     * Paysera payment module name
     */
    const PAYSERA_PAYMENT = 'payment_paysera';

    /**
     * Success text
     */
    const PAYSERA_SUCCESS = 'text_success';

    /**
     * Paysera extension function
     */
    const PAYSERA_EXTENSION_PAYMENT = 'extension/payment/paysera';

    /**
     * Marketplace extension location
     */
    const PAYSERA_MARKETPLACE_EXTENSIONS = 'marketplace/extension';

    /**
     * Dashboard
     */
    const PAYSERA_COMMON_DASHBOARD = 'common/dashboard';

    /**
     * Prefix used with an error code
     */
    const PAYSERA_ERROR_PREFIX = 'error_';

    /**
     * Callback url
     */
    const PAYSERA_CALLBACK_URL = 'index.php?route=extension/payment/paysera/callback';

    /**
     * Token param
     */
    const PAYSERA_TOKEN_PARAM = 'user_token=';

    /**
     * Token value
     */
    const PAYSERA_TYPE_PAYMENT = '&type=payment';

    /**
     * Paysera header hook controller
     */
    const PAYSERA_HEADER_CONTROLER = 'extension/payment/paysera/paysera_header';

    /**
     * Paysera footer hook controller
     */
    const PAYSERA_FOOTER_CONTROLER = 'extension/payment/paysera/paysera_footer';

    /**
     * Paysera header hook event
     */
    const PAYSERA_EVENT_HEADER = 'catalog/view/common/header/before';

    /**
     * Paysera footer hook event
     */
    const PAYSERA_EVENT_FOOTER = 'catalog/view/common/footer/after';

    /**
     * Paysera header event name
     */
    const PAYSERA_EVENT_HEADER_NAME = 'paysera_header';

    /**
     * Paysera footer event name
     */
    const PAYSERA_EVENT_FOOTER_NAME = 'paysera_footer';


    /**
     * @var string
     */
    private $projectID;

    /**
     * @var array
     */
    private $error = array();

    /**
     * @var array
     */
    private $errorFieldName = array(
        'warning',
        'project',
        'sign'
    );

    /**
     * @var array
     */
    private $breadcrumbFields = array(
        'text_home'      => 'common/dashboard',
        'text_extension' => 'marketplace/extension',
        'heading_title'  => 'extension/payment/paysera'
    );

    /**
     * @var array
     */
    private $payseraFieldsName = array(
        'payment_paysera_status',
        'payment_paysera_project',
        'payment_paysera_sign',
        'payment_paysera_test',
        'payment_paysera_total',
        'payment_paysera_title',
        'payment_paysera_description',
        'payment_paysera_display_payments_list',
        'payment_paysera_category',
        'paysera_selected_countries',
        'paysera_countries',
        'payment_paysera_grid_view',
        'payment_paysera_buyer_consent',
        'payment_paysera_default_country',
        'payment_paysera_geo_zone_id',
        'payment_paysera_sort_order',
        'payment_paysera_new_order_status_id',
        'payment_paysera_paid_status_id',
        'payment_paysera_pending_status_id',
        'payment_paysera_quality',
        'payment_paysera_owner',
        'payment_paysera_owner_code'
    );

    public function index()
    {
        $this->load->language('extension/payment/paysera');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if ($this->request->server['REQUEST_METHOD'] == $this::REQUEST_METHOD_TYPE
            && $this->validate()) {
            $this->model_setting_setting->editSetting(
                $this::PAYSERA_PAYMENT,
                $this->request->post
            );

            $this->session->data['success'] = $this->generateData(
                $this::PAYSERA_SUCCESS,
                $this::PAYSERA_EMPTY_VALUE
            );

            $this->response->redirect($this->generateData(
                $this::PAYSERA_EMPTY_VALUE,
                $this::PAYSERA_MARKETPLACE_EXTENSIONS
            ));
        }

        foreach ($this->getErrorFieldName() as $fieldName) {
            $dataName = $this::PAYSERA_ERROR_PREFIX . $fieldName;
            $data[$dataName] = $this->errorValue($fieldName);
        }

        foreach ($this->getBreadcrumbFields() as $key => $value) {
            $data['breadcrumbs'][] = $this->generateData($key, $value);
        }

        $data['action'] = $this->generateData(
            $this::PAYSERA_EMPTY_VALUE,
            $this::PAYSERA_EXTENSION_PAYMENT
        );
        $data['cancel'] = $this->generateData(
            $this::PAYSERA_EMPTY_VALUE,
            $this::PAYSERA_MARKETPLACE_EXTENSIONS
        );
        $data['callback'] = HTTP_CATALOG . $this::PAYSERA_CALLBACK_URL;

        foreach ($this->getPayseraFieldsName() as $fieldName) {
            $data[$fieldName] = $this->generateConfigField($fieldName);
        }

        $this->validateProject($this->config->get('payment_paysera_project'));

        $countries = $this->getCountries();

        if (count($countries) > 0) {
            $data['paysera_countries'] = $countries;

            if (is_array($data['payment_paysera_category'])) {
                $data['paysera_selected_countries'] = [];

                foreach ($data['payment_paysera_category'] as $isoCode) {
                    $data['paysera_selected_countries'][$isoCode] = $data['paysera_countries'][$isoCode];
                }
            }
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->document->addStyle('view/stylesheet/paysera/backoffice.css');
        $this->document->addScript('view/javascript/paysera/backoffice.js');

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this::PAYSERA_EXTENSION_PAYMENT, $data));
    }

    /**
     * @return bool
     */
    protected function validate()
    {
        if (!$this->user->hasPermission('modify', $this::PAYSERA_EXTENSION_PAYMENT)) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        if (!$this->request->post['payment_paysera_project']) {
            $this->error['project'] = $this->language->get('error_project');
        }

        if (!$this->request->post['payment_paysera_sign']) {
            $this->error['sign'] = $this->language->get('error_sign');
        }

        return !$this->error;
    }


    /**
     * @return array
     */
    private function getCountries()
    {
        $countries = [];

        $this->load->model('localisation/country');

        foreach ($this->model_localisation_country->getCountries() as $country) {
            $countries[strtolower($country['iso_code_2'])] = $country['name'];
        }

        return $countries;
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    private function errorValue($fieldName)
    {
        if (isset($this->error[$fieldName])) {
            $data = $this->error[$fieldName];
        } else {
            $data = $this::PAYSERA_EMPTY_VALUE;
        }

        return $data;
    }

    /**
     * @param string $text
     * @param string $path
     *
     * @return array
     */
    private function generateData($text, $path)
    {
        if ($path == $this::PAYSERA_MARKETPLACE_EXTENSIONS) {
            $tokenParam = $this::PAYSERA_EMPTY_VALUE;
        } else {
            $tokenParam = $this::PAYSERA_TYPE_PAYMENT;
        }
        $token = $this::PAYSERA_TOKEN_PARAM . $this->session->data['user_token'] . $tokenParam;

        if (empty($text)) {
            $data = $this->url->link($path, $token, true);
        } elseif (empty($path)) {
            $data = $this->language->get($text);
        } else {
            $data = array(
                'text' => $this->language->get($text),
                'href' => $this->url->link($path, $token, true)
            );
        }

        return $data;
    }

    /**
     * @param string $fieldName
     *
     * @return mixed
     */
    private function generateConfigField($fieldName)
    {
        if (isset($this->request->post[$fieldName])) {
            $data = $this->request->post[$fieldName];
        } else {
            $data = $this->config->get($fieldName);
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getErrorFieldName()
    {
        return $this->errorFieldName;
    }

    /**
     * @return array
     */
    private function getPayseraFieldsName()
    {
        return $this->payseraFieldsName;
    }

    /**
     * @param string $projectID
     */
    private function validateProject($projectID)
    {
        if (empty($projectID)) {
            $result = $this::PAYSERA_DEFAULT_PROJECT_ID;
        } else {
            $result = $projectID;
        }

        $this->setProjectID($result);
    }

    /**
     * @return int
     */
    private function getProjectID()
    {
        return $this->projectID;
    }

    /**
     * @param int $projectID
     */
    private function setProjectID($projectID)
    {
        $this->projectID = $projectID;
    }

    /**
     * @return array
     */
    public function getBreadcrumbFields()
    {
        return $this->breadcrumbFields;
    }

    public function install() {
        $this->load->model('setting/event');
        $this->model_setting_event->addEvent(
            $this::PAYSERA_EVENT_HEADER_NAME,
            $this::PAYSERA_EVENT_HEADER,
            $this::PAYSERA_HEADER_CONTROLER)
        ;
        $this->model_setting_event->addEvent(
            $this::PAYSERA_EVENT_FOOTER_NAME,
            $this::PAYSERA_EVENT_FOOTER,
            $this::PAYSERA_FOOTER_CONTROLER)
        ;
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting(
            $this::PAYSERA_PAYMENT,
            [
                'payment_paysera_display_payments_list' => true,
            ]
        );
    }

    public function uninstall() {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode($this::PAYSERA_EVENT_HEADER_NAME);
        $this->model_setting_event->deleteEventByCode($this::PAYSERA_EVENT_FOOTER_NAME);
    }
}
