<?php

class ControllerExtensionShippingInpostShipping extends Controller
{
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        if (!empty($this->config->get('shipping_inpost_shipping_sandbox_mode'))) {
            $apiKey = $this->config->get('shipping_inpost_shipping_sandbox_api_key');
            $idOrganization = $this->config->get('shipping_inpost_shipping_sandbox_organization_id');
            $geoWidgetKey = $this->config->get('shipping_inpost_shipping_geowidget_sandbox_api_key');
        } else {
            $apiKey = $this->config->get('shipping_inpost_shipping_api_key');
            $idOrganization = $this->config->get('shipping_inpost_shipping_organization_id');
            $geoWidgetKey = $this->config->get('shipping_inpost_shipping_geowidget_api_key');
        }

        $this->load->language('extension/shipping/inpost_shipping');
        $this->load->library('inpostShipping');

        $this->load->model('extension/shipping/inpost_shipping');
        $this->model_extension_shipping_inpost_shipping->checkDatabases();

        $this->inpostShipping->initialize([
            'sandboxMode' => $this->config->get('shipping_inpost_shipping_sandbox_mode'),
            'apiKey' => $apiKey,
            'idOrganization' => $idOrganization,
            'geoWidgetKey' => $geoWidgetKey,
        ]);
    }

    public function index()
    {
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        $this->document->addStyle('view/javascript/inpost_shipping/switchery.min.css');
        $this->document->addScript('view/javascript/inpost_shipping/switchery.min.js');
        $this->document->addStyle('view/javascript/inpost_shipping/inpost.css');
        $this->document->addScript('view/javascript/inpost_shipping/inpost.js');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('shipping_inpost_shipping', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/shipping/inpost_shipping', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = 'Settings saved.';
            unset($this->session->data['success']);
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/inpost_shipping', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/shipping/inpost_shipping', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

        if (isset($this->request->post['shipping_inpost_shipping_status'])) {
            $data['shipping_inpost_shipping_status'] = $this->request->post['shipping_inpost_shipping_status'];
        } else {
            $data['shipping_inpost_shipping_status'] = $this->config->get('shipping_inpost_shipping_status');
        }

        if (isset($this->request->post['shipping_inpost_shipping_sandbox_mode'])) {
            $data['shipping_inpost_shipping_sandbox_mode'] = $this->request->post['shipping_inpost_shipping_sandbox_mode'];
        } else {
            $data['shipping_inpost_shipping_sandbox_mode'] = $this->config->get('shipping_inpost_shipping_sandbox_mode');
        }

        if (isset($this->request->post['shipping_inpost_shipping_api_key'])) {
            $data['shipping_inpost_shipping_api_key'] = $this->request->post['shipping_inpost_shipping_api_key'];
        } else {
            $data['shipping_inpost_shipping_api_key'] = $this->config->get('shipping_inpost_shipping_api_key');
        }

        if (isset($this->request->post['shipping_inpost_shipping_organization_id'])) {
            $data['shipping_inpost_shipping_organization_id'] = $this->request->post['shipping_inpost_shipping_organization_id'];
        } else {
            $data['shipping_inpost_shipping_organization_id'] = $this->config->get('shipping_inpost_shipping_organization_id');
        }

        if (isset($this->request->post['shipping_inpost_shipping_sandbox_api_key'])) {
            $data['shipping_inpost_shipping_sandbox_api_key'] = $this->request->post['shipping_inpost_shipping_sandbox_api_key'];
        } else {
            $data['shipping_inpost_shipping_sandbox_api_key'] = $this->config->get('shipping_inpost_shipping_sandbox_api_key');
        }

        if (isset($this->request->post['shipping_inpost_shipping_sandbox_organization_id'])) {
            $data['shipping_inpost_shipping_sandbox_organization_id'] = $this->request->post['shipping_inpost_shipping_sandbox_organization_id'];
        } else {
            $data['shipping_inpost_shipping_sandbox_organization_id'] = $this->config->get('shipping_inpost_shipping_sandbox_organization_id');
        }

        if (isset($this->request->post['shipping_inpost_shipping_cost'])) {
            $data['shipping_inpost_shipping_cost'] = $this->request->post['shipping_inpost_shipping_cost'];
        } else {
            $data['shipping_inpost_shipping_cost'] = $this->config->get('shipping_inpost_shipping_cost');
        }

        if (isset($this->request->post['shipping_inpost_shipping_tax_class_id'])) {
            $data['shipping_inpost_shipping_tax_class_id'] = $this->request->post['shipping_inpost_shipping_tax_class_id'];
        } else {
            $data['shipping_inpost_shipping_tax_class_id'] = $this->config->get('shipping_inpost_shipping_tax_class_id');
        }

        $this->load->model('localisation/tax_class');

        $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        if (isset($this->request->post['shipping_inpost_shipping_geo_zone_id'])) {
            $data['shipping_inpost_shipping_geo_zone_id'] = $this->request->post['shipping_inpost_shipping_geo_zone_id'];
        } else {
            $data['shipping_inpost_shipping_geo_zone_id'] = $this->config->get('shipping_inpost_shipping_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['shipping_inpost_shipping_sort_order'])) {
            $data['shipping_inpost_shipping_sort_order'] = $this->request->post['shipping_inpost_shipping_sort_order'];
        } else {
            $data['shipping_inpost_shipping_sort_order'] = $this->config->get('shipping_inpost_shipping_sort_order');
        }

        if (isset($this->request->post['shipping_inpost_shipping_geowidget_api_key'])) {
            $data['shipping_inpost_shipping_geowidget_api_key'] = $this->request->post['shipping_inpost_shipping_geowidget_api_key'];
        } else {
            $data['shipping_inpost_shipping_geowidget_api_key'] = $this->config->get('shipping_inpost_shipping_geowidget_api_key');
        }

        if (isset($this->request->post['shipping_inpost_shipping_geowidget_sandbox_api_key'])) {
            $data['shipping_inpost_shipping_geowidget_sandbox_api_key'] = $this->request->post['shipping_inpost_shipping_geowidget_sandbox_api_key'];
        } else {
            $data['shipping_inpost_shipping_geowidget_sandbox_api_key'] = $this->config->get('shipping_inpost_shipping_geowidget_sandbox_api_key');
        }

        if (isset($this->request->post['shipping_inpost_shipping_geowidget_sandbox_api_key'])) {
            $data['shipping_inpost_shipping_geowidget_sandbox_api_key'] = $this->request->post['shipping_inpost_shipping_geowidget_sandbox_api_key'];
        } else {
            $data['shipping_inpost_shipping_geowidget_sandbox_api_key'] = $this->config->get('shipping_inpost_shipping_geowidget_sandbox_api_key');
        }

        if (isset($this->request->post['shipping_inpost_shipping_geowidget_sandbox_checkout'])) {
            $data['shipping_inpost_shipping_geowidget_sandbox_checkout'] = $this->request->post['shipping_inpost_shipping_geowidget_sandbox_checkout'];
        } else {
            $data['shipping_inpost_shipping_geowidget_sandbox_checkout'] = $this->config->get('shipping_inpost_shipping_geowidget_sandbox_checkout');
        }

        if (isset($this->request->post['shipping_inpost_shipping_settings'])) {
            $data['shipping_inpost_shipping_settings'] = $this->request->post['shipping_inpost_shipping_settings'];
        } elseif (!empty($this->config->get('shipping_inpost_shipping_settings'))) {
            $data['shipping_inpost_shipping_settings'] = $this->config->get('shipping_inpost_shipping_settings');
        } else {
            $data['shipping_inpost_shipping_settings'] = [];
        }

        $pickupPointsView = $this->getPickupPointsView();
        if (!empty($pickupPointsView)) {
            $data['shipping_inpost_shipping_settings']['pickupPoints'] = $pickupPointsView;
        }

        $shippingMethodsView = $this->getShippingMethodsView();
        if (!empty($shippingMethodsView)) {
            $data['shipping_inpost_shipping_settings']['shippingMethods'] = $shippingMethodsView;
        }

        $deliveryServicesView = $this->getDeliveryServicesView();
        if (!empty($deliveryServicesView)) {
            $data['shipping_inpost_shipping_settings']['deliveryServices'] = $deliveryServicesView;
        }

        $this->load->model('localisation/tax_rate');
        $data['tax_rates'] = $this->model_localisation_tax_rate->getTaxRates();

        $this->load->model('customer/customer_group');
        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['user_token'] = $this->session->data['user_token'];

        $services = $this->inpostShipping->getServices();
        $shippingMethods = $this->inpostShipping->getShippingMethods();
        $pickupPoints = $this->model_extension_shipping_inpost_shipping->getPickupPoints();

        $data['apiDeliveryServices'] = $services;
        $data['apiDeliveryServicesJson'] = json_encode($services);
        $data['apiShippingMethods'] = $shippingMethods;
        $data['apiShippingMethodsJson'] = json_encode($shippingMethods);
        $data['pickupPoints'] = $pickupPoints;

        $data['geoWidgetAssets'] = $this->inpostShipping->getGeoWidgetAssets();

        $this->response->setOutput($this->load->view('extension/shipping/inpost_shipping', $data));
    }

    public function loadOrganizationDetails()
    {
        $orgData = $this->inpostShipping->getOrganizationDetails();

        $data = [];
        $data['company'] = $orgData['name'];
        $data['firstname'] = !empty($orgData['contact_person']['first_name']) ? $orgData['contact_person']['first_name'] : '';
        $data['lastname'] = !empty($orgData['contact_person']['last_name']) ? $orgData['contact_person']['last_name'] : '';
        $data['telephone'] = !empty($orgData['contact_person']['phone']) ? $orgData['contact_person']['phone'] : '';
        $data['email'] = !empty($orgData['contact_person']['email']) ? $orgData['contact_person']['email'] : '';
        $data['city'] = $orgData['invoice_address']['city'];
        $data['postcode'] = $orgData['invoice_address']['post_code'];
        $data['street'] = $orgData['invoice_address']['street'];
        $data['house'] = $orgData['invoice_address']['building_number'];

        $this->sendJsonResponse($data);
    }

    public function savePickupPoint()
    {
        $request = $this->request->post;

        $response = [];
        if (empty($request) || !$this->validatePickupPointForm($request)) {
            $response['error'] = true;
            $response['error_message'] = 'Form Data is not valid. Please, check required fields';
            $this->sendJsonResponse($response);
            return;
        }

        if (!filter_var($request['pickupEmail'], FILTER_VALIDATE_EMAIL)) {
            $response['error'] = true;
            $response['error_message'] = 'Invalid email format.';
        }

        $this->load->model('extension/shipping/inpost_shipping');
        $mappedData = [
            'name' => $request['pickupName'],
            'hours_operation' => $request['pickupOfficeHours'],
            'email' => $request['pickupEmail'],
            'phone' => $request['pickupTelephone'],
            'street' => $request['pickupStreet'],
            'housenumber' => $request['pickupHousenumber'],
            'postcode' => $request['pickupPostcode'],
            'city' => $request['pickupCity'],
        ];
        if ($request['pickupId'] === '0') {
            $response['id'] = $this->model_extension_shipping_inpost_shipping->createPickupPoint($request);
            $response['message'] = 'New pickup point added successfully!';
            $mappedData['id_pickup_point'] = $response['id'];
        } else {
            $response['id'] = $request['pickupId'];
            $this->model_extension_shipping_inpost_shipping->editPickupPoint($request);
            $response['message'] = sprintf('Pickup point (%s) updated successfully!', $request['pickupName']);
            $mappedData['id_pickup_point'] = $request['pickupId'];
        }
        $response['pickupPointHtml'] = $this->load->view('extension/shipping/inpost/pickup_point', $mappedData);
        $response['success'] = true;

        $this->sendJsonResponse($response);
    }

    public function getPickupPointsView()
    {
        $this->load->model('extension/shipping/inpost_shipping');
        $pickupPoints = $this->model_extension_shipping_inpost_shipping->getPickupPoints();

        if (!empty($pickupPoints)) {
            $html = '<div id="pickup-points-wrapper"><table class="table table-bordered">';

            $html .= $this->load->view('extension/shipping/inpost/pickup_point_heading');
            $html .= '<tbody>';
            foreach ($pickupPoints as $pickupPoint) {
                $html .= $this->load->view('extension/shipping/inpost/pickup_point', $pickupPoint);
            }
            $html .= '</tbody>';
            $html .= '</table></div>';

            return $html;
        }

        return '';
    }

    public function retrievePickupPointsView()
    {
        $this->sendJsonResponse([
            'pickupPointsHtml' => $this->getPickupPointsView()
        ]);
    }

    public function getPickupPointsModal()
    {
        if (isset($this->request->get) && !empty($this->request->get['id_pickup_point'])) {
            $this->load->model('extension/shipping/inpost_shipping');
            $pickupPointData = $this->model_extension_shipping_inpost_shipping->getPickupPoint($this->request->get['id_pickup_point']);
            $modalHtml = $this->load->view('extension/shipping/inpost/pickup_point_modal', $pickupPointData);
        } else {
            $modalHtml = $this->load->view('extension/shipping/inpost/pickup_point_modal');
        }

        $this->sendJsonResponse([
            'pickupPointsModal' => $modalHtml,
        ]);
    }

    public function getPickupPointsOptions()
    {
        $this->load->model('extension/shipping/inpost_shipping');
        $points = $this->model_extension_shipping_inpost_shipping->getPickupPoints();
        if (empty($points)) {
            $this->sendJsonResponse([
                'pickupPointsOptions' => '<option value="">---</option>',
            ]);
            return;
        }

        $pointsHtml = '';
        foreach ($points as $point) {
            $selected = '';
            if ($point['id_pickup_point'] === $this->config->get('shipping_inpost_shipping_settings')['default_pickup_point']) {
                $selected = 'selected="selected"';
            }
            $pointsHtml .= '<option value="'.$point['id_pickup_point'].'" '.$selected.'>'.$point['name'].'</option>';
        }

        $this->sendJsonResponse([
            'pickupPointsOptions' => $pointsHtml,
        ]);
    }

    public function deletePickupPoint()
    {
        $id = $this->request->post['id_pickup_point'];

        if (empty($id) || (int)$id === 0) {
            $this->sendJsonResponse([
                'error' => true,
                'error_message' => 'Bad request.',
            ]);
            return;
        }

        $this->load->model('extension/shipping/inpost_shipping');
        $this->model_extension_shipping_inpost_shipping->deletePickupPoint($id);

        $this->sendJsonResponse([
            'success' => true,
            'message' => "Pickup point successfully deleted!",
        ]);
    }

    public function syncPickupPoints()
    {
        $response = $this->inpostShipping->syncPickupPoints($this);

        $this->sendJsonResponse($response);
    }

    public function getShippingMethodModal()
    {
        $data = [];
        $this->load->model('localisation/tax_class');
        $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/tax_rate');
        $data['tax_rates'] = $this->model_localisation_tax_rate->getTaxRates();

        $this->load->model('customer/customer_group');
        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        $this->load->model('extension/shipping/inpost_shipping');
        $deliveryServices = $this->model_extension_shipping_inpost_shipping->getDeliveryServices();
        $apiDeliveryServices = $this->inpostShipping->getServices();

        foreach ($deliveryServices as $key => $deliveryService) {
            $deliveryServices[$key]['deliveryServiceName'] = $apiDeliveryServices[$deliveryService['deliveryService']]['description'];
        }

        $data['deliveryServices'] = $deliveryServices;
        $this->load->model('tool/image');
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        if (isset($this->request->get) && !empty($this->request->get['id_shipping_method'])) {
            $shippingMethodData = $this->model_extension_shipping_inpost_shipping->getShippingMethod($this->request->get['id_shipping_method']);
            $shippingMethodData = array_merge($data, $shippingMethodData);
            $shippingMethodData['ranges'] = json_decode($shippingMethodData['ranges'], true);
            if (empty($shippingMethodData['logo'])) {
                $shippingMethodData['logo'] = 'no_image.png';
            }
            $shippingMethodData['logo_thumb'] = $this->model_tool_image->resize($shippingMethodData['logo'], 100, 100);
            $modalHtml = $this->load->view('extension/shipping/inpost/shipping_method_modal', $shippingMethodData);
        } else {
            $data['logo_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
            $modalHtml = $this->load->view('extension/shipping/inpost/shipping_method_modal', $data);
        }

        $this->sendJsonResponse([
            'shippingMethodModal' => $modalHtml,
        ]);
    }

    public function getShippingMethodsView()
    {
        $this->load->model('extension/shipping/inpost_shipping');
        $this->load->model('tool/image');
        $shippingMethods = $this->model_extension_shipping_inpost_shipping->getShippingMethods();

        if (!empty($shippingMethods)) {
            $html = '<div id="shipping-methods-wrapper"><table class="table table-bordered">';

            $html .= $this->load->view('extension/shipping/inpost/shipping_method_heading');
            $html .= '<tbody>';
            foreach ($shippingMethods as $shippingMethod) {
                $shippingMethod['logo'] = $this->model_tool_image->resize($shippingMethod['logo'], 40, 40);
                $html .= $this->load->view('extension/shipping/inpost/shipping_method', $shippingMethod);
            }
            $html .= '</tbody>';
            $html .= '</table></div>';

            return $html;
        }

        return '';
    }

    public function retrieveShippingMethodsView()
    {
        $this->sendJsonResponse([
            'shippingMethodsHtml' => $this->getShippingMethodsView()
        ]);
    }

    public function saveShippingMethod()
    {
        $request = $this->request->post;

        $request = $this->inpostShipping->transformShippingMethodRequest($request);

        $response = [];
        if (empty($request) || !$this->validateShippingMethodForm($request)) {
            $response['error'] = true;
            $response['error_message'] = 'Form Data is not valid. Please, check required fields';
            $this->sendJsonResponse($response);
            return;
        }

        $this->load->model('extension/shipping/inpost_shipping');
        $this->load->model('tool/image');

        if ($request['shippingMethodId'] === '0') {
            $response['id'] = $this->model_extension_shipping_inpost_shipping->createShippingMethod($request);
            $response['message'] = 'New shipping method added successfully!';
            $request['id_shipping_method'] = $response['id'];
        } else {
            $response['id'] = $request['shippingMethodId'];
            $request['id_shipping_method'] = $request['shippingMethodId'];
            $this->model_extension_shipping_inpost_shipping->editShippingMethod($request);
            $response['message'] = sprintf('Shipping Method (%s) updated successfully!', $request['name']);
        }
        $request['logo'] = $this->model_tool_image->resize($request['logo'], 40, 40);
        $response['shippingMethodHtml'] = $this->load->view('extension/shipping/inpost/shipping_method', $request);
        $response['success'] = true;

        $this->sendJsonResponse($response);
    }

    public function deleteShippingMethod()
    {
        $id = $this->request->post['id_shipping_method'];

        if (empty($id) || (int)$id === 0) {
            $this->sendJsonResponse([
                'error' => true,
                'error_message' => 'Bad request.',
            ]);
            return;
        }

        $this->load->model('extension/shipping/inpost_shipping');
        $this->model_extension_shipping_inpost_shipping->deleteShippingMethod($id);

        $this->sendJsonResponse([
            'success' => true,
            'message' => "Shipping method successfully deleted!",
        ]);
    }

    public function getDeliveryServiceModal()
    {
        $data = [];
        $services = $this->inpostShipping->getServices();
        $shippingMethods = $this->inpostShipping->getShippingMethods();

        $data['apiDeliveryServices'] = $services;
        $data['apiDeliveryServicesJson'] = json_encode($services);
        $data['apiShippingMethods'] = $shippingMethods;
        $data['apiShippingMethodsJson'] = json_encode($shippingMethods);

        if (isset($this->request->get) && !empty($this->request->get['id_delivery_service'])) {
            $this->load->model('extension/shipping/inpost_shipping');
            $deliveryServiceData = $this->model_extension_shipping_inpost_shipping->getDeliveryService($this->request->get['id_delivery_service']);
            $deliveryServiceData = array_merge($data, $deliveryServiceData);
            $modalHtml = $this->load->view('extension/shipping/inpost/delivery_service_modal', $deliveryServiceData);
        } else {
            $modalHtml = $this->load->view('extension/shipping/inpost/delivery_service_modal', $data);
        }

        $this->sendJsonResponse([
            'deliveryServiceModal' => $modalHtml,
        ]);
    }

    public function getDeliveryServicesView()
    {
        $this->load->model('extension/shipping/inpost_shipping');
        $deliveryServices = $this->model_extension_shipping_inpost_shipping->getDeliveryServices();
        $apiServices = $this->inpostShipping->getServices();
        foreach ($deliveryServices as $serviceKey => $service) {
            $deliveryServices[$serviceKey]['deliveryServiceDescription'] = $apiServices[$service['deliveryService']]['description'];
        }

        if (!empty($deliveryServices)) {
            $html = '<div id="delivery-services-wrapper"><table class="table table-bordered">';

            $html .= $this->load->view('extension/shipping/inpost/delivery_service_heading');
            $html .= '<tbody>';
            foreach ($deliveryServices as $deliveryService) {
                $html .= $this->load->view('extension/shipping/inpost/delivery_service', $deliveryService);
            }
            $html .= '</tbody>';
            $html .= '</table></div>';

            return $html;
        }

        return '';
    }

    public function retrieveDeliveryServicesView()
    {
        $this->sendJsonResponse([
            'deliveryServicesHtml' => $this->getDeliveryServicesView()
        ]);
    }

    public function saveDeliveryService()
    {
        $request = $this->request->post;

        $request = $this->inpostShipping->transformDeliveryServiceRequest($request);

        $response = [];
        if (empty($request) || !$this->validateDeliveryServiceForm($request)) {
            $response['error'] = true;
            $response['error_message'] = 'Form Data is not valid. Please, check required fields';
            $this->sendJsonResponse($response);

            return;
        }

        $this->load->model('extension/shipping/inpost_shipping');

        if ($request['deliveryServiceId'] === '0') {
            $response['id'] = $this->model_extension_shipping_inpost_shipping->createDeliveryService($request);
            $response['message'] = 'New delivery service added successfully!';
            $request['id_delivery_service'] = $response['id'];
        } else {
            $response['id'] = $request['deliveryServiceId'];
            $request['id_delivery_service'] = $request['deliveryServiceId'];
            $this->model_extension_shipping_inpost_shipping->editDeliveryService($request);
            $response['message'] = sprintf('Delivery Service (%s) updated successfully!', $request['carrierName']);
        }

        $apiServices = $this->inpostShipping->getServices();
        $request['deliveryServiceDescription'] = $apiServices[$request['deliveryService']]['description'];

        $response['deliveryServiceHtml'] = $this->load->view('extension/shipping/inpost/delivery_service', $request);
        $response['success'] = true;

        $this->sendJsonResponse($response);
    }

    public function deleteDeliveryService()
    {
        $id = $this->request->post['id_delivery_service'];

        if (empty($id) || (int)$id === 0) {
            $this->sendJsonResponse([
                'error' => true,
                'error_message' => 'Bad request.',
            ]);
            return;
        }

        $this->load->model('extension/shipping/inpost_shipping');
        $this->model_extension_shipping_inpost_shipping->deleteDeliveryService($id);

        $this->sendJsonResponse([
            'success' => true,
            'message' => "Delivery service successfully deleted!",
        ]);
    }

    public function getCreateShipmentModal()
    {
        $response = [];
        $data = [];
        $idOrder = $this->request->get['order_id'];
        if (!isset($idOrder)) {
            $this->sendJsonResponse([
                'error' => 'No Order ID provided',
            ]);
            return;
        }

        $this->load->model('sale/order');
        $this->load->model('extension/shipping/inpost_shipping');
        $this->load->model('catalog/product');

        $order_info = $this->model_sale_order->getOrder($idOrder);
        $orderProducts = $this->model_sale_order->getOrderProducts($idOrder);
        $totalWeight = 0;

        $sizeTemplateMax = 0;
        $sizeTemplateName = '';
        foreach ($orderProducts as $product) {
            $product_info = $this->model_catalog_product->getProduct($product['product_id']);
            $totalWeight += $product_info['weight'] * $product['quantity'];
            if (!empty($product_info['inpost_template']) && (int)$product_info['inpost_template'] !== 0) {
                if ((int)$product_info['inpost_template'] > $sizeTemplateMax) {
                    $sizeTemplateMax = (int)$product_info['inpost_template'];
                }
            }
        }

        if ($sizeTemplateMax !== 0) {
            $sizeTemplateName = $sizeTemplateMax == 1 ? 'size_a' : '';
            if (empty($templateName) && $sizeTemplateMax == 2) {
                $sizeTemplateName =  'size_b';
            } elseif (empty($templateName) && $sizeTemplateMax == 3) {
                $sizeTemplateName =  'size_c';
            }
        }

        $inpostShippingModuleSettings = $this->config->get('shipping_inpost_shipping_settings');
        $deliveryServiceSettings = $this->model_extension_shipping_inpost_shipping->getSettingsByOrderShippingCode($order_info['shipping_code']);
        $apiServices = $this->inpostShipping->getServices();
        $shippingMethods = $this->inpostShipping->getShippingMethods();

        $data['email'] = $order_info['email'];
        $data['phone'] = $order_info['telephone'];
        $data['total'] = $order_info['total'];
        $data['orderInfo'] = base64_encode(json_encode($order_info));
        $data['orderReference'] = $order_info['order_id'];
        $data['insurance'] = $inpostShippingModuleSettings['default_insurance'];
        $data['deliveryService'] = $deliveryServiceSettings;

        $data['dimensions'] = $this->calculateDimensions($order_info['order_id'], $deliveryServiceSettings);

        $data['services'] = $apiServices;
        $data['servicesJson'] = json_encode($apiServices);
        $data['apiShippingMethods'] = $shippingMethods;
        $data['apiShippingMethodsJson'] = json_encode($shippingMethods);
        $data['weight'] = $totalWeight;
        $data['parcelLocker'] = $order_info['parcelLocker'];
        $data['sizeTemplate'] = !empty($sizeTemplateName) ? $sizeTemplateName : $deliveryServiceSettings['sizeTemplate'];
        $lockerServices = [
            'inpost_locker_standard',
            'inpost_locker_allegro',
            'inpost_locker_pass_thru',
            'inpost_courier_c2c',
        ];
        $data['showLocker'] = !empty($deliveryServiceSettings['deliveryService']) ? in_array($deliveryServiceSettings['deliveryService'], $lockerServices) : true;
        $data['user_token'] = $this->session->data['user_token'];

        $response['createShipmentModalHtml'] = $this->load->view('extension/shipping/inpost/create_shipment_modal', $data);

        $this->sendJsonResponse($response);
    }

    protected function calculateDimensions($idOrder, $deliveryServiceSettings)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."order_product` WHERE order_id = '".(int)$idOrder."'";
        $orderProducts = $this->db->query($sql)->rows;
        $volumes = [];
        $productDimensions = [];

        foreach ($orderProducts as $product) {
            $productInfo = $this->db->query("SELECT * FROM `".DB_PREFIX."product` WHERE product_id = '".(int)$product['product_id']."'")->row;
            if ($productInfo['length'] == 0.0000 || $productInfo['width'] == 0.0000 || $productInfo['height'] == 0.0000) {
                continue;
            }

            $volumes[$productInfo['product_id']] = $productInfo['width'] * $productInfo['height'] * $productInfo['length'];
            $productDimensions[$productInfo['product_id']] = [
                'width' => $productInfo['width'],
                'length' => $productInfo['length'],
                'height' => $productInfo['height'],
            ];
        }

        if (!empty($volumes)) {
            asort($volumes);
            $maxVolumeProduct = array_key_last($volumes);

            return [
                'length' => $productDimensions[$maxVolumeProduct]['length'],
                'width' => $productDimensions[$maxVolumeProduct]['width'],
                'height' => $productDimensions[$maxVolumeProduct]['height']
            ];
        }

        return [
            'length' => $deliveryServiceSettings['length'],
            'width' => $deliveryServiceSettings['width'],
            'height' => $deliveryServiceSettings['height']
        ];
    }

    public function createShipmentAction($bulkRequest = [], $bulk = false)
    {
        $request = $bulk ? $bulkRequest : $this->request->post;

        $errors = $this->validateCreateShipmentRequest($request);

        if (count($errors) > 0) {
            if ($bulk) {
                return [
                    'id' => $request['orderReference'],
                    'error' => $errors[0],
                ];
            }

            $this->sendJsonResponse([
                'error' => $errors[0],
            ]);
            return;
        }
        $response = $this->inpostShipping->createShipment($request, $this);

        if (isset($response['error'])) {
            $error_type = '';
            if ($response['error'] === 'validation_failed') {
                $error_type = 'Validation failed';
            }

            if ($response['error'] === 'missing_trucker_id') {
                if ($bulk) {
                    return [
                        'id' => $request['orderReference'],
                        'error' => 'Trucker ID is not set for organization.',
                    ];
                }
                $this->sendJsonResponse([
                    'error' => [
                        'errorType' => 'Missing Trucker ID',
                        'errorDescription' => 'Trucker ID is not set for organization.',
                    ],
                ]);
                return;
            }

            $errorDetails = [];
            foreach ($response['details'] as $errorKey => $errorDetail) {
                $issues = [];
                foreach ((array)$errorDetail[0] as $issueKey => $issue) {
                    $issues[$issueKey] = $issue;
                }

                $errorDetails[] = json_encode($issues) . ' for ' . $errorKey . ' details';
            }
            if ($bulk) {
                return [
                    'id' => $request['orderReference'],
                    'error' => $errorDetails[0],
                ];
            }

            $this->sendJsonResponse([
                'error' => [
                    'errorType' => $error_type,
                    'errorDescription' => $errorDetails[0],
                ],
            ]);
            return;
        }

        $response['phone'] = $request['phone'];
        $response['email'] = $request['email'];

        $this->load->model('extension/shipping/inpost_shipping');


        $response['sbxMode'] = (int)$this->config->get('shipping_inpost_shipping_sandbox_mode');
        $response['lastSync'] = strtotime(date('Y-m-d H:i:s'));
        $idShipment = $this->model_extension_shipping_inpost_shipping->createShipment($response);

        $response['idShipment'] = $idShipment;

        if ($bulk) {
            return [
                'id' => $request['orderReference'],
                'success' => true,
                'message' => 'New shipment for order #' . $request['orderReference'] . ' was successfully created',
            ];
        }
        $this->sendJsonResponse([
            'success' => true,
            'shipmentHtml' => $this->load->view('extension/shipping/inpost/create_shipment_modal', $response)
        ]);
    }

    public function getPrintLabelModal()
    {
        if (empty($this->request->get['id'])) {
            $this->sendJsonResponse([
                'error' => 'Shipment id cannot be found',
            ]);
            return;
        }

        $this->sendJsonResponse([
            'printLabelModal' => $this->load->view('extension/shipping/inpost/print_label_modal', ['id' => $this->request->get['id']]),
        ]);
    }

    public function getBulkPrintLabelModal()
    {
        if (empty($this->request->get['id'])) {
            $this->sendJsonResponse([
                'error' => 'Something wrong with selected items. Please, re-select items again.',
            ]);
            return;
        }

        $this->sendJsonResponse([
            'printLabelModal' => $this->load->view('extension/shipping/inpost/print_label_modal', ['id' => $this->request->get['id'], 'bulk' => true]),
        ]);
    }

    public function printLabel($singleId = '', $format = '', $type = '')
    {
        if (empty($singleId) && empty($this->request->get['id'])) {
            $this->sendJsonResponse([
                'error' => 'Shipment id cannot be found',
            ]);
            return;
        }

        $id = !empty($singleId) ? $singleId : $this->request->get['id'];

        $this->load->model('extension/shipping/inpost_shipping');
        $shipmentData = $this->model_extension_shipping_inpost_shipping->getShipment($id);

        $labelGenerateResponse = $this->inpostShipping
            ->printLabel([
                'idShipment' => $shipmentData['idInpost'],
                'format' => !empty($format) ? $format : $this->request->get['format'],
                'type' => !empty($type) ? $type : $this->request->get['type'],
                'service' => $shipmentData['service'],
                'created_at' => $shipmentData['created_at']
            ]);

        $this->model_extension_shipping_inpost_shipping->setLabelPrinted($id);

        $this->sendJsonResponse($labelGenerateResponse);
    }

    public function bulkPrintLabels()
    {
        if (empty($this->request->get['ids'])) {
            $this->sendJsonResponse([
                'error' => 'No selected items.',
            ]);
            return;
        }

        $ids = explode(',', $this->request->get['ids']);

        if (count($ids) < 2) {
            $this->printLabel($ids[0], $this->request->get['format'], $this->request->get['type']);
            return;
        }

        $this->load->model('extension/shipping/inpost_shipping');

        $shipments = [];
        foreach ($ids as $id) {
            $shipments[] = $this->model_extension_shipping_inpost_shipping->getShipment($id)['idInpost'];
        }

        $labelGenerateResponse = $this->inpostShipping
            ->printLabel([
                'shipmentIds' => $shipments,
                'format' => $this->request->get['format'],
                'type' => $this->request->get['type'],
                'bulk' => true,
            ]);

        foreach ($ids as $id) {
            $this->model_extension_shipping_inpost_shipping->setLabelPrinted($id);
        }

        $this->sendJsonResponse($labelGenerateResponse);
    }

    public function printDispatchLabel()
    {
        if (empty($this->request->get['id'])) {
            $this->sendJsonResponse([
                'error' => 'Shipment id cannot be found',
            ]);
            return;
        }

        $id = $this->request->get['id'];

        $this->load->model('extension/shipping/inpost_shipping');
        $idShipment = $this->model_extension_shipping_inpost_shipping->getShipment($id)['idInpost'];
        $idInpostDispatch = $this->model_extension_shipping_inpost_shipping->getDispatchId($idShipment);

        $labelGenerateResponse = $this->inpostShipping
            ->printDispatchLabel($idInpostDispatch);

        $this->sendJsonResponse($labelGenerateResponse);
    }

    public function bulkPrintDispatchLabel()
    {
        if (empty($this->request->get['ids'])) {
            $this->sendJsonResponse([
                'error' => 'Something wrong with selected items. Please, re-select items again.',
            ]);
            return;
        }

        $ids = explode(',', $this->request->get['ids']);

        $this->load->model('extension/shipping/inpost_shipping');

        $dispatchedShipments = [];
        foreach ($ids as $id) {
            $shipmentDetails = $this->model_extension_shipping_inpost_shipping->getShipment($id);
            if ((int)$shipmentDetails['dispatched'] === 1) {
                $dispatchedShipments[] = $shipmentDetails['idInpost'];
            }
        }

        if (empty($dispatchedShipments)) {
            $this->sendJsonResponse([
                'error' => 'All selected shipments are not dispatched.',
            ]);
            return;
        }

        $labelGenerateResponse = $this->inpostShipping
            ->printBulkDispatchLabel($dispatchedShipments);

        $this->sendJsonResponse($labelGenerateResponse);
    }

    public function getDispatchOrderModal()
    {
        if (empty($this->request->get['id'])) {
            $this->sendJsonResponse([
                'error' => 'Shipment id cannot be found',
            ]);
            return;
        }

        $data = [];
        $id = $this->request->get['id'];

        $this->load->model('extension/shipping/inpost_shipping');
        $pickupPoints = $this->model_extension_shipping_inpost_shipping->getPickupPoints();

        $data['id'] = $id;
        $data['pickupPoints'] = $pickupPoints;

        $this->sendJsonResponse([
            'dispatchOrderModal' => $this->load->view('extension/shipping/inpost/dispatch_order_modal', $data),
        ]);
    }

    public function getBulkDispatchOrderModal()
    {
        if (empty($this->request->get['id'])) {
            $this->sendJsonResponse([
                'error' => 'Something wrong with selected items. Please, re-select items again.',
            ]);
            return;
        }

        $data = [];
        $id = $this->request->get['id'];

        $this->load->model('extension/shipping/inpost_shipping');
        $pickupPoints = $this->model_extension_shipping_inpost_shipping->getPickupPoints();

        $data['id'] = $id;
        $data['bulk'] = true;
        $data['pickupPoints'] = $pickupPoints;

        $this->sendJsonResponse([
            'dispatchOrderModal' => $this->load->view('extension/shipping/inpost/dispatch_order_modal', $data),
        ]);
    }

    public function dispatchOrderAction()
    {
        if (empty($this->request->post['id'])) {
            $this->sendJsonResponse([
                'error' => 'Shipment id cannot be found',
            ]);
            return;
        }

        if (empty($this->request->post['point'])) {
            $this->sendJsonResponse([
                'error' => 'Pickup Point cannot be found',
            ]);
            return;
        }

        $response = $this->inpostShipping->dispatchOrder(
            [
                'ids' => [
                    $this->request->post['id'],
                ],
                'point' => $this->request->post['point'],
            ],
            $this
        );

        if (!empty($response[0]) && !empty($response[0]['error']) && $response[0]['error'] == 'validation_failed') {
            $this->sendJsonResponse([
                'error' => 'Shipment is already dispatched!',
            ]);
            return;
        }

        $this->load->model('extension/shipping/inpost_shipping');
        $this->model_extension_shipping_inpost_shipping->createDispatchOrder($response);
        $this->model_extension_shipping_inpost_shipping->setDispatched($this->request->post['id']);

        $this->sendJsonResponse([
            'success' => true,
            'message' => 'Shipment successfully dispatched!',
            'response' => $response,
        ]);
    }

    public function bulkDispatchOrderAction()
    {
        if (empty($this->request->post['ids'])) {
            $this->sendJsonResponse([
                'error' => 'Shipment id cannot be found',
            ]);
            return;
        }

        if (empty($this->request->post['point'])) {
            $this->sendJsonResponse([
                'error' => 'Pickup Point cannot be found',
            ]);
            return;
        }

        $ids = explode(',', $this->request->post['ids']);

        $this->load->model('extension/shipping/inpost_shipping');
        $shipmentsToDispatch = [];
        foreach ($ids as $id) {
            $shipmentDetails = $this->model_extension_shipping_inpost_shipping->getShipment($id);
            if ((int)$shipmentDetails['dispatched'] !== 0) {
                $shipmentsToDispatch[] = $id;
            }
        }

        if (empty($shipmentsToDispatch)) {
            $this->sendJsonResponse([
                'error' => 'All selected shipments are already dispatched.',
            ]);
            return;
        }

        $response = $this->inpostShipping->dispatchOrder(
            [
                'ids' => $shipmentsToDispatch,
                'point' => $this->request->post['point'],
            ],
            $this
        );

        if (!empty($response[0]) && !empty($response[0]['error']) && $response[0]['error'] == 'validation_failed') {
            $this->sendJsonResponse([
                'error' => 'Shipment is already dispatched!',
            ]);
            return;
        }

        $this->load->model('extension/shipping/inpost_shipping');
        $this->model_extension_shipping_inpost_shipping->createDispatchOrder($response);
        $this->model_extension_shipping_inpost_shipping->setDispatched($this->request->post['id']);

        $this->sendJsonResponse([
            'success' => true,
            'message' => 'Shipment successfully dispatched!',
            'response' => $response,
        ]);
    }

    public function bulkCreateShipment()
    {
        $ids = $this->request->post['ids'];

        $shipments = [];

        $this->load->model('sale/order');
        $this->load->model('extension/shipping/inpost_shipping');
        $this->load->model('catalog/product');
        foreach ($ids as $idOrder) {
            $order_info = $this->model_sale_order->getOrder($idOrder);
            $orderProducts = $this->model_sale_order->getOrderProducts($idOrder);

            $inpostShippingModuleSettings = $this->config->get('shipping_inpost_shipping_settings');
            $deliveryServiceSettings = $this->model_extension_shipping_inpost_shipping->getSettingsByOrderShippingCode($order_info['shipping_code']);
            if (empty($deliveryServiceSettings)) {
                $shipments[] = [
                    'id' => $order_info['order_id'],
                    'error' => 'Delivery service related to this order was removed. Please, choose another one.',
                ];
                continue;
            }

            $totalWeight = 0;
            $sizeTemplateMax = 0;
            $sizeTemplateName = '';
            foreach ($orderProducts as $product) {
                $product_info = $this->model_catalog_product->getProduct($product['product_id']);
                $totalWeight += $product_info['weight'] * $product['quantity'];
                if (!empty($product_info['inpost_template']) && (int)$product_info['inpost_template'] !== 0) {
                    if ((int)$product_info['inpost_template'] > $sizeTemplateMax) {
                        $sizeTemplateMax = (int)$product_info['inpost_template'];
                    }
                }
            }

            if ($sizeTemplateMax !== 0) {
                $sizeTemplateName = $sizeTemplateMax == 1 ? 'size_a' : '';
                if (empty($templateName) && $sizeTemplateMax == 2) {
                    $sizeTemplateName =  'size_b';
                } elseif (empty($templateName) && $sizeTemplateMax == 3) {
                    $sizeTemplateName =  'size_c';
                }
            }

            $data['email'] = $order_info['email'];
            $data['phone'] = $order_info['telephone'];
            $deliveryService = $data['delivery_service'] = $deliveryServiceSettings['deliveryService'];
            $data['shipping_method'] = $deliveryServiceSettings['shippingMethod'];
            $data['total'] = $order_info['total'];
            $data['orderInfo'] = base64_encode(json_encode($order_info));
            $data['orderReference'] = $order_info['order_id'];
            $data['insurance'] = $inpostShippingModuleSettings['default_insurance'];
            if ($deliveryServiceSettings['cod']) {
                $data['insurance'] = $order_info['total'];
            }

            if ($deliveryService === 'inpost_locker_standard' ||
                $deliveryService === 'inpost_locker_allegro' ||
                $deliveryService === 'inpost_locker_pass_thru' ||
                $deliveryService === 'inpost_courier_c2c'
            ) {
                $data['predefinedTemplate'] = true;
                $data['template'] = !empty($sizeTemplateName) ? $sizeTemplateName : $deliveryServiceSettings['sizeTemplate'];
            }

            $dimensions = $this->calculateDimensions($order_info['order_id'], $deliveryServiceSettings);
            $data['length'] = $dimensions['length'];
            $data['width'] = $dimensions['width'];
            $data['height'] = $dimensions['height'];
            $data['parcelLocker'] = $order_info['parcelLocker'];
            $data['weight'] = $totalWeight;

            $request = $data;

            $shipments[] = $this->createShipmentAction($request, true);
        }

        $this->sendJsonResponse($shipments);
    }

    public function syncTrackingNumber()
    {
        if (empty($this->request->post['id'])) {
            $this->sendJsonResponse([
                'error' => 'Shipment ID not found or not provided.',
            ]);
            return;
        }

        $id = $this->request->post['id'];

        $this->load->model('extension/shipping/inpost_shipping');

        $shipmentInfo = $this->model_extension_shipping_inpost_shipping->getShipment($id);
        $idInpost = $shipmentInfo['idInpost'];

        $response = $this->inpostShipping->syncTrackingNumber($idInpost, $this);

        if (!empty($response['error'])) {
            $this->sendJsonResponse([
                'error' => 'Error during sync tracking number.',
                'error_details' => !empty($response['error_details']) ? $response['error_details'] : '',
            ]);
            return;
        }

        $this->model_extension_shipping_inpost_shipping->setTrackingNumber($id, $response['tracking_number']);

        $this->sendJsonResponse([
            'success' => true,
            'tracking_number' => $response['tracking_number'],
        ]);
    }

    public function getInpostStatuses()
    {
        $this->sendJsonResponse([
            'statuses' => $this->inpostShipping->getStatuses()
        ]);
    }

    public function getInpostServices()
    {
        $this->sendJsonResponse([
            'services' => $this->inpostShipping->getServices()
        ]);
    }

    protected function validateShippingMethodForm($request)
    {
        if (empty($request['name']) ||
            empty($request['delivery_time']) ||
            empty($request['reckoning'])
        ) {
            return false;
        }

        return true;
    }

    protected function validatePickupPointForm($request)
    {
        if (empty($request['pickupName']) ||
            empty($request['pickupStreet']) ||
            empty($request['pickupHousenumber']) ||
            empty($request['pickupCity']) ||
            empty($request['pickupPostcode']) ||
            empty($request['pickupTelephone'])
        ) {
            return false;
        }

        return true;
    }

    protected function validateDeliveryServiceForm($request)
    {
        if (empty($request['deliveryService']) ||
            empty($request['carrierName'])
        ) {
            return false;
        }

        return true;
    }

    protected function validateCreateShipmentRequest($request)
    {
        $error = [];

        $availableLockerServices = [
            'inpost_locker_standard',
            'inpost_locker_allegro',
            'inpost_locker_pass_thru',
            'inpost_courier_c2c',
        ];

        if (in_array($request['delivery_service'], $availableLockerServices)) {
            if (empty($request['predefinedTemplate'])) {
                if ((int)$request['height'] === 0) {
                    $error[] = [
                        'height' => 'Height value must be higher'
                    ];
                }
                if ((int)$request['width'] === 0) {
                    $error[] = [
                        'width' => 'Width value must be higher'
                    ];
                }
                if ((int)$request['length'] === 0) {
                    $error[] = [
                        'length' => 'Length value must be higher'
                    ];
                }
                if ($request['weight'] === 0.00) {
                    $error[] = [
                        'weight' => 'Weight value must be higher'
                    ];
                }
            }
        }

        if (empty($request['email'])) {
            $error[] = [
                'errorType' => 'Email validation error',
                'errorDescription' => 'Email is required'
            ];
        }

        if (empty($request['phone'])) {
            $error[] = [
                'errorType' => 'Phone validation error',
                'errorDescription' => 'Phone is required',
            ];
        }

        if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
            $error[] = [
                'errorType' => 'Email validation error',
                'errorDescription' => 'Invalid email format',
            ];
        }

        preg_match('/\d+/', $request['phone'], $phoneRegexCheck);
        if (strlen($request['phone']) !== 9 || strlen($phoneRegexCheck[0]) !== 9) {
            $error[] = [
                'errorType' => 'Phone validation error',
                'errorDescription' => 'Invalid phone format. It should contains 9 digits',
            ];
        }

        if ((float)$request['insurance'] !== 0.0000 && $request['insurance'] < 1) {
            $error[] = [
                'errorType' => 'Insurance validation error',
                'errorDescription' => 'Insurance amount must be higher',
            ];
        }

        return $error;
    }

    public function shipmentsList()
    {
        $data = [];

        $this->document->addStyle('view/javascript/inpost_shipping/inpost.css');
        $this->document->addStyle('view/stylesheet/stylesheet.css');
        $this->document->addScript('view/javascript/inpost_shipping/inpost_shipment.js');
        $this->document->addScript('view/javascript/inpost_shipping/inpost_bulk.js');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['heading_title'] = 'Inpost Shipments';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/inpost_shipping/shipmentsList', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['userToken'] = $this->session->data['user_token'];
        $returnsUrl = !empty($this->config->get('shipping_inpost_shipping_settings')['quick_returns']) ? $this->config->get('shipping_inpost_shipping_settings')['quick_returns'] : '';
        $data['returnsUrl'] = 'https://szybkiezwroty.pl/'. $returnsUrl .'#navigate-buttons';

//        $data['action'] = $this->url->link('extension/shipping/inpost_shipping', 'user_token=' . $this->session->data['user_token'], true);

        $data['goToOrders'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true);

        $this->load->model('extension/shipping/inpost_shipping');
        $this->load->model('sale/order');

        $data['apiServices'] = $this->inpostShipping->getServices();
        $data['apiShippingMethods'] = $this->inpostShipping->getShippingMethods();

        $filters = [];

        foreach ($this->request->get as $k => $v) {
            if (strpos($k, 'filter_') !== false) {
                $filters[$k] = $data[$k] = $v;
            }
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'id_shipment';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $start = ($page - 1) * $this->config->get('config_limit_admin');
        $limit = $this->config->get('config_limit_admin');

        $shipmentTotal = $this->model_extension_shipping_inpost_shipping->getAllShipmentsTotal($filters, $sort, $order);
        $data['shipments'] = $this->model_extension_shipping_inpost_shipping->getAllShipments($filters, $sort, $order, $start, $limit);

        $data['services'] = $this->model_extension_shipping_inpost_shipping->getAllShipmentServices();
        $data['sendingMethods'] = $this->model_extension_shipping_inpost_shipping->getAllShipmentSendingMethods();

        foreach ($data['shipments'] as $k => $shipment) {
            $data['shipments'][$k]['orderInfo'] = $data['orderInfo'] = $this->model_sale_order->getOrder($shipment['reference']);
            if (!empty($shipment['custom_attributes']) && (int)$shipment['dispatched'] === 0) {
                $customAttributes = json_decode($shipment['custom_attributes'], 1);
                $data['shipments'][$k]['createDispatchOrder'] = $customAttributes['sending_method'] === 'dispatch_order';
            } elseif ((int)$shipment['dispatched'] === 1) {
                $data['shipments'][$k]['printDispatchOrder'] = 1;
            }
        }

        $url = '';
        foreach ($filters as $filterKey => $filter) {
            $url .= '&' . $filterKey . '=' . urlencode(html_entity_decode($filter, ENT_QUOTES, 'UTF-8'));
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $sortKeys = [
            'idInpost',
            'reference',
            'price',
            'email',
            'created_at',
            'labelPrinted',
            'dispatched',
        ];

        foreach ($sortKeys as $sortKey) {
            $data['sort_filter_'.$sortKey] = $this->url->link('extension/shipping/inpost_shipping/shipmentsList', 'user_token=' . $this->session->data['user_token'] . '&sort='. $sortKey . $url, true);
        }

        $url = '';
        foreach ($filters as $filterKey => $filter) {
            $url .= '&' . $filterKey . '=' . $filter;
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $shipmentTotal;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/shipping/inpost_shipping/shipmentsList', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($shipmentTotal) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($shipmentTotal - $this->config->get('config_limit_admin'))) ? $shipmentTotal : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $shipmentTotal, ceil($shipmentTotal / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/shipping/inpost/shipmentList', $data));
    }

    /**
     * @param $data
     */
    protected function sendJsonResponse($data)
    {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/shipping/inpost_shipping')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $apiParams = [
            'sandbox' => [
                'key' => $this->request->post['shipping_inpost_shipping_sandbox_api_key'],
                'idOrganization' => $this->request->post['shipping_inpost_shipping_sandbox_organization_id']
            ],
            'live' => [
                'key' => $this->request->post['shipping_inpost_shipping_api_key'],
                'idOrganization' => $this->request->post['shipping_inpost_shipping_organization_id']
            ],
        ];
        $apiKeysValid = $this->inpostShipping->validateApiKeys($apiParams);

        if (!$apiKeysValid) {
            $this->error['warning'] = 'Entered API keys are not valid for provided organization ID';
        }

        $dayStart = (int)$this->request->post['shipping_inpost_shipping_settings']['weekend_day_start'];
        $dayEnd = (int)$this->request->post['shipping_inpost_shipping_settings']['weekend_day_end'];
        if ($dayStart > $dayEnd) {
            $this->error['warning'] = 'Weekend Delivery: "Start" day can\'t be later than "End" day';
        }

        if (
            $dayStart === $dayEnd &&
            !empty($this->request->post['shipping_inpost_shipping_settings']['weekend_time_start']) &&
            !empty($this->request->post['shipping_inpost_shipping_settings']['weekend_time_end'])
        ) {
            $timeStart = $this->request->post['shipping_inpost_shipping_settings']['weekend_time_start'];
            $timeEnd = $this->request->post['shipping_inpost_shipping_settings']['weekend_time_end'];

            if (strtotime($timeStart) > strtotime($timeEnd)) {
                $this->error['warning'] = 'Weekend Delivery: "Start" time can\'t be later than "End" time';
            }
        }

        return !$this->error;
    }

    public function install()
    {
        $this->load->model('extension/shipping/inpost_shipping');
        $this->model_extension_shipping_inpost_shipping->installDatabases();
        $this->model_extension_shipping_inpost_shipping->prepare();
    }
}