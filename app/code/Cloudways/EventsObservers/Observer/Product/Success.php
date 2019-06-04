<?php
namespace Cloudways\EventsObservers\Observer\Product;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
class Success implements ObserverInterface
{ 

    protected $orderRepository;
    public $apiauthtoken = "03d95794d824e8c2c65a2a2b6cfedc65";
    protected $_productloader;  

    public function __construct(  
    OrderRepositoryInterface $OrderRepositoryInterface, \Magento\Catalog\Model\ProductFactory $_productloader
    ) {
        $this->orderRepository = $OrderRepositoryInterface;
          $this->_productloader = $_productloader;
    

    }
    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
    public function execute(\Magento\Framework\Event\Observer $observer) 
    {
                $accessToken = $this->access_token();
                $order_ids = $observer->getEvent()->getOrderIds()[0];
                $ordersss = $observer->getOrder();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $tableName = $resource->getTableName('aitoc_sales_order_value');
                $sql = $connection->select('*')->from($tableName)->where('order_id = ?', $order_ids);
                $tresult = $connection->fetchAll($sql);
                $purchase_order_val = "";
                if(isset($tresult[3]))
                {
                  if(isset($tresult[3]['value']) && !empty($tresult[3]['value']))
                  {
                    $purchase_order_val = $tresult[3]['value'];
                  }
                  
                }
              
                $tableName = $resource->getTableName('sales_order');
                $sql_sales_order = $connection->select('*')->from($tableName)->where('entity_id = ?', $order_ids);
                $sales_order_result = $connection->fetchAll($sql_sales_order);
                $ordercomment = "";
                $tax_amount = "";
                $order_status = "";
                foreach ($sales_order_result as $key => $value) {
                   if(!empty($value['osc_order_comment'])){
                      $ordercomment = $value['osc_order_comment'];
                   }

                   if(!empty($value['tax_amount'])){
                      $tax_amount = $value['tax_amount'];
                   }
                   if(!empty($value['status'])){
                      $order_status = $value['status'];
                   }
                }
            
               
               //$shippingTax = $order->getTotalTax();
              
                 $addionalinfo = array();
                  foreach($tresult as $key=>$val) {
                      if($key !=3){
                      $tName = $resource->getTableName('eav_attribute_option_value');
                      $option_id = $val['value'];
                      $sqls = "select * FROM " . $tName . " where option_id=".$option_id;
                      $option_result=$connection->fetchAll($sqls);
                      //$counter =0; 
                      if(isset($option_result)){
                          foreach ($option_result as $key => $value) {
                          //echo $sqls."INLOOP<br/><PRE>";
                          $addionalinfo[$value['option_id']] = $value['value'];
                          }
                        }
                      }
                  }
         							 
                  $salesorderarray = array();
                  $order = $this->orderRepository->get($order_ids);    
                    //$statusHistoryItem = $order->getStatusHistoryCollection()->getFirstItem();  
                      

                  
                  $order_id = $order->getIncrementId();   
                  $customer_email = $order->getCustomerEmail();
                  $shipping_address = $order->getShippingAddress();                  
                  $salesorderarray['Shipping_First_Name']=$shipping_address->getFirstname();
                  $salesorderarray['Shipping_Second_Name']=$shipping_address->getLastname();
                  $salesorderarray['Shipping_City']=$shipping_address->getCity();
                  $salesorderarray['Shipping_Company_Name']= $shipping_address->getCompany();
                  $salesorderarray['Shipping_Street'] = "";
                  $shipStreet = $order->getBillingAddress()->getStreet();
                  if(isset($shipStreet) && !empty($shipStreet))
                    {
                      foreach ($shipStreet as $key => $value) 
                      {
                        $salesorderarray['Shipping_Street'] .= $value;
                        $salesorderarray['Shipping_Street'] .= ",";
                      }
                    }
                  $salesorderarray['Shipping_State1']= $shipping_address->getRegion();                  
                  $salesorderarray['Shipping_Code']= $shipping_address->getPostcode();
                  $salesorderarray['Phone']= $shipping_address->getTelephone();
                  $salesorderarray['Shipping_Country1']= $shipping_address->getCountryId();
                  $billing_address = $order->getBillingAddress();
                  $salesorderarray['Billing_First_Name']=$billing_address->getFirstname();
                  $salesorderarray['Billing_Second_Name']=$billing_address->getLastname();
                  $salesorderarray['Billing_City']=$billing_address->getCity();
                  $salesorderarray['Company']= $billing_address->getCompany();
                  $salesorderarray['Billing_Street'] = "";
                  $billStreet = $order->getShippingAddress()->getStreet();
                  if(isset($billStreet) && !empty($billStreet))
                    {
                      foreach ($billStreet as $key => $value) 
                      {
                        $salesorderarray['Billing_Street'] .= $value;
                        $salesorderarray['Billing_Street'] .=",";
                      }
                  }
                  $salesorderarray['Billing_State1']= $billing_address->getRegion();
                  $salesorderarray['Company_Name']= $billing_address->getCompany();
                  $salesorderarray['Billing_City']= $billing_address->getCity();
                  $salesorderarray['Billing_Code']= $billing_address->getPostcode();
                  $salesorderarray['Phone']= $billing_address->getTelephone();
                  $salesorderarray['Billing_Country1']= $billing_address->getCountryId();
                  $payment = $order->getPayment();
                  $method = $payment->getMethodInstance();
                  $methodTitle = $method->getTitle();
                  $salesorderarray['First_Name'] = $shipping_address->getFirstname();
                  $salesorderarray['Last_Name'] = $shipping_address->getLastname();
                  $salesorderarray['Email'] = $order->getCustomerEmail();
                  $salesorderarray['Phone'] = $shipping_address->getTelephone();
                  $grandtotal=$order->getGrandTotal();
                  $subtotal=$order->getSubtotal();  
                  $discount=$order->getDiscountAmount();  
                  $couponcode=$order->getCouponCode();
                  $shippingCost = $order->getShippingAmount();

                  $contactid=$this->checkExistingContact($customer_email, $accessToken);                   
                  if(!empty($contactid))
                  {                   
                    $contactid=$this->checkExistingContact($customer_email, $accessToken);
                  }else
                  {
                    $contactid = $this->createContact($salesorderarray, $accessToken);
                  // echo "INELSE".$contactid;   
                  }
                  $salesorderarray['Customer_Name']['id'] = $contactid;

                  $ipaddress=$_SERVER['REMOTE_ADDR'];
                  $salesorderarray['Purchase_Order'] = '#'.$purchase_order_val;                  
                   if(isset($addionalinfo))
                   {
                        $keys = array_keys($addionalinfo);
                						for($i=0; $i < count($keys); ++$i) {
                						if($i == 0)
                						{
                							$industry = $addionalinfo[$keys[$i]];
                						}
                						elseif($i == 1)
                						{
                                           $Referred = $addionalinfo[$keys[$i]];
                						}
                						elseif($i == 2)
                						{
                							$Type = $addionalinfo[$keys[$i]];
                						}
                						
                						}                   
                   	
                   }
                   if($industry != "")
                    {

                      $salesorderarray['Industry'] = $industry;
                      //Industry and Segement Mapping
                      if($industry == "Advertising" || $industry == "Agriculture" || $industry == "Apparel" || $industry == "Architecture" || $industry == "Banking/Financial/Investment" || $industry == "Chiropractic" || $industry == "Construction" || $industry == "Food Service" || $industry == "Insurance" || $industry == "Legal" || $industry == "Manufacturing" || $industry == "Media/Broadcasting" || $industry == "Non-Profit" || $industry == "Pharmaceutical" || $industry == "Professional Services" || $industry == "Real Estate" || $industry == "Retail" || $industry == "Technology" || $industry == "Telecommunications" || $industry == "Transport") 
                        {
                          $segement = "Corporate";                        
                        }
                        elseif ($industry == "Education")
                        {
                          $segement = "Education";
                          
                        }
                        elseif ($industry == "Government")
                        {
                          $segement = "Government";
                          
                        }
                        elseif ($industry == "Healthcare" || $industry == "Hospitality")
                        {
                          $segement = "Medical";
                        
                        }
                        elseif ($industry == "Other")
                        {
                          $segement = "Other";
                        
                        }
                        else
                        {
                          $segement = "";
                          
                        }
                        $salesorderarray['Segment'] = $segement;

                    }
                   //die;
                   //$xml.='<FL val="Industry">'.$Industry.'</FL>';
                   $salesorderarray['Referred_by'] = $Referred;
                   $salesorderarray['Type'] = $Type;
					
                  if(!empty($salesorderarray['Company']))
                    {
                      $AccName = $salesorderarray['Company'];
                      
                          $accountid = $this->checkExistingaccount($AccName,$accessToken);
                          if(!empty($accountid))                          
                          {
                          $salesorderarray['Account_Name']['id'] = $accountid;                          
                          }
                        else
                        {

                          $accountid=$this->createAccount($salesorderarray,$accessToken);
                          //echo "inelse".$accountid;
                          $salesorderarray['Account_Name']['id'] = $accountid;
                          

                        }
                        
                          
                    }
                    else
                    {
                      $AccName = $salesorderarray['First_Name'].' '.$salesorderarray['Last_Name'];
                      
                          $accountid=$this->checkExistingaccount($AccName,$accessToken);
                          if(!empty($accountid))                          
                          {
                          $salesorderarray['Account_Name']['id'] = $accountid;
                          
                          }
                        else
                        {

                          $accountid=$this->createAccount($salesorderarray,$accessToken);
                          //echo "inelse".$accountid;
                          $salesorderarray['Account_Name']['id'] = $accountid;

                        }
                      ///$xml.='<FL val="Account Name">'.$billing['firstname'].' '.$billing['lastname'].'</FL>'; 
                    }
                  $grandtotal1 = round($grandtotal,2);
                  $shippingCost1 = round($shippingCost,2);
                  $salesorderarray['Email'] = $customer_email;
                  $salesorderarray['Total'] = (string) $grandtotal1;
                  $salesorderarray['Brand'] = "VersaTables";
                  $salesorderarray['Subject'] = $salesorderarray['First_Name'].','.$salesorderarray['Last_Name'].'- Salesorder';
                  $salesorderarray['Order_ID'] = $order_id;
                  $salesorderarray['Due_Date'] = Date('Y-m-d');                
                  $salesorderarray['Order_Status'] = $order_status;
                  $salesorderarray['IP_Address'] = $ipaddress;
                  $salesorderarray['Source'] = "Versatables Website Purchase";
                  $salesorderarray['Shipping_Method'] = $methodTitle;
                  $salesorderarray['Shipping_Charge'] = (string) $shippingCost1;
                  $salesorderarray['Tax'] = $tax_amount;
                  $salesorderarray['Sub_Total'] = $subtotal;
                  $salesorderarray['Grand_Total'] = $grandtotal;
                  $salesorderarray['Discount'] = $discount;
                  $salesorderarray['total_after_discount'] = $grandtotal;
                  $salesorderarray['Description'] = $ordercomment;                 
                  $counter=1;
                  $items=array();
                       foreach ($order->getAllVisibleItems() as $item)
                        {
                          $items[]=$item->getName();
                          $itemname=$item->getName();
                          $options='';
                          if(isset($item['product_options']['options']))
                           { 
                            foreach(@$item['product_options']['options'] as $opt)
                            {
                              $options .=$opt['label'].'='.str_replace('&quot;',"",($opt['value'])).',';

                            }
                          }
                         // echo $options."<br/>";
                         
                          $options_array = explode(",", $options);
                          $productsku = array();
                          $optionsarray = array(); 
                          foreach ($options_array as $key => $value) {
                            if(isset($value) && !empty($value))
                            {
                              $sku_explode  = explode("=",$value);                              
                              if(isset($sku_explode[0]) &&  ($sku_explode[0] == "Sku" || $sku_explode[0] == "sku" || $sku_explode[0] == "SKU"))
                              {
                                $productsku['sku'] = $sku_explode[1];
                              }
                              else
                              {
                                $optionsarray[$sku_explode[0]] = $sku_explode[1];
                              }
                              //$productsku['sku'] = $sku_explode[1];

                            }
                            
                          }
                         
                          $price=$item->getPrice();
                          $sku=$item->getSku();                          
                          $product=$this->getLoadProduct($item->getProductId());
                          $productdata=array();
                          $productdata['name']=$product->getData('name');
                          $productdata['option']=$options;
                          if(isset($productsku['sku']))
                          {
                          //$productdata['code']=$product->getData('sku');
                            $productdata['code']=$productsku['sku'];
                          }
                          else
                          {
                            $productdata['code']=$product->getData('sku');
                          }
                          $productdata['skucode'] = $product->getData('sku');
                          $productdata['price']=$product->getData('price');
                          $productdata['Description']= strip_tags($product->getData('description'));
                          $productdata['created_at']=$product->getData('created_at');
                          $productdata['updated_at']=$product->getData('updated_at');
                          $productdata['qty']=$product->getData('quantity_and_stock_status/qty');
                          if(isset($productsku['sku'])){
                                  $resultSearchitemID=$this->searchBy($productsku['sku'],$accessToken);
                                  //print_r($resultSearchitemID);
                            }
                          if(isset($resultSearchitemID) && !empty($resultSearchitemID))
                          {
                            //echo "inisel";
                            $serachitemid = $resultSearchitemID;
                          }else{
                            //echo "insels";
                            $serachitemid =  $this->productAdd($productdata,$optionsarray,$accessToken);
                          }
                          
                          if(!empty($serachitemid))
                            {
                              $salesorder_itemarray[] = $this->lineitem($item,$serachitemid,$counter,$productdata,$accessToken);
                                 $this->updateProductByid($serachitemid,$productdata,$optionsarray,$accessToken);
                            }

                            $counter++;
                      } 
                      $salesorderarray['Product_Details'] = $salesorder_itemarray;
                      $salesorder_jsondata='{
                      "data":['.json_encode($salesorderarray).']}';
                      //echo $salesorder_jsondata;
                      $url = "https://www.zohoapis.com/crm/v2/Sales_Orders";
                      $resp = $this->curlRequest($url,$salesorder_jsondata,"POST",$accessToken); 
                     
                      return $resp;
    }

  public function createContact($data,$accessToken)
  {
    $contactjson = '{
      "data": [
         {
              "phone": "'.$data['Phone'].'",
              "Last_Name": "'.$data['Last_Name'].'",
              "First_Name":"'.$data['First_Name'].'",
              "Email": "'.$data['Email'].'"                        
          }]}';
      //echo $contactjson;
      $url = "https://www.zohoapis.com/crm/v2/Contacts?";                 
      $resp = $this->curlRequest($url,$contactjson,"POST",$accessToken); 
      return @$resp['data']['0']['details']['id'];
         
  }

   public function createAccount($salesorderarray,$accessToken)
    {
      $accountjson = '{
        "data": [
          {
            "Company": "'.$salesorderarray['Company'].'",
            "Billing_Country":"'.$salesorderarray['Billing_Country1'].'",
            "Billing_Street": "'.$salesorderarray['Billing_Street'].'",
            "Billing_Code":"'.$salesorderarray['Billing_Code'].'",
            "Billing_City": "'.$salesorderarray['Billing_City'].'",
            "Billing_State":"'.$salesorderarray['Billing_State1'].'",
            "Shipping_Street":"'.$salesorderarray['Shipping_Street'].'",
            "Shipping_State":"'.$salesorderarray['Shipping_State1'].'",
            "Shipping_City":"'.$salesorderarray['Shipping_City'].'",
            "Shipping_Country":"'.$salesorderarray['Shipping_Country1'].'",
            "Shipping_Code": "'.$salesorderarray['Shipping_Code'].'",
            "Account_Name":"'.$salesorderarray['Company'].'"
            }]}';
        //echo $contactjson;
        $url = "https://www.zohoapis.com/crm/v2/Accounts?";                 
        $resp = $this->curlRequest($url,$accountjson,"POST",$accessToken); 
        return @$resp['data']['0']['details']['id'];
          
    }

  public function checkExistingaccount($AccName,$accessToken)
    {
      $url = "https://www.zohoapis.com/crm/v2/Accounts/search?criteria=(Company:equals:".$AccName.")";
      //$query ="authtoken=".$this->apiauthtoken."&scope=crmapi&criteria=(Email:".$email.")";
      
        $srch_result = $this->curlRequest($url,"","GET",$accessToken);  
        $accountId ='';
         if(isset($srch_result['data']['0'])){
             $accountId = $srch_result['data']['0']['id'];
         } 
        return $accountId;

        } 

    public function checkExistingContact($email,$accessToken)
    {
        $url = "https://www.zohoapis.com/crm/v2/Contacts/search?criteria=(Email:equals:".$email.")";
        //$query ="authtoken=".$this->apiauthtoken."&scope=crmapi&criteria=(Email:".$email.")";

        $srch_result = $this->curlRequest($url,"","GET",$accessToken);          
        $contactId ='';
        if(isset($srch_result['data']['0'])){
        $contactId = $srch_result['data']['0']['id'];


        } 
        return $contactId;

        }           
            

            public function lineitem($item,$serachitemid,$counter,$productdata,$accessToken)
                    {

                      $productid=$item->getProductId();
                      $quantity=$item->getQtyOrdered();
                      $price=$item->getPrice();
                      $discntAmt = $item->getDiscountAmount();
                      $Amount=$quantity*$price;
                      $total=$Amount;
                      if(isset($discntAmt))
                      {
                        //$discntAmt = $item['discount_amount'];
                        $total = $total-$discntAmt;
                      }
                      $Addproduct = array();
                      $Addproduct['product']['id'] = $serachitemid;
                      $Addproduct['product']['name'] = $productdata['name'];
                      $Addproduct['quantity'] = round($quantity,2);
                      $Addproduct['Discount'] = round($discntAmt,2);
                      $Addproduct['net_total'] = round($total,2);
                      $Addproduct['list_price'] = round($price,2);
                      $Addproduct['unit_price'] = round($price,2);
                      $Addproduct['total'] = round($Amount,2);                                            
                         
                      return  $Addproduct;
                     }


             public function updateProductByid($pid,$data,$selctedoptionval,$accessToken)
                    {
                      $productarray = array();
                      if(isset($selctedoptionval['Width'])){
                        $productarray['Width'] = $selctedoptionval["Width"];
                      }
                      if(isset($selctedoptionval['Depth'])){
                        $productarray['Depth'] = $selctedoptionval["Depth"];
                       
                      }
                      if(isset($selctedoptionval['Frame Color'])){
                        $productarray['Frame_Color'] = $selctedoptionval["Frame Color"];
                        
                      }
                      if(isset($selctedoptionval['Surface Color'])){
                        $productarray['Surface_Color'] = $selctedoptionval["Surface Color"];
                        
                      }
                      if(isset($selctedoptionval['Color'])){
                        $productarray['Color'] = $selctedoptionval["Color"];
                      }
                      if(isset($selctedoptionval['Select Users'])){
                        $productarray['Select_Users'] = $selctedoptionval["Select Users"];
                        
                      }
                      if(isset($selctedoptionval['Control Switch'])){
                        $productarray['Control_Switch'] = $selctedoptionval["Control Switch"];
                      }
                       if(isset($selctedoptionval['Legacy SKU'])){
                        $productarray['Legacy_SKU'] = $selctedoptionval["Legacy SKU"];
                      }
                      $productarray['Description'] = $data;
                      $productarray['id'] = $pid;
                      $productJson='{
                        "data":[{'.json_encode($productarray).'}]}';                       

                      $url = "https://www.zohoapis.com/crm/v2/Products?";                 
                      $resp = $this->curlRequest($url,$productJson,"PUT",$accessToken);
                      return $resp;
                  

                    }
                     
                       
            public  function searchBy($sku,$accessToken)
             {
              $url="https://www.zohoapis.com/crm/v2/Products/search?criteria=(Product_Code:equals:".$sku.")";              
              $srch_result = $this->curlRequest($url,"","GET",$accessToken); 
              
                     $productId ='';
                      if(isset($srch_result['data']['0'])){
                          $productId = $srch_result['data']['0']['id'];
                      } 
                     
                      
                     return $productId;              
              }
             
               public function productAdd($data,$selctedoptionval,$accessToken)
              { 
                      $productarray = array();
                      if(isset($selctedoptionval['Width'])){
                        $productarray['Width'] = $selctedoptionval["Width"];
                      }
                      if(isset($selctedoptionval['Depth'])){
                        $productarray['Depth'] = $selctedoptionval["Depth"];
                      
                      }
                      if(isset($selctedoptionval['Frame Color'])){
                        $productarray['Frame_Color'] = $selctedoptionval["Frame Color"];
                        
                      }
                      if(isset($selctedoptionval['Surface Color'])){
                        $productarray['Surface_Color'] = $selctedoptionval["Surface Color"];
                        
                      }
                      if(isset($selctedoptionval['Color'])){
                        $productarray['Color'] = $selctedoptionval["Color"];
                      }
                      if(isset($selctedoptionval['Select Users'])){
                        $productarray['Select_Users'] = $selctedoptionval["Select Users"];
                        
                      }
                      if(isset($selctedoptionval['Control Switch'])){
                        $productarray['Control_Switch'] = $selctedoptionval["Control Switch"];
                      }
                      if(isset($legacy_array['Legacy SKU'])){
                        $productarray['Legacy_SKU'] = $selctedoptionval["Legacy SKU"];
                      }

                      $productarray['Product_Name'] = $data['name'];
                      $productarray['Product_Owner'] = "Christian Voelkers";
                      $productarray['Description'] = urlencode($data['Description']);
                      //$productarray['Qty_in_Stock'] = $data['qty'];
                      $productarray['Unit_Price'] = $data['price'];
                      $productarray['Product_Code'] = $data['code'];                   
                      $productjson = '{"data": ['.json_encode($productarray).']}';
                      
                      $url = "https://www.zohoapis.com/crm/v2/Products?";                 
                      $resp = $this->curlRequest($url,$productjson,"POST",$accessToken);
                      
                      if(isset($resp['data']['0'])){
                        $productId = $resp['data']['0']['details']['id'];
                        return $productId;
                    }
                    else{
                      return "";
                    } 
 
              }
              public function curlRequest($url,$query=null,$method,$accessToken=null)
                {
                    //$accessToken = $this->access_token();              
                    $header_arry = array('Authorization: Zoho-oauthtoken '.$accessToken);            
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arry);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$query);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
                    $server_output = curl_exec($ch);    
                    $responseResult = json_decode($server_output,true);
                    return $responseResult;
                    curl_close ($ch);
                }

              public function access_token()
              {
                  $refreshToken = "1000.b93b246d29a62f004b3d87affc16bb1e.b253c8869a765437d696c13f8ca48b9d";
                  $refreshTokenRequest = "https://accounts.zoho.com/oauth/v2/token?refresh_token=".$refreshToken."&client_id=1000.O24BGGFDR23G809087IR2XMO1JYYF5&client_secret=ae9cc8aa1458ab0744af97d13af63e7c2bc27e7980&grant_type=refresh_token";

                  $curl = curl_init();
                  curl_setopt($curl, CURLOPT_URL,$refreshTokenRequest);
                  curl_setopt($curl, CURLOPT_POST, 1);
                  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                  $response = curl_exec($curl);
                  $err = curl_error($curl);
                  curl_close($curl);
                  if ($err) {
                    echo "cURL Error #:" . $err;
                  } else {
                    $result = json_decode($response);
                  }

                  ///$accessToken = $result->access_token;
                 // $accessToken =  "1000.d9e25967803ac287699d6c39ec61d1f0.95e6a2e786cbdcd11fc887e9f534fda0";
                  return $result->access_token;
              }
}

