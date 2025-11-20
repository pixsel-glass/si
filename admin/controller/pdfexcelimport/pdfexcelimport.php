<?php
class ControllerPdfexcelimportPdfexcelimport extends Controller {
	private $error = array();

    public function small_pdf($redirect=1, $from_site = 0){    
      ini_set('error_reporting', E_ALL);
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      set_time_limit (60);
      ini_set('memory_limit', '5512M');

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $order_id = $this->request->post['order_id'];
      } else {
        $order_id = $this->request->get['order_id'];
      }
      
      $this->load->model('sale/order');
      $this->load->model('catalog/product');
      $this->load->model('catalog/category');
      $this->load->model('catalog/option');
      $this->load->model('setting/setting');

      $language_sticker = (!empty($this->config->get('module_my_pdfstickers_language')) ? $this->config->get('module_my_pdfstickers_language') : (int)$this->config->get('config_language_id'));
      // echo $language_sticker;
      // exit;

      // require_once($_SERVER['DOCUMENT_ROOT'].'/system/library/fpdf/fpdf.php' );
      require_once($_SERVER['DOCUMENT_ROOT'].'/system/library/tfpdf/tfpdf.php' );

      // if ($from_site==0) {
        $nn_file = 'order_'.$order_id.'.pdf';
      // } elseif ($from_site==1) {
      //  $nn_file = 'cart'.date('dmY').'-'.date('Hi').'.pdf';
      // }

      //$pdf = new FPDF('L', 'cm', array(3.2,2));
      //$pdf = new FPDF('L', 'pt', array(90.7,34.1));
      // $pdf = new FPDF('L', 'pt', array(121.7,39.7));
      $pdf = new tFPDF('L', 'pt', array(121.7,39.7));
      $pdf->SetMargins(0,2);
      $pdf->SetAutoPageBreak(false, 10);
      $pdf->SetAuthor($_SERVER['SERVER_NAME']);
      $pdf->SetTitle($nn_file);

      $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
      // $pdf->SetFont('DejaVu','',14);
      $pdf->SetFont('DejaVu');
      $pdf->SetTextColor(0,0,0);
 

      //data---------------------
      $products = $this->model_sale_order->getOrderProducts($order_id);

      foreach ($products as $product) {
        for ($i=1; $i <= $product['quantity'] ; $i++) { 
          
        $product_info = $this->model_catalog_product->getProductDescriptions($product['product_id']);
		//$name_ukr = $this->model_catalog_product->getProduct($product['product_id']);
		//console_log($name_ukr);
        $categories = $this->model_catalog_product->getProductCategories($product['product_id']);
        $category_info = $this->model_catalog_category->getCategory($categories[0]);
		
		/*$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id AND cd1.language_id = '" . 5 . "' GROUP BY cp.category_id) AS path FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd2.language_id = '" . 5 . "'");
		$this->console_log($query->row['name']);*/
	
        // $name = $product_info[$language_sticker]['pixsel_short_name'].' '.$category_info['name'];
        $name = $product_info[$language_sticker]['pixsel_short_name'];

        $code = '';
        $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);
        $product_options = $this->model_catalog_product->getProductOptionsLanguage($product['product_id'],$language_sticker);

        /*foreach ($product_options as $key => $value) {
            $option_values = $this->model_catalog_option->getOptionValuesMs($value['option_id'], $language_sticker);
            foreach ($value['product_option_value'] as $key2 => $value2) {
                if($value2['product_option_value_id']==$options[0]['product_option_value_id']){
                    $code = $option_values[0]['name'] . ' ( '.trim($value2['pixsel_sku']).' )';
                }
            }
        }*/

        foreach ($product_options as $key => $value) {
          $option_values = $this->model_catalog_option->getOptionValuesMs($value['option_id'], $language_sticker);
          $optvalue = array();
          foreach ($option_values as $optvalue) {
            $optvalues[$optvalue['option_value_id']] = $optvalue['name'];
          }
          foreach ($value['product_option_value'] as $key2 => $value2) {
              if($value2['product_option_value_id']==$options[0]['product_option_value_id']){
                  // $code = $option_values[0]['name'] . ' ( '.trim($value2['pixsel_sku']).' )';
                  $code = $optvalues[$value2['option_value_id']] . ' ( '.trim($value2['pixsel_sku']).' )';
              }
          }
      }

        // $nameoptions = $options[0]['value'].$code;
        $nameoptions = $code;

        // echo $nameoptions; exit;

        $pdf->AddPage();

        $pdf->SetFontSize(8);
        // $pdf->MultiCell('',8,iconv('utf-8', 'windows-1251', $name),0,'L',0);
        // $pdf->MultiCell('',8,iconv("UTF-8", "CP1252//IGNORE", $name),0,'L',0);
        $pdf->MultiCell('',8,$name,0,'L',0);
        $pdf->Ln(1);
        $pdf->SetFontSize(7);
        // $pdf->MultiCell('',8,iconv('utf-8', 'windows-1251', $nameoptions),0,'L',0);
        // $pdf->MultiCell('',8,iconv('UTF-8','iso-8859-2//TRANSLIT//IGNORE',$nameoptions),0,'L',0);
        $pdf->MultiCell('',8,$nameoptions,0,'L',0);
        }
      }
      
      //data---------------------
      if($from_site==0 or $from_site==1){
        $directory = '/pdfstickers/';
        $file_data = $directory.$nn_file;

        $pdf->Output($_SERVER['DOCUMENT_ROOT'].$file_data, "F" );
      }
      if($redirect==1){
         $this->response->redirect($this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'].'&order_id='.$order_id, true));
       }
    }

    public function reload_pdf(){
    	  ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        set_time_limit (60);
        ini_set('memory_limit', '5512M');

        $this->load->language('pdfexcelimport/pdfexcelimport');
        
        $this->document->setTitle($this->language->get('text_title'));
		    $this->load->model('pdfexcelimport/pdfexcelimport');
		    $this->load->model('customer/customer_group');
		    $this->load->model('catalog/category');

        $customer_group_id = $this->request->get['customer_group_id'];
		    $customer_group = $this->model_customer_customer_group->getCustomerGroup($customer_group_id);
		    $prices = $this->model_pdfexcelimport_pdfexcelimport->getPriceSettings($customer_group['customer_group_id']);
  
		    if(isset($prices['prices_ids'])){
		      $prices_ids = explode(',', $prices['prices_ids']);
		    }
		   if(isset($prices['prices_dopin'])){
		      $prices_dopin = explode(',', $prices['prices_dopin']);
		    }
        if(isset($prices['prices_dopin_poask'])){
           $prices_dopin_poask = explode(',', $prices['prices_dopin_poask']);
        }

		    $customer_group['prices'] = $prices;

		    $nn = $customer_group['prices']['prices_filename'];

		    //PDF-------------------------------------
        $nn_file = $customer_group['prices']['prices_filename'];
        if($customer_group['prices']['prices_dateadd']=='1'){
        	$nn_file .= '_'.date("d_m_Y");
        }
        $nn_file .= '.pdf';

        // require_once($_SERVER['DOCUMENT_ROOT'].'/libs/fpdf/fpdf.php' );
        require_once($_SERVER['DOCUMENT_ROOT'].'/system/library/fpdf/fpdf.php' );

        $pdf = new FPDF( 'L', 'pt', 'A4' );
        $pdf->SetMargins(20,20,20);
        $pdf->SetAuthor('pixsel.com.ua');
        $pdf->SetTitle($nn_file);

        $pdf->AddFont('Arial','','arial.php');
        $pdf->SetFont('Arial');
        $pdf->SetTextColor(0,0,0);
 
        $pdf->AddPage('L');
        $pdf->SetDisplayMode('real','default');

        //---------header---------------------
        $margin = 20;
        $pdf->SetTextColor(255,255,255);
        $pdf->SetDrawColor(0,0,0);

        $pdf->SetXY($margin,$margin);
        $pdf->Cell(0,120,'',1,0,'R',1);

        $src = $_SERVER['DOCUMENT_ROOT'].'/image/logoexcel.png';
        $pdf->Image($src,30,30,600);

        $left_header = 650;
        $pdf->SetFontSize(12);
        $pdf->SetXY($left_header,45);
        $pdf->Write(5,iconv('utf-8', 'windows-1251',$this->config->get('config_telephone')));
        $pdf->SetXY($left_header,60);
        $pdf->Write(5,iconv());
        $pdf->SetXY($left_header,75);
        $pdf->Write(5,iconv());
        $pdf->SetXY($left_header,90);
        $pdf->Write(5,iconv('utf-8', 'windows-1251',$this->config->get('config_email')));

        $src = $_SERVER['DOCUMENT_ROOT'].'/image/insta.png';
        $pdf->Image($src,$left_header,105,18);
        $pdf->SetXY($left_header + 20,110);
        $pdf->Write(5,iconv('utf-8', 'windows-1251','pixsel.glass'));
        //---------header---------------------

        //---------table header---------------------
        $pdf->SetXY($margin,140);
        $pdf->SetFontSize(7);
        $pdf->SetTextColor(0,0,0);
        $width = array(10,60,65,142,50.6);
        $column_height = 20;
        
        $name_price_1 = $this->language->get('text_sklo_ga');
        $name_price_2 = $this->language->get('text_sklo_gm');

        $prices_dopin = explode(',', $customer_group['prices']['prices_dopin']);

        $merge_price = 0;
        $merge_price_first = 3;
        $merge_first_column_height = $column_height;
        $merge_column_height = $column_height;
        $ln = 1;
        if(count($prices_dopin)==2){
           $merge_price = 1;
           $merge_price_first = 1;  
           $merge_first_column_height = $column_height;
           $merge_column_height = $column_height;
           $ln = 1;
        }elseif (count($prices_dopin)==3) {
           $merge_price = 0; 
           $merge_price_first = 1; 
           $merge_first_column_height = $column_height;
           $merge_column_height = $column_height/2;
           $ln = 2;
        }elseif (count($prices_dopin)==4) {
           $merge_price = 0;  
           $merge_price_first = 0;
           $merge_first_column_height = $column_height/2;
           $merge_column_height = $column_height/2;
           $ln = 2;
        }
        $merge_price = $merge_price*($width[4]);
        $merge_price_first = $merge_price_first*($width[4]);

        $merge_name_0 = $width[0] + ($width[1]*3) + $width[2] + $width[3];
        $pdf->Cell($merge_name_0,$column_height,iconv('utf-8', 'windows-1251',''),1,0,'C',0);
		
		
        
        $count_f = 1;
        foreach ($prices_dopin as $key => $value) {
            $customer_group_dopin = $this->model_customer_customer_group->getCustomerGroup($value);
			      $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cg.customer_group_id = '" . (int)$value . "' AND cgd.language_id = '" . 5 . "'");	
				
			      $group_ukr = $query->row[name];
            if($count_f==1){
              $pdf->Cell(($width[4]*2) + ($merge_price_first*2),$column_height,iconv('utf-8', 'windows-1251',$group_ukr),1,0,'C',0);
              $count_f = 2;
            }else{
              $pdf->Cell(($width[4]*2) + ($merge_price*2),$column_height,iconv('utf-8', 'windows-1251',$group_ukr),1,0,'C',0); 
            }
        }
        $pdf->Ln();

        $merge_name = 0;
        $pdf->Cell($width[0],$column_height,iconv('utf-8', 'windows-1251',''),1,0,'C',0);
        if($customer_group['prices']['prices_code']=='1'){
           $pdf->Cell($width[1],$column_height,iconv('utf-8', 'windows-1251',$this->language->get('text_p_code')),1,0,'C',0);
        }else{
           $merge_name = $merge_name + $width[1];
        }
        if($customer_group['prices']['prices_articul']=='1'){
           $pdf->Cell($width[1],$column_height,iconv('utf-8', 'windows-1251',$this->language->get('text_p_sku')),1,0,'C',0);
        }else{
           $merge_name = $merge_name + $width[1];
        }

        $merge_name = $merge_name + $width[1];

        if($customer_group['prices']['prices_img']=='1'){
           $pdf->Cell($width[2],$column_height,iconv('utf-8', 'windows-1251',$this->language->get('text_p_image')),1,0,'C',0);
        }else{
           $merge_name = $merge_name + $width[2];
        }

        $pdf->Cell($width[3] + $merge_name,$column_height,iconv('utf-8', 'windows-1251',$this->language->get('text_p_name')),1,0,'C',0);

        $x=$pdf->GetX();
        $y=$pdf->GetY();
        $count_f = 1;
        foreach ($prices_dopin as $key => $value) {
        	    $x=$pdf->GetX();
                $y=$pdf->GetY();
                if($count_f==1){
                    if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                      $pdf->MultiCell($width[4] + $merge_price_first,$merge_first_column_height,iconv('utf-8', 'windows-1251',$name_price_1),1,'C',0);
                      $pdf->SetXY($x+$width[4]+ $merge_price_first,$y);
                      $pdf->MultiCell($width[4] + $merge_price_first,$merge_first_column_height,iconv('utf-8', 'windows-1251',$name_price_2),1,'C',0);
                      $pdf->SetXY($x+($width[4]*2)+ ($merge_price_first*2),$y);
                      $count_f = 2;
                    } else {
                      $pdf->MultiCell(($width[4]*3) + $merge_price_first,$merge_first_column_height,iconv('utf-8', 'windows-1251',$name_price_2),1,'C',0);
                      $pdf->SetXY($x+($width[4]*3)+ $merge_price_first,$y);
                      // $pdf->MultiCell($width[4] + $merge_price_first,$merge_first_column_height,iconv('utf-8', 'windows-1251',$name_price_2),1,'C',0);
                      // $pdf->SetXY($x+($width[4]*2)+ ($merge_price_first*2),$y);
                      $count_f = 2;
                    }
                }else{
                    if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                      $pdf->MultiCell($width[4] + $merge_price,$merge_column_height,iconv('utf-8', 'windows-1251',$name_price_1),1,'C',0);
                      $pdf->SetXY($x+$width[4]+ $merge_price,$y);
                      $pdf->MultiCell($width[4] + $merge_price,$merge_column_height,iconv('utf-8', 'windows-1251',$name_price_2),1,'C',0);
                      $pdf->SetXY($x+($width[4]*2)+ ($merge_price*2),$y);
                    } else {
                      $pdf->MultiCell(($width[4]*3) + $merge_price,$merge_column_height,iconv('utf-8', 'windows-1251',$name_price_2),1,'C',0);
                      $pdf->SetXY($x+($width[4]*3)+ $merge_price,$y);
                      // $pdf->MultiCell($width[4] + $merge_price,$merge_column_height,iconv('utf-8', 'windows-1251',$name_price_2),1,'C',0);
                      // $pdf->SetXY($x+($width[4]*2)+ ($merge_price*2),$y);
                    }
                }
        }
        if($ln==2){
            $pdf->Ln();
            $pdf->Ln();
        }else{
            $pdf->Ln();
        }

        //-----data---------------
        $prices_ids = explode(',', $customer_group['prices']['prices_ids']);
        $sort_by = $customer_group['prices']['prices_sort'];
        /*$prices_model1 = $customer_group['prices']['prices_model1'];
        $manufacturer_id = $customer_group['prices']['prices_model2'];
        $sklad = $customer_group['prices']['prices_sklad'];*/
        foreach ($prices_ids as $key => $value) {
        	$category_info = $this->model_catalog_category->getCategory($value);
            $category_line = '';
            $count = 0;
            $results1 = $this->model_catalog_category->getCategoriesClearPrice($category_info['category_id'],1000, $sort_by);
            foreach ($results1 as $key_c1 => $value_c1) {
                $results2 = $this->model_catalog_category->getCategoriesClearPrice($value_c1['category_id'],1000, $sort_by);
                foreach ($results2 as $key_c2 => $value_c2) {
                    if($count!=0){
                       $category_line .= ',';
                    }
                    $category_line .= $value_c2['category_id'];
                    $count = 1;
                }
            }
            if($category_line!=''){
               $results_products = $this->model_catalog_category->getProductsClearPrice($category_line, $sort_by);
            }

            if(count($results_products)>0){
        	   $pdf->SetDrawColor(0,0,0);
        	   $pdf->SetFillColor(200,200,200);
        	   $pdf->SetFontSize(14);
        	   $x=$pdf->GetX();
               $y=$pdf->GetY();
        	
               if($customer_group['prices']['prices_imgmark']=='1'){
                  $pdf->Cell(0,50,'                     '.$category_info['name'],1,0,'L',1);
                  $src = $_SERVER['DOCUMENT_ROOT'].'/image/'.$category_info['image'];
                  $pdf->Image($src,$x+10,$y+5,40);
                }else{
            	  $pdf->Cell(0,50,$category_info['name'],1,0,'L',1);
                }
                $pdf->Ln();

        	 
        	  $pdf->SetFontSize(7);
        	  foreach ($results_products as $key => $value) {
				  //$name_ukr = $this->model_catalog_product->getProduct($value['product_id']);
				  $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$value['product_id'] . "' AND pd.language_id = '" . 5 . "'");

					$name_ukr = $query->row['name'];
                    $continue = 0;
                    //foreach ($value['options'] as $key_o1 => $value_o1) {
                    //    if($value_o1['option_id']=='16'){
                    //        if(count($value_o1['product_option_value'])==0 and $manufacturer_id==0){
                    //           $continue=1;
                    //        }
                    //    }
                    //}
                    if(count($value['options'])==0 and $manufacturer_id==0){
                       $continue=1;
                    }
                    if($continue==1){
                        continue;
                    }
                    if($sort_by==1){
                        if($subcat!=$value['cat_2']['name']){
                            $subcat = $value['cat_2']['name'];
                            $pdf->SetDrawColor(0,0,0);
                            $pdf->SetFontSize(12);
                            $x=$pdf->GetX();
                            $y=$pdf->GetY();
                            $pdf->Cell(0,$column_height,$category_info['name'].' '.$subcat,1,0,'L',0);
                            $pdf->Ln();
                            $pdf->SetFontSize(7);
                        }
                    }
                    $pdf->Cell($width[0],$column_height,iconv('utf-8', 'windows-1251',''),1,0,'C',0);
                    $merge_column_height_name_count = 1;
                    if($customer_group['prices']['prices_code']=='1'){
                        $pdf->Cell($width[1],$column_height,iconv('utf-8', 'windows-1251',$value['model']),1,0,'C',0);
                        $merge_column_height_name_count = $merge_column_height_name_count + 1;
                    }
                    if($customer_group['prices']['prices_articul']=='1'){
                        $pdf->Cell($width[1],$column_height,iconv('utf-8', 'windows-1251',$value['sku']),1,0,'C',0);
                        $merge_column_height_name_count = $merge_column_height_name_count + 1;
                    }
                    if($customer_group['prices']['prices_img']=='1'){
                       $x=$pdf->GetX();
                       $y=$pdf->GetY();
                       $pdf->Cell($width[2],$column_height,'',1,0,'C',0);
                       $x_n=$pdf->GetX();
                       $y_n=$pdf->GetY();
                       $src = $_SERVER['DOCUMENT_ROOT'].'/image/'.$value['image'];
                       $pdf->Image($src,$x+15,$y+2,30);
                       $merge_column_height_name_count = $merge_column_height_name_count + 1;
                    }else{
                       $x_n=$pdf->GetX();
                       $y_n=$pdf->GetY();
                    }

                    $x=$pdf->GetX();
                    $y=$pdf->GetY();
                    $sa = iconv('utf-8', 'windows-1251//IGNORE',trim($name_ukr));

                    if($merge_column_height_name_count==1 or $merge_column_height_name_count==2 or $merge_column_height_name_count==3){
                    	$merge_column_height_name = $column_height;
                        $strlen = 63;
                    }elseif($merge_column_height_name_count==4){
                    	$merge_column_height_name = $column_height;
                        $strlen = 53;
                    }else{
                    	$merge_column_height_name = $column_height;
                        $strlen = 63;
                    }
                    if(strlen($sa)>$strlen){
                        $sa = substr($sa, 0,$strlen);
                    }
                    $pdf->MultiCell($width[3] + $merge_name,$merge_column_height_name,$sa,1,'L',0);
                    $pdf->SetXY($x_n+$width[3]+ $merge_name,$y_n);
                    
                    $count_f = 1;
                    foreach ($prices_dopin as $keydopin => $valuedopin) {
        	            //if($valuedopin!=$customer_group_id){
                        // $pdf->MultiCell(($width[4]*3) + $merge_price,$merge_column_height,iconv('utf-8', 'windows-1251',$name_price_2),1,'C',0);
                        // $pdf->SetXY($x+($width[4]*3)+ $merge_price,$y);
                            if($count_f==1){
                              if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                $x1=$pdf->GetX();
                                $y1=$pdf->GetY();
                                $pdf->Cell($width[4]+ $merge_price_first,$column_height,'',1,0,'C',0);
                                $x2=$pdf->GetX();
                                $y2=$pdf->GetY();
                                $pdf->Cell($width[4]+ $merge_price_first,$column_height,'',1,0,'C',0);
                              } else {
                                // $x1=$pdf->GetX();
                                // $y1=$pdf->GetY();
                                // $pdf->Cell($width[4]+ $merge_price_first,$column_height,'',1,0,'C',0);
                                $x2=$pdf->GetX();
                                $y2=$pdf->GetY();
                                $pdf->Cell(($width[4]*3)+ $merge_price_first,$column_height,'',1,0,'C',0);
                              }
                            }else{
                              if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                $x1=$pdf->GetX();
                                $y1=$pdf->GetY();
                                $pdf->Cell($width[4]+ $merge_price,$column_height,'',1,0,'C',0);
                                $x2=$pdf->GetX();
                                $y2=$pdf->GetY();
                                $pdf->Cell($width[4]+ $merge_price,$column_height,'',1,0,'C',0); 
                              } else {
                                // $x1=$pdf->GetX();
                                // $y1=$pdf->GetY();
                                // $pdf->Cell($width[4]+ $merge_price,$column_height,'',1,0,'C',0);
                                $x2=$pdf->GetX();
                                $y2=$pdf->GetY();
                                $pdf->Cell(($width[4]*3)+ $merge_price,$column_height,'',1,0,'C',0); 
                              }
                            }
                            foreach ($value['options'] as $key_o1 => $value_o1) {
                	            // if($value_o1['option_id']=='16'){
                                    foreach ($value_o1['product_option_value'] as $key_o2 => $value_o2) {
                                        if(!in_array($valuedopin, $prices_dopin_poask)){
                                          $price = round($value_o2['price'][$valuedopin]);
                                        }else{
                                          $price = iconv('utf-8', 'windows-1251//IGNORE',$this->language->get('text_request'));  
                                        }
                                        // if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                        if ($customer_group['prices']['gart_enabled'] == 1 && $value_o2['option_value_id'] == $customer_group['prices']['gart_optid']) {
                                        // if($value_o2['option_value_id']=='60'){
                                            if($count_f==1){
                                        	   $pdf->SetXY($x1,$y1);
                                    	       $pdf->Cell($width[4]+ $merge_price_first,$column_height,$price,0,0,'C',0);
                                               //$count_f = 2;
                                            }else{
                                               $pdf->SetXY($x1,$y1);
                                               $pdf->Cell($width[4]+ $merge_price,$column_height,$price,0,0,'C',0); 
                                            }
                                        }else{
                                            if($count_f==1) {
                                              if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                                $pdf->SetXY($x2,$y2);
                                                $pdf->Cell($width[4]+ $merge_price_first,$column_height,$price,0,0,'C',0);
                                              } else {
                                                $pdf->SetXY($x2,$y2);
                                                $pdf->Cell(($width[4]*3)+$merge_price_first,$column_height,$price,0,0,'C',0);
                                              }
                                               //$count_f = 2;
                                            } else {
                                              if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                                $pdf->SetXY($x2,$y2);
                                                $pdf->Cell($width[4]+ $merge_price,$column_height,$price,0,0,'C',0);
                                              } else {
                                                $pdf->SetXY($x2,$y2);
                                                $pdf->Cell(($width[4]*3)+ $merge_price,$column_height,$price,0,0,'C',0);
                                              }
                                            }
                                        }
                                    }
                                //}
                            }
                            
        	            //}
                            if($count_f==1){
                                $count_f = 2;
                            }
        	        }

                    $pdf->Ln();
        	  }
        	}
        }
        //-----data---------------
        $directory = '/prices/'.$customer_group_id.'/';
        
        if($customer_group['prices']['prices_delete']=='1'){
            $dir = opendir($_SERVER['DOCUMENT_ROOT'].$directory);
            while(($file = readdir($dir))){
                if(is_dir($_SERVER['DOCUMENT_ROOT'].$directory.$file)){
                	if($file != "." && $file != ".."){
                		$dir2 = opendir($_SERVER['DOCUMENT_ROOT'].$directory.$file);
                	    while(($file2 = readdir($dir2))){
                            if((is_file($_SERVER['DOCUMENT_ROOT'].$directory.$file.'/'.$file2))){
                                list($n1,$n2) = explode('.', $_SERVER['DOCUMENT_ROOT'].$directory.$file.'/'.$file2);
                                if($n2=='pdf'){
                                   unlink($_SERVER['DOCUMENT_ROOT'].$directory.$file.'/'.$file2);
                                }
                            }
                	    }
                	    if($n2=='pdf'){
                	       rmdir($_SERVER['DOCUMENT_ROOT'].$directory.$file);
                	    }
                	}
                }
            }
        }
       
        $rand = rand(0,9999999999);
        mkdir($_SERVER['DOCUMENT_ROOT'].$directory.$rand);
        $file_data = $directory.$rand.'/'.$nn_file;
        
        $datar['prices_date_pdf'] = date('Y-m-d');
        $datar['prices_last_file_pdf'] = $file_data;
        $this->model_pdfexcelimport_pdfexcelimport->editPriceSettings($customer_group_id, $datar);

        $pdf->Output($_SERVER['DOCUMENT_ROOT'].$file_data, "F" );

        //PDF-------------------------------------

    	  $success = 2;

		    $this->response->redirect($this->url->link('pdfexcelimport/pdfexcelimport', 'user_token=' . $this->session->data['user_token'].'&success='.$success, true));
    }

    public function reload_excel(){
    	ini_set('error_reporting', E_ALL);
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      set_time_limit (60);
      ini_set('memory_limit', '5512M');

      $this->load->language('pdfexcelimport/pdfexcelimport');

      $this->document->setTitle($this->language->get('text_title'));

		  $this->load->model('pdfexcelimport/pdfexcelimport');
		  $this->load->model('customer/customer_group');
		  $this->load->model('catalog/category');

		  $customer_group_id = $this->request->get['customer_group_id'];
		  $customer_group = $this->model_customer_customer_group->getCustomerGroup($customer_group_id);
		  $prices = $this->model_pdfexcelimport_pdfexcelimport->getPriceSettings($customer_group['customer_group_id']);
		  if(isset($prices['prices_ids'])){
		   $prices_ids = explode(',', $prices['prices_ids']);
		  }
		  if(isset($prices['prices_dopin'])){
		   $prices_dopin = explode(',', $prices['prices_dopin']);
		  }
        if(isset($prices['prices_dopin_poask'])){
           $prices_dopin_poask = explode(',', $prices['prices_dopin_poask']);
        }
		  $customer_group['prices'] = $prices;

      $prices_dopin_poask = explode(',', $customer_group['prices']['prices_dopin_poask']);

      $nn = $customer_group['prices']['prices_filename'];

        //EXCEL-------------------------------------
        $nn_file = $customer_group['prices']['prices_filename'];
        if($customer_group['prices']['prices_dateadd']=='1') {
        	$nn_file .= '_'.date("d_m_Y");
        }
        $nn_file .= '.xls';

		    $this->load->model('pdfexcelimport/excel');
		    $objPHPExcel = new ModelPdfexcelimportExcel();
            
        $left = array(
            'alignment'=>array(
                'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_TOP
            )
        );
        $left2 = array(
            'alignment'=>array(
                'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $center = array(
            'alignment'=>array(
                'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $bckg = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000')
            )
        );
        $bckg_category = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'c0c0c0')
            )
        );
        $borders = array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            ),
        );
        $font = array(
        	'font'=>array(
                'name'  => 'Arial',
                'size'  => 10,
                'bold'  => true,
	              'italic'=> false,
	              'color' => array(
		              'rgb' => 'FFFFFF'
	               ),
	        ),
        );
        $font_header = array(
        	'font'=>array(
                'name'  => 'Arial',
                'size'  => 8,
                'bold'  => true,
	            'italic'=> false,
	            'color' => array(
		              'rgb' => '000000'
	                ),
	        ),
        );
        $font_in = array(
        	'font'=>array(
                'name'  => 'Arial',
                'size'  => 8,
                'bold'  => false,
	            'italic'=> false,
	            'color' => array(
		              'rgb' => '000000'
	                ),
	        ),
        );
        $font_category = array(
        	'font'=>array(
                'name'  => 'Arial',
                'size'  => 16,
                'bold'  => true,
	            'italic'=> false,
	            'color' => array(
		              'rgb' => '000000'
	                ),
	        ),
        );
        $font_category2 = array(
            'font'=>array(
                'name'  => 'Arial',
                'size'  => 14,
                'bold'  => true,
                'italic'=> false,
                'color' => array(
                      'rgb' => '000000'
                    ),
            ),
        );

        $objPHPExcel->setMeta();
        $objPHPExcel->setTitleSheet($nn);
              
        //-----header--------   
        $column_merge = 3;  
        if($customer_group['prices']['prices_code']=='1'){
          $column_merge = $column_merge + 1;
        }
        if($customer_group['prices']['prices_articul']=='1'){
          $column_merge = $column_merge + 1;
        }
        if($customer_group['prices']['prices_img']=='1'){
          $column_merge = $column_merge + 1;
        }
        $prices_dopin = explode(',', $customer_group['prices']['prices_dopin']);
        foreach ($prices_dopin as $key => $value) {
        	if($value!=$customer_group['customer_group_id']){
               $column_merge = $column_merge + 2;
        	}
        }
        if($column_merge<13){
          if($customer_group['prices']['gart_enabled']=='1'){
        	  $column_merge = 13;
          } else {
            $column_merge = 9;
          }
        }

        if($customer_group['prices']['gart_enabled']=='1') {
          $objPHPExcel->mergeCells(0,1,8,7);
          $infocell = 9;
        } else {
          $objPHPExcel->mergeCells(0,1,7,6);
          $infocell = 8;
        }
        
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(15);
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(15);
        $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(15);
        $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(15);
        $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(15);
        $objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(15);

        $src = $_SERVER['DOCUMENT_ROOT'].'/image/logoexcel.png';
        $objPHPExcel->insertImg($this->language->get('text_image_title'), $src, 0, 10, 120, 120, 'A1');                     
        $objPHPExcel->mergeCells($infocell,1,$column_merge,1);
        $objPHPExcel->mergeCells($infocell,2,$column_merge,2);
        $objPHPExcel->setValue($infocell,2,$this->config->get('config_telephone'));
        $objPHPExcel->mergeCells($infocell,3,$column_merge,3);
        $objPHPExcel->setValue($infocell,3,'');
        $objPHPExcel->mergeCells($infocell,4,$column_merge,4);
        $objPHPExcel->setValue($infocell,4,'');
        $objPHPExcel->mergeCells($infocell,5,$column_merge,5);
        $objPHPExcel->setValue($infocell,5,$this->config->get('config_email'));
        $objPHPExcel->mergeCells($infocell,6,$column_merge,6);
        $objPHPExcel->setValue($infocell,6,'       pixsel.glass');
        $objPHPExcel->mergeCells($infocell,7,$column_merge,7);

        $src = $_SERVER['DOCUMENT_ROOT'].'/image/insta.png';
        if($customer_group['prices']['gart_enabled']=='1') {
          $objPHPExcel->insertImg($this->language->get('text_insta_title'), $src, 0, 4, 18, 18, 'J6');
        } else {
          $objPHPExcel->insertImg($this->language->get('text_insta_title'), $src, 0, 4, 18, 18, 'I6');
        }

        $objPHPExcel->styleInRows(1,7,$left);
        $objPHPExcel->styleInRows(1,7,$bckg);
        $objPHPExcel->styleInRows(1,7,$font);
        $objPHPExcel->styleInRows(1,7,$borders);
        $objPHPExcel->textWrapInRow(1);
        //-----header--------

        
        //-----table header--------
        $row = 8;
        $column = 1;
        $merge_name = 0;
        $objPHPExcel->setValue(0,$row,'');
        $objPHPExcel->setValue(0,($row+1),' ');
        $objPHPExcel->setColumnWidth(0, "5");
        if($customer_group['prices']['prices_code']=='1'){
           $objPHPExcel->setValue($column,$row,'');
           $objPHPExcel->setValue($column,($row+1),$this->language->get('text_p_code'));
           $objPHPExcel->setColumnWidth($column, "12");
           $column = $column + 1;
        }else{
           $merge_name = $merge_name + 1;
        }
        if($customer_group['prices']['prices_articul']=='1'){
           $objPHPExcel->setValue($column,$row,'');
           $objPHPExcel->setValue($column,($row+1),$this->language->get('text_p_sku'));
           $objPHPExcel->setColumnWidth($column, "8");
           $column = $column + 1;
        }else{
           $merge_name = $merge_name + 1;
        }

        $merge_name = $merge_name + 1;
  
        if($customer_group['prices']['prices_img']=='1'){
           $objPHPExcel->setValue($column,$row,'');
           $objPHPExcel->setValue($column,($row+1),$this->language->get('text_p_image'));
           $objPHPExcel->setColumnWidth($column, "15");
           $column = $column + 1;
        }else{
           $merge_name = $merge_name + 1;
        }

        if($merge_name!=0){
        	$objPHPExcel->mergeCells($column,$row,($column+$merge_name),$row);
        	$objPHPExcel->mergeCells($column,($row+1),($column+$merge_name),($row+1));
        }
        $objPHPExcel->setValue($column,$row,'');
        $objPHPExcel->setValue($column,($row+1),$this->language->get('text_p_name'));
        $objPHPExcel->setColumnWidth($column, "34");
        $column = $column + $merge_name + 1;

        
        $merge_price = 0;
        $merge_price_first = 3;
        $prices_dopin = explode(',', $customer_group['prices']['prices_dopin']);
        if(count($prices_dopin)==2){
           $merge_price = 1;
           $merge_price_first = 1;  
        }elseif (count($prices_dopin)==3) {
           $merge_price = 0; 
           $merge_price_first = 1; 
        }elseif (count($prices_dopin)==4) {
           $merge_price = 0;  
           $merge_price_first = 0;
        }
        $name_price_1 = $this->language->get('text_sklo_ga');
        $name_price_2 = $this->language->get('text_sklo_gm');

        $count_f = 1;
        foreach ($prices_dopin as $key => $value) {
        		$customer_group_dopin = $this->model_customer_customer_group->getCustomerGroup($value);	

				    $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cg.customer_group_id = '" . (int)$value . "' AND cgd.language_id = '" . 1 . "'");	
				
				    $group_ukr = $query->row[name];
				    //$this->console_log($group_ukr);
				
            $column_work = $column;
            if($count_f==1){                   
                    if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                      $objPHPExcel->mergeCells($column_work,$row,($column_work+($merge_price_first*2)+1),$row);
                      $objPHPExcel->mergeCells($column_work,($row+1),($column_work+$merge_price_first),($row+1));
                      $objPHPExcel->mergeCells(($column_work+$merge_price_first+1),($row+1),($column_work+($merge_price_first*2)+1),($row+1));
                      $objPHPExcel->setValue($column_work,$row,$group_ukr);//$customer_group_dopin['name']  

                      $objPHPExcel->setValue($column_work,($row+1),$name_price_1);
                      $objPHPExcel->setValue(($column_work+1+$merge_price_first),($row+1),$name_price_2);

                      $objPHPExcel->styleInCell(($column_work+4+$merge_price_first),$row,$borders);

                      $count_f = 2;
                      $column = $column + ($merge_price_first*2) + 2;
                    } else {
                      // $objPHPExcel->mergeCells($column_work,$row,($column_work+($merge_price_first*1)+1),$row);
                      $objPHPExcel->mergeCells($column_work,$row,($column_work+$merge_price_first),$row);
                      $objPHPExcel->mergeCells($column_work,($row+1),($column_work+$merge_price_first),($row+1));
                      // $objPHPExcel->mergeCells(($column_work+$merge_price_first+1),($row+1),($column_work+($merge_price_first*1)+1),($row+1));
                      $objPHPExcel->setValue($column_work,$row,$group_ukr);//$customer_group_dopin['name']  

                      $objPHPExcel->setValue($column_work,($row+1),$name_price_2);
                      // $objPHPExcel->setValue(($column_work+1+$merge_price_first),($row+1),$name_price_2);

                      $objPHPExcel->styleInCell(($column_work+$merge_price_first),$row,$borders);

                      $count_f = 2;
                      $column = $column + ($merge_price_first*1) + 1;
                    }
            }else{  
                    if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                      $objPHPExcel->mergeCells($column_work,$row,($column_work+($merge_price*2)+1),$row);
                      $objPHPExcel->mergeCells($column_work,($row+1),($column_work+$merge_price),($row+1));
                      $objPHPExcel->mergeCells(($column_work+$merge_price+1),($row+1),($column_work+($merge_price*2)+1),($row+1));
                      $objPHPExcel->setValue($column_work,$row,$group_ukr);//$customer_group_dopin['name']  

                      $objPHPExcel->setValue($column_work,($row+1),$name_price_1);
                      $objPHPExcel->setValue(($column_work+1+$merge_price),($row+1),$name_price_2);

                      $objPHPExcel->styleInCell(($column_work+2+$merge_price),$row,$borders);

                      $column = $column + 2;
                    } else {
                      // $objPHPExcel->mergeCells($column_work,$row,($column_work+($merge_price*2)+1),$row);
                      $objPHPExcel->mergeCells($column_work,$row,($column_work+$merge_price),$row);
                      $objPHPExcel->mergeCells($column_work,($row+1),($column_work+$merge_price),($row+1));
                      // $objPHPExcel->mergeCells(($column_work+$merge_price+1),($row+1),($column_work+($merge_price*2)+1),($row+1));
                      $objPHPExcel->setValue($column_work,$row,$group_ukr);//$customer_group_dopin['name']  

                      $objPHPExcel->setValue($column_work,($row+1),$name_price_2);
  
                      $objPHPExcel->styleInCell(($column_work+$merge_price),$row,$borders);
  
                      $column = $column + 1;
                    }
            }

            $objPHPExcel->setColumnWidth($column_work, "12");
            $objPHPExcel->setColumnWidth(($column_work+1), "12");
            $objPHPExcel->styleInCell($column_work,$row,$borders);
            $objPHPExcel->styleInCell(($column_work+1),$row,$borders);

        }

        $objPHPExcel->styleInRow($row,$center);
        $objPHPExcel->styleInRow($row,$font_header);
        $objPHPExcel->styleInRow($row,$borders);
        $objPHPExcel->styleInRow(($row+1),$borders);
        $objPHPExcel->styleInRow(($row+1),$center);
        $objPHPExcel->styleInRow(($row+1),$font_header);
        $objPHPExcel->textWrapInRow(($row+1));
        $objPHPExcel->freeze(1,($row+2));
        //-----table header--------

        //-----data---------------
        $row = $row + 2;
        $prices_ids = explode(',', $customer_group['prices']['prices_ids']);
        $sort_by = $customer_group['prices']['prices_sort'];

        foreach ($prices_ids as $key => $value) {
        	$category_info = $this->model_catalog_category->getCategory($value);

          $category_line = '';
          $count = 0;
          $results1 = $this->model_catalog_category->getCategoriesClearPrice($category_info['category_id'],1000, $sort_by);

          foreach ($results1 as $key_c1 => $value_c1) {
            $results2 = $this->model_catalog_category->getCategoriesClearPrice($value_c1['category_id'],1000, $sort_by);
            foreach ($results2 as $key_c2 => $value_c2) {
              // print_r($value_c2); exit;
              if($count!=0){
                $category_line .= ',';
              }
              $category_line .= $value_c2['category_id'];
              $count = 1;
            }
         }

            if($category_line!=''){
               $results_products = $this->model_catalog_category->getProductsClearPrice($category_line, $sort_by);
            }

            if(count($results_products)>0){
                if($customer_group['prices']['gart_enabled']=='1') {
                  $objPHPExcel->mergeCells(1,$row,13,$row);
                } else {
                  $objPHPExcel->mergeCells(1,$row,9,$row);
                }

                if($customer_group['prices']['prices_imgmark']=='1'){
        	        $objPHPExcel->setValue(1,$row,'                         '.$category_info['name']);

        	        $src = $_SERVER['DOCUMENT_ROOT'].'/image/'.$category_info['image'];
                    $objPHPExcel->insertImg($category_info['name'], $src, 10, 15, 50, 50, 'B'.$row);
                    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                }else{
            	    $objPHPExcel->setValue(1,$row,$category_info['name']);
                }

        	    $objPHPExcel->styleInRow($row,$bckg_category);
        	    $objPHPExcel->styleInRow($row,$font_category);
        	    $objPHPExcel->styleInRow($row,$borders);
        	    $objPHPExcel->styleInRow($row,$left2);

        	    $row = $row + 1;
                $subcat = '';
        	    foreach ($results_products as $key => $value) {
                    $continue = 0;
                    //foreach ($value['options'] as $key_o1 => $value_o1) {
                    //    if($value_o1['option_id']=='16'){
                    //        if(count($value_o1['product_option_value'])<3 and $manufacturer_id==0){
                    //           $continue=1;
                    //        }
                    //    }
                    //}
                    
                    // if(count($value['options'])==0 and $manufacturer_id==0){
                      if(count($value['options'])==0){
                       $continue=1;
                    }
                    if($continue==1){
                        continue;
                    }
                    // echo $category_info['name'].' '.$subcat; exit;
                    if($sort_by==1){
                        if($subcat!=$value['cat_2']['name']){
                            $subcat = $value['cat_2']['name'];
                            $objPHPExcel->mergeCells(1,$row,13,$row);
                            $objPHPExcel->setValue(1,$row,$category_info['name'].' '.$subcat);
                            $objPHPExcel->styleInRow($row,$font_category2);
                            $objPHPExcel->styleInRow($row,$borders);
                            $objPHPExcel->styleInRow($row,$left2);
                            $row = $row + 1;
                        }
                    }
        		        
                    $objPHPExcel->setValue(0,$row,'');
                    $column = 1;
                    $column_img = 'B';
                    $merge_name = 0;
                    if($customer_group['prices']['prices_code']=='1'){
                       $objPHPExcel->setValue($column,$row,$value['model']);
                       $objPHPExcel->styleInCell($column,$row,$center);
                       $column_img = 'C';
                       $column = $column + 1;
                    }else{
                       $merge_name = $merge_name + 1;
                    }
                    if($customer_group['prices']['prices_articul']=='1'){
                       $objPHPExcel->setValue($column,$row,$value['sku']);
                       $objPHPExcel->styleInCell($column,$row,$center);
                       $column_img = 'C';
                       $column = $column + 1;
                    }else{
                       $merge_name = $merge_name + 1;
                    }
                    if($customer_group['prices']['prices_code']=='1' && $customer_group['prices']['prices_articul']=='1'){
                      $column_img = 'D';
                    }
                    //if($customer_group['prices']['prices_class']=='1'){
                    //   $column_img = 'E';
                       // $objPHPExcel->setValue($column,$row,$value['manufacturer_name']);
                    //   $column = $column + 1;
                    //}else{
                    $merge_name = $merge_name + 1;
                    //}
                    if($customer_group['prices']['prices_img']=='1'){
                       $src = $_SERVER['DOCUMENT_ROOT'].'/image/'.$value['image'];
                       $objPHPExcel->insertImg($value['name'], $src, 10, 10, 50, 50, $column_img.$row);
                       $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(50);
                       $column = $column + 1;
                    }else{
                       $merge_name = $merge_name + 1;
                    }
                    if($merge_name!=0){
        	           $objPHPExcel->mergeCells($column,$row,($column+$merge_name),$row);
                    }
					//$this->console_log($value);
					
					$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$value['product_id'] . "' AND pd.language_id = '" . 1 . "'");

					$name_ukr = $query->row['name'];
					
                    $objPHPExcel->setValue($column,$row, $name_ukr);
                    $objPHPExcel->styleInCell($column,$row,$left2);
                    $column = $column + $merge_name + 1;

                    // $objPHPExcel->styleInRow($row,$left2);
                    $objPHPExcel->styleInRow($row,$font_in);
                    $objPHPExcel->styleInRow($row,$borders);

                    $count_f = 1;
                    foreach ($prices_dopin as $keydopin => $valuedopin) {
                            if($count_f==1){
                              if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                $objPHPExcel->mergeCells($column,$row,($column+$merge_price_first),$row);
                                $objPHPExcel->mergeCells($column+1+$merge_price_first,$row,($column+1+($merge_price_first)*2),$row);
                              } else {
                                $objPHPExcel->mergeCells($column,$row,($column+$merge_price_first),$row);
                                $objPHPExcel->mergeCells($column+1+$merge_price_first,$row,($column+1+($merge_price_first)*2),$row);
                              }
                            }else{
                              if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                $objPHPExcel->mergeCells($column,$row,($column+$merge_price),$row);
                                $objPHPExcel->mergeCells($column+1+$merge_price,$row,($column+1+($merge_price)*2),$row);
                              } else {
                                $objPHPExcel->mergeCells($column,$row,($column+$merge_price),$row);
                                $objPHPExcel->mergeCells($column+1+$merge_price,$row,($column+1+($merge_price)*2),$row);
                              }
                            }
        	       	        
                            foreach ($value['options'] as $key_o1 => $value_o1) {
                	            /*if($value_o1['option_id']=='16'){*/
                                    foreach ($value_o1['product_option_value'] as $key_o2 => $value_o2) {
                                        if(!in_array($valuedopin, $prices_dopin_poask)){
                                          $price = round($value_o2['price'][$valuedopin]);
                                        }else{
                                          $price = $this->language->get('text_request');  
                                        }
                                        // if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                        if($customer_group['prices']['gart_enabled'] == 1 && $value_o2['option_value_id']==$customer_group['prices']['gart_optid']) {
                                            $objPHPExcel->setValue($column,$row,$price);
                                            $objPHPExcel->styleInCell($column,$row,$center);
                                        } else {
                                            if($count_f==1) {
                                              if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                                $objPHPExcel->setValue(($column+1+$merge_price_first),$row,$price);
                                                $objPHPExcel->styleInCell(($column+4+$merge_price_first),$row,$borders);
                                                $objPHPExcel->styleInCell(($column+1+$merge_price_first),$row,$center);
                                              } else {
                                                $objPHPExcel->setValue(($column),$row,$price);
                                                $objPHPExcel->styleInCell(($column+2+$merge_price_first),$row,$borders);
                                                $objPHPExcel->styleInCell(($column),$row,$center);
                                              }
                                            } else {
                                              if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                                $objPHPExcel->setValue(($column+1+$merge_price),$row,$price);
                                                $objPHPExcel->styleInCell(($column+2+$merge_price),$row,$borders);
                                                $objPHPExcel->styleInCell(($column+1+$merge_price),$row,$center);
                                              } else {
                                                $objPHPExcel->setValue(($column+1),$row,$price);
                                                $objPHPExcel->styleInCell(($column+1+$merge_price),$row,$borders);
                                                $objPHPExcel->styleInCell(($column+1),$row,$center);
                                              }
                                            }
                                        }
//}
                                }
                            }
                            if($count_f==1){
                              if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                $count_f = 2;
                                $column = $column + ($merge_price_first*2) + 2;
                              } else {
                                $count_f = 2;
                                $column = $column + 1;
                              }
                            }else{
                              if ($customer_group['prices']['gart_enabled'] == 1 && $customer_group['prices']['gart_optid'] > 0) {
                                $column = $column + 2;
                              } else {
                                $column = $column + 1;
                              }
                            }
        	        }
        		    $row = $row + 1;
        	    }
        	}
        }
        //-----data---------------

        //$objPHPExcel->autoWidth();
        $directory = '/prices/'.$customer_group_id.'/';
        
        if($customer_group['prices']['prices_delete']=='1'){
            $dir = opendir($_SERVER['DOCUMENT_ROOT'].$directory);
            while(($file = readdir($dir))){
                if(is_dir($_SERVER['DOCUMENT_ROOT'].$directory.$file)){
                	if($file != "." && $file != ".."){
                		$dir2 = opendir($_SERVER['DOCUMENT_ROOT'].$directory.$file);
                	    while(($file2 = readdir($dir2))){
                            if((is_file($_SERVER['DOCUMENT_ROOT'].$directory.$file.'/'.$file2))){
                                list($n1,$n2) = explode('.', $_SERVER['DOCUMENT_ROOT'].$directory.$file.'/'.$file2);
                                if($n2=='xls'){
                                   unlink($_SERVER['DOCUMENT_ROOT'].$directory.$file.'/'.$file2);
                                }
                            }
                	    }
                	    if($n2=='xls'){
                	       rmdir($_SERVER['DOCUMENT_ROOT'].$directory.$file);
                	    }
                	}
                }
            }
        }
        
        $rand = rand(0,9999999999);
        mkdir($_SERVER['DOCUMENT_ROOT'].$directory.$rand);
        $file_data = $directory.$rand.'/'.$nn_file;

        $objPHPExcel->outToFile($objPHPExcel,$_SERVER['DOCUMENT_ROOT'].$file_data);
        
        $datar['prices_date_excel'] = date('Y-m-d');
        $datar['prices_last_file_excel'] = $file_data;
        $this->model_pdfexcelimport_pdfexcelimport->editPriceSettings($customer_group_id, $datar);
        //$objPHPExcel->outToBrowser($objPHPExcel,$nn_file);
		    //EXCEL-------------------------------------

		    $success = 1;

		    $this->response->redirect($this->url->link('pdfexcelimport/pdfexcelimport', 'user_token=' . $this->session->data['user_token'].'&success='.$success, true));
    }

    public function edit(){
        $this->load->language('pdfexcelimport/pdfexcelimport');

        $this->document->setTitle($this->language->get('text_title'));

		    $this->load->model('pdfexcelimport/pdfexcelimport');
		    $this->load->model('customer/customer_group');
		    $this->load->model('catalog/category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $datar['prices_ids'] = implode(',', $this->request->post['categories']);
            $datar['prices_dopin'] = implode(',', $this->request->post['prices_dopin']);
            $datar['prices_dopin_poask'] = implode(',', $this->request->post['prices_dopin_poask']);

            /*if(isset($this->request->post['prices_model1'])){
              $datar['prices_model1'] = 1;
            }else{
              $datar['prices_model1'] = 0;
            }
            if(isset($this->request->post['prices_model2'])){
              $datar['prices_model2'] = 1;
            }else{
              $datar['prices_model2'] = 0;
            }*/
            if(isset($this->request->post['prices_code'])){
              $datar['prices_code'] = 1;
            }else{
              $datar['prices_code'] = 0;
            }
            if(isset($this->request->post['prices_articul'])){
              $datar['prices_articul'] = 1;
            }else{
              $datar['prices_articul'] = 0;
            }
            if(isset($this->request->post['prices_img'])){
              $datar['prices_img'] = 1;
            }else{
              $datar['prices_img'] = 0;
            }
            /*if(isset($this->request->post['prices_sklad'])){
              $datar['prices_sklad'] = 1;
            }else{
              $datar['prices_sklad'] = 0;
            }
            if(isset($this->request->post['prices_class'])){
              $datar['prices_class'] = 1;
            }else{
              $datar['prices_class'] = 0;
            }*/
            if(isset($this->request->post['prices_sort'])){
              $datar['prices_sort'] = 1;
            }else{
              $datar['prices_sort'] = 0;
            }
            if(isset($this->request->post['gart_enabled'])){
              $datar['gart_enabled'] = 1;
            }else{
              $datar['gart_enabled'] = 0;
            }
            if(isset($this->request->post['gart_optid'])){
              $datar['gart_optid'] = $this->request->post['gart_optid'];
            }else{
              $datar['gart_optid'] = 0;
            }
            /*if(isset($this->request->post['prices_imgmark'])){
              $datar['prices_imgmark'] = 1;
            }else{
              $datar['prices_imgmark'] = 0;
            }*/
            if(isset($this->request->post['prices_dateadd'])){
              $datar['prices_dateadd'] = 1;
            }else{
              $datar['prices_dateadd'] = 0;
            }
            if(isset($this->request->post['prices_delete'])){
              $datar['prices_delete'] = 1;
            }else{
              $datar['prices_delete'] = 0;
            }

            $datar['prices_filename'] = $this->request->post['prices_filename'];
            $customer_group_id = $this->request->post['customer_group_id'];

            $prices = $this->model_pdfexcelimport_pdfexcelimport->getPriceSettings($customer_group_id);
            if(count($prices)>0){
               $this->model_pdfexcelimport_pdfexcelimport->editPriceSettings($customer_group_id, $datar);
            }else{
               $this->model_pdfexcelimport_pdfexcelimport->addPriceSettings($customer_group_id, $datar);
            }
            $this->response->redirect($this->url->link('pdfexcelimport/pdfexcelimport', 'user_token=' . $this->session->data['user_token'], true));
        }

		    $customer_group_id = $this->request->get['customer_group_id'];
		    $customer_group = $this->model_customer_customer_group->getCustomerGroup($customer_group_id);
		    $prices = $this->model_pdfexcelimport_pdfexcelimport->getPriceSettings($customer_group['customer_group_id']);
		   if(isset($prices['prices_ids'])){
		      $prices_ids = explode(',', $prices['prices_ids']);
		    }
		   if(isset($prices['prices_dopin'])){
		      $prices_dopin = explode(',', $prices['prices_dopin']);
		    }
        if(isset($prices['prices_dopin_poask'])){
           $prices_dopin_poask = explode(',', $prices['prices_dopin_poask']);
        }
		    $customer_group['prices'] = $prices;
        $data['customer_group'] = $customer_group;

        $alloptions_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option_value_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
        $data['alloptions'] = $alloptions_query->rows;

        $filter_data = array(
			    'sort'  => 'cg.sort_order',
			    'order' => 'ASC',
			    'start' => 0,
			    'limit' => 100
		    );
		    $customer_groups = $this->model_customer_customer_group->getCustomerGroups($filter_data);
		    foreach ($customer_groups as $key => $value) {
        	if(!isset($prices['prices_dopin']) or in_array($value['customer_group_id'], $prices_dopin)){
        	   $customer_groups[$key]['checked'] = 1;
        	}else{
               $customer_groups[$key]['checked'] = 0;
        	}
            if(in_array($value['customer_group_id'], $prices_dopin_poask)){
                $customer_groups[$key]['prices_dopin_poask_checked'] = 1;
            }else{
                $customer_groups[$key]['prices_dopin_poask_checked'] = 0;
            }
        }
		    $data['customer_groups'] = $customer_groups;

        $categories = $this->model_catalog_category->getCategoriesClear(0,1000);
        foreach ($categories as $key => $value) {
        	if(!isset($prices['prices_ids']) or in_array($value['category_id'], $prices_ids)){
        	   $categories[$key]['checked'] = 1;
        	}else{
               $categories[$key]['checked'] = 0;
        	}
        }
        $data['categories'] = $categories;

		    $data['breadcrumbs'] = array();
		    $data['breadcrumbs'][] = array(
		    	'text' => $this->language->get('text_home'),
		    	'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		    );
		    $data['breadcrumbs'][] = array(
		    	'text' => $this->language->get('text_title'),
		    	'href' => $this->url->link('pdfexcelimport/pdfexcelimport', 'user_token=' . $this->session->data['user_token'], true)
		    );
		    $data['breadcrumbs'][] = array(
		    	'text' => $this->language->get('text_edit'),
		    	'href' => $this->url->link('pdfexcelimport/pdfexcelimport', 'user_token=' . $this->session->data['user_token'], true)
		    );


		    $data['action'] = $this->url->link('pdfexcelimport/pdfexcelimport/edit', 'user_token=' . $this->session->data['user_token'], true);
		    $data['user_token'] = $this->session->data['user_token'];
		

		    $data['header'] = $this->load->controller('common/header');
		    $data['column_left'] = $this->load->controller('common/column_left');
		    $data['footer'] = $this->load->controller('common/footer');

		    $this->response->setOutput($this->load->view('pdfexcelimport/pdfexcelimport_edit', $data));
    }

	public function index() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "prices` (
    `prices_id` int(11) NOT NULL AUTO_INCREMENT,
    `prices_date_excel` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
    `prices_date_pdf` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
    `prices_dateadd` int(11) NOT NULL,
    `prices_filename` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
    `prices_ids` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
    `prices_model1` int(11) NOT NULL,
    `prices_model2` int(11) NOT NULL,
    `prices_code` int(11) NOT NULL,
    `prices_articul` int(11) NOT NULL,
    `prices_img` int(11) NOT NULL,
    `prices_sklad` int(11) NOT NULL,
    `prices_class` int(11) NOT NULL,
    `prices_sort` int(11) NOT NULL,
    `gart_enabled` int(11) NOT NULL,
    `gart_optid` int(11) NOT NULL,
    `prices_imgmark` int(11) NOT NULL,
    `prices_dopin` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
    `prices_dopin_poask` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
    `prices_delete` int(11) NOT NULL,
    `prices_last_file_excel` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
    `prices_last_file_pdf` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
		  PRIMARY KEY (`prices_id`)
		)");

    $this->load->language('pdfexcelimport/pdfexcelimport');

    $this->document->setTitle($this->language->get('text_title'));

		$this->load->model('pdfexcelimport/pdfexcelimport');

		$this->load->model('customer/customer_group');
        $filter_data = array(
			'sort'  => 'cg.sort_order',
			'order' => 'ASC',
			'start' => 0,
			'limit' => 100
		);
		$customer_groups = $this->model_customer_customer_group->getCustomerGroups($filter_data);
		foreach ($customer_groups as $key => $value) {
			$prices = $this->model_pdfexcelimport_pdfexcelimport->getPriceSettings($value['customer_group_id']);
			$names = explode('/', $prices['prices_last_file_excel']);
			$prices['prices_last_file_excel_name'] = $names[count($names)-1];
			$prices['prices_last_file_excel']= $prices['prices_last_file_excel'].'?ver'.rand(0,9999999);
			$names = explode('/', $prices['prices_last_file_pdf']);
			$prices['prices_last_file_pdf_name'] = $names[count($names)-1];
			$prices['prices_last_file_pdf']= $prices['prices_last_file_pdf'].'?ver'.rand(0,9999999);
            $customer_groups[$key]['prices'] = $prices;
            $customer_groups[$key]['action_edit'] = $this->url->link('pdfexcelimport/pdfexcelimport/edit', 'customer_group_id='.$value['customer_group_id'].'&user_token=' . $this->session->data['user_token'], true);
            $customer_groups[$key]['action_reload_excel'] = $this->url->link('pdfexcelimport/pdfexcelimport/reload_excel', 'customer_group_id='.$value['customer_group_id'].'&user_token=' . $this->session->data['user_token'], true);
            $customer_groups[$key]['action_reload_pdf'] = $this->url->link('pdfexcelimport/pdfexcelimport/reload_pdf', 'customer_group_id='.$value['customer_group_id'].'&user_token=' . $this->session->data['user_token'], true);
		}
		$data['customer_groups'] = $customer_groups;
    
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_title'),
			'href' => $this->url->link('pdfexcelimport/pdfexcelimport', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('pdfexcelimport/pdfexcelimport', 'user_token=' . $this->session->data['user_token'], true);

		$data['user_token'] = $this->session->data['user_token'];
		
		if(isset($this->request->get['success']) and $this->request->get['success']==1){
			$data['success'] = $this->language->get('text_e_success');
		}
		if(isset($this->request->get['success']) and $this->request->get['success']==2){
			$data['success'] = $this->language->get('text_p_success');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('pdfexcelimport/pdfexcelimport', $data));
	}

public function console_log() 
{
	static $f = false;
    if (!func_num_args()) return; # Аргументы не переданы
    if (!$f) $f = fopen('!console.log',"w");
    foreach (func_get_args() as $arg) 
	{
        if (is_bool($arg)) $s = $arg?'TRUE':'FALSE';
        elseif (is_array($arg) or is_object($arg)) $s = print_r($arg, TRUE);
        else $s = $arg;
        fwrite($f,$s.' '); # вывод аргументов разделяется пробелом
    }
}

}
