<?php
    class ControllerExtensionModuleFancourier extends Controller{


        public function generateOrder($route, $data){
            $this->load->model('checkout/order');
            $this->load->model('catalog/product');

            $orderId = $data[0];
            $orderInfo = $this->model_checkout_order->getOrder($orderId);
            $productsInfo = $this->model_checkout_order->getOrderProducts($orderId);
            $totalInfo = $this->model_checkout_order->getOrderTotals($orderId);

            $weight = 0 ;
            $content = "";
            $k = 0;

            $height = array();
            $lenght = array();
            $width = array();

            foreach($productsInfo as $productInfo){
                $productOrderInfo = $this->model_catalog_product->getProduct($productInfo["product_id"]);

                $weight += $productOrderInfo["weight"];
                $content = $content.$productInfo["quantity"]." X ".$productInfo["name"].", ";

                $height[$k] = number_format($productOrderInfo["height"],2);
                $lenght[$k] = number_format($productOrderInfo["length"],2);
                $width [$k] = number_format($productOrderInfo["width"],2);
                $k++;
            }

            $maxLenght = max(array_merge($height, $lenght, $width));
            $widthSum = array_sum($width);
            $heightSum = array_sum($height);

            $weight = number_format(round((float) $weight,0), 0, '.', '');

            if(strpos($orderInfo["shipping_code"],"fancourier") !== false){

                $serviceName = $orderInfo["shipping_method"];
                $service= $this->db->query("SELECT fancourier_id  FROM `" . DB_PREFIX . "fancourier_service` WHERE fancourier_name = '".$serviceName."'");

                $serviceId = $service->row["fancourier_id"];
                $lockerName = isset($_COOKIE['fancourier_locker_name']) ? $_COOKIE['fancourier_locker_name'] : null;

                $recipientStreet = $orderInfo["shipping_address_1"]. " ". $orderInfo["shipping_address_2"];
                $recipientCounty = $orderInfo["shipping_zone"];
                $recipientLocality = $orderInfo["shipping_city"];
                if( $serviceId == '27' &&  $lockerName != null){
                    $recipientStreet = $lockerName;
                }

                $COD = $orderInfo["payment_code"] === 'cod' ? 1 : 0;

                if ($COD) {
                    $servicesAndContCollector = [
                        1 => 4,
                        2 => 9,
                        5 => 10,
                        6 => 11,
                        7 => 12,
                        13 => 14,
                        23 => 24,
                        27 => 28,
                    ];

                    $serviceId = $servicesAndContCollector[$serviceId];
                }


                $orderDetails["serviceTypeId"] =  $serviceId;
                $orderDetails["recipientName"] =   $orderInfo["shipping_firstname"].' '.$orderInfo["shipping_lastname"];
                $orderDetails["recipientContactPerson"] =  "";
                $orderDetails["recipientPhone"] = $orderInfo["telephone"];
                $orderDetails["recipientEmail"] =  $orderInfo["email"];
                $orderDetails["recipientStreet"] =  $recipientStreet;
                $orderDetails["recipientZipCode"] =  $lockerName != null ? '' : $postalcode = str_pad($orderInfo["shipping_postcode"], 6 ,"0");
                $orderDetails["recipientLocality"] =  $recipientLocality;
                $orderDetails["recipientCounty"] =  $recipientCounty;
                $orderDetails["weight"] = $weight > 0 ? $weight : 1;
                $orderDetails["length"] =  $maxLenght;
                $orderDetails["height"] =  $heightSum;
                $orderDetails["width"] =  $widthSum;
                $orderDetails["total_products"] =  round($totalInfo[0]["value"],2);
                $orderDetails["total_shipping"] = round($totalInfo[1]["value"],2);
                $orderDetails["total_paid"] = round($totalInfo[2]["value"],2);
                $orderDetails["content"] = $content;
                $orderDetails["payByCard"] = !$COD;

                $orderData = json_encode($orderDetails);
                $auth = $this->db->query("SELECT token FROM `" . DB_PREFIX . "fancourier_auth_info`");
                $authorization = "Authorization: Bearer ".$auth->rows[0]["token"];
                $domain = HTTPS_SERVER;

                $url = 'https://ecommerce.fancourier.ro/generate-order';
                $chOrder = curl_init($url);
                curl_setopt($chOrder, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($chOrder, CURLOPT_POST, true);
                curl_setopt($chOrder, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded',$authorization));
                curl_setopt ($chOrder, CURLOPT_POSTFIELDS, "domain=$domain&orderData=$orderData&orderId=$orderId");
                curl_setopt($chOrder ,CURLOPT_SSL_VERIFYPEER,0);

                $responseOrder = curl_exec($chOrder);
                $httpcode = curl_getinfo($chOrder, CURLINFO_HTTP_CODE);
                curl_close($chOrder);

                if ($httpcode == 200) {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "fancourier_order_info` SET id_order = '" .$orderId. "', fan_AWB='', fanbox_name = '".$lockerName."'");
                    setcookie('fancourier_locker_name', '', time() - 3600, '/');
                    setcookie('fancourier_locker_address', '', time() - 3600, '/');

                }
            }
            //$this->load->model('sale/order');
        }
    }
?>
