<?php
class ControllerExtensionModulePixselStats extends Controller
{

  private $error = array();

  public function index()
  {


    $this->load->language('extension/module/pixsel_parser');
    $this->load->model('setting/setting');

    $this->document->setTitle($this->language->get('heading_title_stats'));

    // Breadcrumbs
    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title_stats'),
      'href' => $this->url->link('extension/module/pixsel_stats', 'user_token=' . $this->session->data['user_token'], true)
    );

    // Button actions
    $data['action'] = $this->url->link('extension/module/pixsel_stats', 'user_token=' . $this->session->data['user_token'], true);
    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);



    $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
    unset($this->session->data['success']);

    $data['user_token'] = $this->session->data['user_token'];


    // code

    // Date filter

    $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : '';
    $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : '';


    $months = $this->language->get('text_months');

    if (isset($this->request->get['filter_date_start'])) {
      $currentDate = new DateTime($this->request->get['filter_date_start']);
    } else {
      $currentDate = new DateTime();
    }

    $current_month = $months[(int)$currentDate->format('n')] . ' ' . $currentDate->format('Y');
    $data['current_month'] = $current_month;

    $data['filter_date_start'] = $filter_date_start;
    $data['filter_date_end'] = $filter_date_end;

    // $data['іs_today'] = $filter_date_end && !$filter_date_start;
    $data['іs_today'] = false;

    $data['text_start_date'] = $filter_date_start ?: date('Y-m-01');
    $data['text_end_date'] = $filter_date_end ?: date('Y-m-t');

    $filter = [
      'start_date' => $filter_date_start ?: date('Y-m-01'),
      'end_date' => $filter_date_end ?: date('Y-m-t'),
    ];


    $this->load->model('extension/module/pixsel_parser');
    $products = $this->model_extension_module_pixsel_parser->get_order_products_stats($filter);

    $data['totals'] = $this->model_extension_module_pixsel_parser->get_stats($products);



    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');


    $this->response->setOutput($this->load->view('extension/module/pixsel_stats', $data));
  }



  protected function validate()
  {
    if (!$this->user->hasPermission('modify', 'extension/module/pixsel_stats')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    return !$this->error;
  }

}
