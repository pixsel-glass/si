<?php
class ModelExtensionShippingFANCourier extends Model {
	public function getQuote($address) {
		$this->load->language('extension/shipping/fancourier');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country where country_id = '" . (int)$address['country_id'] . "'");

		if ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		if ($status) {
                        $username = $this->config->get('shipping_fancourier_username');
                        $parola = $this->config->get('shipping_fancourier_password');
                        $clientid = $this->config->get('shipping_fancourier_clientid');
                        $parcel = $this->config->get('shipping_fancourier_parcel');
                        $labels = $this->config->get('shipping_fancourier_labels');
                        $ramburs = $this->config->get('shipping_fancourier_ramburs');
                        $content = $this->config->get('shipping_fancourier_content');
                        $contcolector = $this->config->get('shipping_fancourier_contcolector');
                        $redcode = $this->config->get('shipping_fancourier_redcode');
                        $express = $this->config->get('shipping_fancourier_express');
						$paypoint = $this->config->get('shipping_fancourier_ridicare_paypoint');
                        $keba = $this->config->get('shipping_fancourier_ridicare_keba');

					    $paymentdest = $this->config->get('shipping_fancourier_paymentdest');
                        $paymentrbdest = $this->config->get('shipping_fancourier_paymentrbdest');
                        $payment0 = $this->config->get('shipping_fancourier_payment0');
                        $min_gratuit = $this->config->get('shipping_fancourier_min_gratuit');
                        //alex g valoare fixa
                        $valoare_fixa = $this->config->get('shipping_fancourier_valoare_fixa');
                        //sfarsit valoare fixa
                        //alex g valoare fixa bucuresti
                        $valoare_fixa_bucuresti = $this->config->get('shipping_fancourier_valoare_fixa_bucuresti');
                        //sfarsit valoare fixa bucuresti
                        $observatii = $this->config->get('fancourier_comment');
			            $pers_contact_expeditor = $this->config->get('fancourier_pers_contact_expeditor');
			            $deschidere_la_livrare = $this->config->get('fancourier_deschidere_livrare');
                        $epod = $this->config->get('fancourier_epod');

			            $asigurare = $this->config->get('shipping_fancourier_asigurare');
                        $totalrb = $this->config->get('shipping_fancourier_totalrb');
                        $fara_tva = $this->config->get('shipping_fancourier_fara_tva');
                        //valoare km suplimentari
                        $doar_km = $this->config->get('fancourier_doar_km');
                        //sfarsit valoare km suplimentari


                        $msg = "Comanda nu a fost procesata de catre FAN Courier.<br>Va rugam sa corectati datele de livrare conform mesajului de mai jos: <br><br>";

			            $optiuni = '';

                        if ($deschidere_la_livrare == 'A') $optiuni .= 'A';
                        if ($epod == 'X') $optiuni .= 'X';

                        $method_data = array();
                        $error = '';

                        if (is_numeric($min_gratuit)) $min_gratuit = $min_gratuit + 0; else $min_gratuit = 0 + 0;

						//alex g valoare fixa
						if (is_numeric($valoare_fixa)) $valoare_fixa = $valoare_fixa + 0; else $valoare_fixa = 0 + 0;
                        //sfarsit valoare fixa

						//alex g valoare fixa
						if (is_numeric($valoare_fixa_bucuresti)) $valoare_fixa_bucuresti = $valoare_fixa_bucuresti + 0; else $valoare_fixa_bucuresti = 0 + 0;
                        //sfarsit valoare fixa

						$link_standard="";

                        if ($parcel){
                            $plic="0";
                            if (is_numeric($labels)){
                                $colet=$labels;
                            } else {
                                $colet=1;
                            }
                        } else {
                            $colet="0";
                            if (is_numeric($labels)){
                                $plic=$labels;
                            } else {
                                $plic=1;
                            }
                        }

                        if ($totalrb){
                            $totalrb = "1";
                        } else {
                            $totalrb = "0";
                        }

                        $totalPrice = number_format(round((float)$this->cart->getTotal(),2), 2, '.', '');

                        if ($this->cart->hasProducts()){

                                    if ($asigurare){
                                            $valoaredeclarata = $totalPrice;
                                    } else {
                                            $valoaredeclarata = 0;
                                    }

                                    $greutate = number_format(round((float)$this->cart->getWeight(),0), 0, '.', '');
									//
									if ($greutate>1)
									{
										$plic=0;
										if (is_numeric($labels))
										{
											$colet=$labels;
										}
										else
										{
											$colet=1;
										}
									}


									//
                                    if (round((float)$this->cart->getWeight(),0)>5) $redcode = false;

                                    $lungime=0;
                                    $latime=0;
                                    $inaltime=0;

                                    if ($paymentdest){
                                        $plata_expeditiei="destinatar";
                                    }else{
                                        $plata_expeditiei="expeditor";
                                    }

                                    $rambursare = '';
                                    $rambursare_number = 0 + 0;

                                    $plata_expeditiei_ramburs = "";

                                    //alex g comutare intre valoarea fixa bucuresti si valoarea fixa pe tara
                                    if ((strtolower($address['city'])=="bucuresti") and is_numeric($valoare_fixa_bucuresti))
                                    {
                                    	$valoare_fixa=$valoare_fixa_bucuresti;
                                    }
                                    //alex g sfarsit comutare


                                    if ($ramburs)
									{
                                        if ($contcolector)
										{
                                            $rambursare = number_format(round((float)$this->cart->getTotal(),2), 2, '.', '');
                                            $rambursare_number = round((float)$this->cart->getTotal(),2)+0;

                                            if ($min_gratuit<$rambursare_number and $min_gratuit!=0)
                                            {
                                            	$totalrb="0";
                                            }

                                            if ($paymentrbdest)
											{
                                                $plata_expeditiei_ramburs="destinatar";
                                            }
											else
											{
                                                $plata_expeditiei_ramburs="expeditor";
                                            }
                                        }
										else
										{
                                            $rambursare = (string)number_format(round((float)$this->cart->getTotal(),2), 2, '.', '')." LEI";
                                            $rambursare_number = round((float)$this->cart->getTotal(),2)+0;

											if ($min_gratuit<$rambursare_number and $min_gratuit!=0)
                                            {
                                            	$totalrb="0";
                                            }

                                            if ($paymentrbdest)
											{
                                                $plata_expeditiei_ramburs="destinatar";
                                            }
											else
											{
                                                $plata_expeditiei_ramburs="expeditor";
                                            }
                                        }
                                    }
									else
									{
                                        $rambursare_number = round((float)$this->cart->getTotal(),2)+0;
                                    }

									//cand min gratuit  mai mic ca ramburs trec automat plata la expeditor
                                        if ($min_gratuit<$rambursare_number and $min_gratuit!=0)
										{
											$plata_expeditiei="expeditor";
										}
									//sfarsit

                                    $localitate_dest = $address['city'];

                                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone where zone_id = " . (int)$address['zone_id'] . " limit 0, 1");

                                    if ($query->num_rows) {
                                        $judet_dest = $query->row['name'];
                                    }

                                    if ($judet_dest=="Satu-Mare")
                                    {
                                        $judet_dest="Satu Mare";
                                    }

                                    if ($judet_dest=="Dimbovita")
                                    {
                                        $judet_dest="Dambovita";
                                    }



                                    $continut='';


                            $height = array();
                            $length = array();
                            $width = array();
                            $k=0;

                            foreach ($this->cart->getProducts() as $product)
                            {
                                //if ($product['shipping'])
                                //{
                                //    $continut = $continut.$product["quantity"]." X ".$product["name"].", ";
                                //}


                                $height[$k] = number_format($product["height"],2);
                                $length[$k] = number_format($product["length"],2);
                                $width[$k] = number_format($product["width"],2);

                                $produs[$k] = array( $height[$k],  $width[$k], $length[$k] );

                                $k++;

                            }



                            //setez lungime -> ce mai mare dintre dimensiuni
                            $lungime = max(array_merge($height, $length, $width));
                            $latime = array_sum($width);
                            $inaltime = array_sum($height);


                                    if(trim($address["company"])!='')
									{
                                        $nume_destinatar =$address["company"];
                                        $persoana_contact =  $address["firstname"]." ".$address["lastname"];
                                        if (trim($persoana_contact) == '')$this->customer->getFirstName()." ".$this->customer->getLastName();
                                    }
									else
									{
										$nume_destinatar = $address["firstname"]." ".$address["lastname"];
										if ($nume_destinatar == ' ')$this->customer->getFirstName()." ".$this->customer->getLastName();
										$persoana_contact = $this->customer->getFirstName()." ".$this->customer->getLastName();
										if (trim($persoana_contact) == trim($persoana_contact)) $persoana_contact = '';
                                    }



                                    $strada = $address['address_1'];
									$strada = rawurlencode($strada);
                                    if ($address['address_1']!=''){
										$temp_adress = $address['address_2'];
										$temp_adress = rawurlencode($temp_adress);
                                        $strada = $strada.", ".$temp_adress;
                                    }

                                    $activeServices = $this->db->query("SELECT fancourier_id, fancourier_name  FROM `" . DB_PREFIX . "fancourier_service` WHERE status = 1 ");
                                    $services = $activeServices->rows;
                                    $currency = 'RON';
                                    $quote_data = array();

                                    foreach($services as $service){
                                        // echo "domain=$domain&serviceTypeId=$serviceTypeId&recipientLocality=$localitate_dest&recipientCounty=Romania&weight=$greutate&packageLength=$lungime&packageWidth=$latime&packageHeight=$inaltime&repayment=0";

                                        $serviceTypeId = $service["fancourier_id"];
                                        $auth = $this->db->query("SELECT token FROM `" . DB_PREFIX . "fancourier_auth_info`");
                                        $authorization = "Authorization: Bearer ".$auth->rows[0]["token"];
                                        $domain = HTTPS_SERVER;
                                        $url = 'https://ecommerce.fancourier.ro/check-service';

                                        $chService = curl_init($url);
                                        curl_setopt($chService, CURLOPT_RETURNTRANSFER, 1);
                                        curl_setopt ($chService, CURLOPT_POST, true);
                                        curl_setopt($chService, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded',$authorization));
                                        // curl_setopt ($chService, CURLOPT_POSTFIELDS, "domain=$domain&serviceTypeId=$serviceTypeId&recipientLocality=$localitate_dest&recipientCounty=$judet_dest&weight=$greutate&packageLength=$lungime&packageWidth=$latime&packageHeight=$inaltime&repayment=0");
                                        curl_setopt ($chService, CURLOPT_POSTFIELDS, "domain=$domain&serviceTypeId=1&recipientLocality=$localitate_dest&recipientCounty=Romania&weight=1.000&packageLength=1.000&packageWidth=1&packageHeight=1&repayment=0");
                                        curl_setopt($chService ,CURLOPT_SSL_VERIFYPEER,0);

                                        $responseService = curl_exec($chService);
                                        $httpcode = curl_getinfo($chService, CURLINFO_HTTP_CODE);

                                        curl_close($chService);
                                        //$this->log->write(json_encode(array($serviceTypeId,$responseService)));
                                        //$this->log->write(json_encode(array($domain,$serviceTypeId,$localitate_dest,$judet_dest,$greutate,$lungime,$latime,$inaltime,$rambursare_number)));
                                        // echo $httpcode;
                                        if($httpcode == 200){
                                            $available = $responseService == 1 ? true : false;
                                        } else {
                                            $available = false ;
                                        }
                                        $available = 1;

                                        if ($available) {

                                            if (is_numeric($this->config->get('shipping_fancourier_min_gratuit')) && $totalPrice >= $this->config->get('shipping_fancourier_min_gratuit')) {
                                                $quote_data[str_replace(" ","_",$service["fancourier_name"])] = array(
                                                    'code'         => 'fancourier.'.str_replace(" ","_",$service["fancourier_name"]),
                                                    'title'        => $service["fancourier_name"],
                                                    'cost'         => $this->currency->convert(0, $currency, $this->config->get('config_currency')),
                                                    'tax_class_id' => 'fancourier.tax',
                                                    'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($tariff, $currency, $this->config->get('config_currency') ), $this->config->get('fan_class_id'), $this->config->get('config_tax')), $this->config->get('config_currency') , 1.0000000)
                                                );

                                                $method_data = array(
                                                    'code'       => 'fancourier',
                                                    'title'      => 'FAN Courier - Romania',
                                                    'quote'      => $quote_data,
                                                    'sort_order' => '1',
                                                    'error'      => false
                                                );
                                            }

                                            if (is_numeric($this->config->get('shipping_fancourier_' .$serviceTypeId. '_price'))) {
                                                $tariff = $this->config->get('shipping_fancourier_' .$serviceTypeId. '_price');

                                                $quote_data[str_replace(" ","_",$service["fancourier_name"])] = array(
                                                    'code'         => 'fancourier.'.str_replace(" ","_",$service["fancourier_name"]),
                                                    'title'        => $service["fancourier_name"],
                                                    'cost'         => $this->currency->convert($tariff, $currency, $this->config->get('config_currency')),
                                                    'tax_class_id' => 'fancourier.tax',
                                                    'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($tariff, $currency, $this->config->get('config_currency') ), $this->config->get('fan_class_id'), $this->config->get('config_tax')), $this->config->get('config_currency') , 1.0000000)
                                                );

                                                $method_data = array(
                                                    'code'       => 'fancourier',
                                                    'title'      => 'FAN Courier - Romania',
                                                    'quote'      => $quote_data,
                                                    'sort_order' => '1',
                                                    'error'      => false
                                                );

                                                continue;
                                            }

                                            $auth = $this->db->query("SELECT token FROM `" . DB_PREFIX . "fancourier_auth_info`");
                                            $authorization = "Authorization: Bearer ".$auth->rows[0]["token"];
                                            $domain = HTTPS_SERVER;
                                            $url = 'https://ecommerce.fancourier.ro/get-tariff';
                                            $chTariff = curl_init($url);
                                            curl_setopt($chTariff, CURLOPT_RETURNTRANSFER, 1);
                                            curl_setopt ($chTariff, CURLOPT_POST, true);
                                            curl_setopt($chTariff, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded',$authorization));
                                            curl_setopt ($chTariff, CURLOPT_POSTFIELDS, "domain=$domain&serviceTypeId=$serviceTypeId&recipientLocality=$localitate_dest&recipientCounty=$judet_dest&weight=$greutate&length=$lungime&width=$latime&height=$inaltime&declaredValue=$valoaredeclarata");
                                            curl_setopt($chTariff ,CURLOPT_SSL_VERIFYPEER,0);

                                            $responseTariff = curl_exec($chTariff);
                                            $httpcode = curl_getinfo($chTariff, CURLINFO_HTTP_CODE);
                                            curl_close($chTariff);
                                            $responseTariffDecoded = json_decode($responseTariff);

                                            if ($httpcode == 200) {
                                                $tariff = $responseTariffDecoded->tariff;

                                                if ($min_gratuit<$rambursare_number and $min_gratuit!=0)
                                                {
                                                    $tariff="0";
                                                }

                                                $quote_data[str_replace(" ","_",$service["fancourier_name"])] = array(
                                                    'code'         => 'fancourier.'.str_replace(" ","_",$service["fancourier_name"]),
                                                    'title'        => $service["fancourier_name"],
                                                    'cost'         => $this->currency->convert($tariff, $currency, $this->config->get('config_currency')),
                                                    'tax_class_id' => 'fancourier.tax',
                                                    'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($tariff, $currency, $this->config->get('config_currency') ), $this->config->get('fan_class_id'), $this->config->get('config_tax')), $this->config->get('config_currency') , 1.0000000)
                                                );

                                                $method_data = array(
                                                    'code'       => 'fancourier',
                                                    'title'      => 'FAN Courier - Romania',
                                                    'quote'      => $quote_data,
                                                    'sort_order' => '1',
                                                    'error'      => false
                                                 );
                                            } else {
                                                $message =  "Nu am putut calcula tariful pentru celelalte tipuri de servicii!";
                                            }
                                        }
                                    }
                                    return $method_data;



                        } else {
                            $currency = 'RON';

                            $activeServices = $this->db->query("SELECT fancourier_id, fancourier_name  FROM `" . DB_PREFIX . "fancourier_service` WHERE status = 1 ");
                            $services = $activeServices->rows;

                            foreach($services as $service){
                                $quote_data[str_replace(" ","_",$service["fancourier_name"])] = array(
                                    'code'         => 'fancourier.'.str_replace(" ","_",$service["fancourier_name"]),
                                    'title'        => $service["fancourier_name"],
                                    'cost'         => $this->currency->convert(0, $currency, $this->config->get('config_currency')),
                                    'tax_class_id' => 'fancourier.tax',
                                    'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($tariff, $currency, $this->config->get('config_currency') ), $this->config->get('fan_class_id'), $this->config->get('config_tax')), $this->config->get('config_currency') , 1.0000000)
                                );

                                $method_data = array(
                                    'code'       => 'fancourier',
                                    'title'      => 'FAN Courier - ' . $service["fancourier_name"],
                                    'quote'      => $quote_data,
                                    'sort_order' => '1',
                                    'error'      => false
                                );
                            }

                            return $method_data;
                        }

                }
	}
}
?>
