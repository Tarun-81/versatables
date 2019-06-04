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
         							 
                  $url = "https://crm.zoho.com/crm/private/json/SalesOrders/insertRecords?";   
                  $query = "authtoken=".$this->apiauthtoken."&scope=crmapi&newFormat=1&wfTrigger=true&xmlData=";
                  $order = $this->orderRepository->get($order_ids);    
                    //$statusHistoryItem = $order->getStatusHistoryCollection()->getFirstItem();  
                      

                  
                  $order_id = $order->getIncrementId();   
                  $customer_email = $order->getCustomerEmail();
                  $shipping_address = $order->getShippingAddress();
                  //$cusomerNote =  $order->getData('customer_note');
                 
                    
                  $shipping = array();
                  $shipping['firstname']=$shipping_address->getFirstname();
                  $shipping['lastname']=$shipping_address->getLastname();
                  $shipping['city']=$shipping_address->getCity();
                  $shipping['company']= $shipping_address->getCompany();
                  //$shipping['street']= $shipping_address->getStreetFull();
                  $shipping['street'] = "";
                  $shipStreet = $order->getBillingAddress()->getStreet();
                  if(isset($shipStreet) && !empty($shipStreet))
                    {
                      foreach ($shipStreet as $key => $value) {
                       $shipping['street'] .= $value;
                       $shipping['street'] .= ",";

                    }
                  }
                  $shipping['region']= $shipping_address->getRegion();
                  $shipping['city']= $shipping_address->getCity();
                  $shipping['postalcode']= $shipping_address->getPostcode();
                  $shipping['telephone']= $shipping_address->getTelephone();
                  $shipping['countryid']= $shipping_address->getCountryId();
                  $billing_address = $order->getBillingAddress();
                  $billing =array();
                  $billing['firstname']=$billing_address->getFirstname();
                  $billing['lastname']=$billing_address->getLastname();
                  $billing['city']=$billing_address->getCity();
                  $billing['company']= $billing_address->getCompany();

                  //$billing['street']= $billing_address->getStreetFull();
                /*  $additionalInformation = $order->getPayment()->getAdditionalInformation();
                  echo "<PRE>";
                  print_r($additionalInformation);
                 die;*/
                  $billing['street'] = "";
                  $billStreet = $order->getShippingAddress()->getStreet();
                  if(isset($billStreet) && !empty($billStreet))
                    {
                      foreach ($billStreet as $key => $value) {
                       $billing['street'] .= $value;
                       $billing['street'] .=",";
                    }
                  }
                  $billing['region']= $billing_address->getRegion();
                  $billing['city']= $billing_address->getCity();
                  $billing['postalcode']= $billing_address->getPostcode();
                  $billing['telephone']= $billing_address->getTelephone();
                  $billing['countryid']= $billing_address->getCountryId();
                  $payment = $order->getPayment();
                  $method = $payment->getMethodInstance();
                  $methodTitle = $method->getTitle();
                  $insertData=array();
                  $insertData['fname'] = $shipping_address->getFirstname();
                  $insertData['lname'] = $shipping_address->getLastname();
                  $insertData['email'] = $order->getCustomerEmail();
                  $insertData['phone'] = $shipping_address->getTelephone();
                  $grandtotal=$order->getGrandTotal();
                  $subtotal=$order->getSubtotal();  
                  $discount=$order->getDiscountAmount();  
                  $couponcode=$order->getCouponCode();
                  $shippingCost = $order->getShippingAmount();

                 

                  $contactid=$this->checkExistingContact($customer_email);
                  //echo "Customer Detail".$customer_email;
                  if(!empty($contactid))
                    {
                      //echo "INIF".$contactid;
                      $contactid=$this->checkExistingContact($customer_email);
                    }else
                    {
                      $contactid=$this->createContact($insertData);
                      //echo "INELSE".$contactid;   
                    }

                

                  $ipaddress=$_SERVER['REMOTE_ADDR'];
                  $xml='<SalesOrders><row no="1">';
                  $xml.='<FL val="Billing First Name">'.$billing['firstname'].'</FL>';
                  $xml.='<FL val="Phone">'.$billing['telephone'].'</FL>';
                  $xml.='<FL val="Purchase Order">#'.$purchase_order_val.'</FL>';
                   if(isset($addionalinfo))
                   {
                        $keys = array_keys($addionalinfo);
                						for($i=0; $i < count($keys); ++$i) {
                						//echo $keys[$i] . ' ' . $addionalinfo[$keys[$i]] . "\n";
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

                     $xml.='<FL val="Industry">'.@$industry.'</FL>';
                    //Industry and Segement Mapping
                     if($industry == "Advertising" || $industry == "Agriculture" || $industry == "Apparel" || $industry == "Architecture" || $industry == "Banking/Financial/Investment" || $industry == "Chiropractic" || $industry == "Construction" || $industry == "Food Service" || $industry == "Insurance" || $industry == "Legal" || $industry == "Manufacturing" || $industry == "Media/Broadcasting" || $industry == "Non-Profit" || $industry == "Pharmaceutical" || $industry == "Professional Services" || $industry == "Real Estate" || $industry == "Retail" || $industry == "Technology" || $industry == "Telecommunications" || $industry == "Transport") 
                      {
                        $segement = "Corporate";
                        $xml.='<FL val="Segment">'.$segement.'</FL>';
                      }
                      elseif ($industry == "Education")
                      {
                        $segement = "Education";
                        $xml.='<FL val="Segment">'.$segement.'</FL>';
                      }
                      elseif ($industry == "Government")
                      {
                        $segement = "Government";
                        $xml.='<FL val="Segment">'.$segement.'</FL>';
                      }
                      elseif ($industry == "Healthcare" || $industry == "Hospitality")
                      {
                        $segement = "Medical";
                        $xml.='<FL val="Segment">'.$segement.'</FL>';
                      }
                      elseif ($industry == "Other")
                      {
                        $segement = "Other";
                        $xml.='<FL val="Segment">'.$segement.'</FL>';
                      }
                      else
                      {
                        $segement = "";
                        $xml.='<FL val="Segment">'.$segement.'</FL>';
                      }


                  }
                   //die;
           				//$xml.='<FL val="Industry">'.$Industry.'</FL>';
						$xml.='<FL val="Referred by">'.$Referred.'</FL>';
						$xml.='<FL val="Type">'.$Type.'</FL>';

                 if(!empty($billing['company']))
                    {
                      $AccName = $billing['company'];
                      
                         $accountid=$this->checkExistingaccount($AccName);
                         if(!empty($accountid))                          
                         {
                          //echo "inif".$accountid;
                          $xml.='<FL val="ACCOUNTSID">'.$accountid.'</FL>';
                         }
                        else
                        {

                          $accountid=$this->createAccount($shipping,$billing,$AccName,$customer_email);
                          //echo "inelse".$accountid;
                           $xml.='<FL val="ACCOUNTSID">'.$accountid.'</FL>';

                        }
                        
                         
                    }
                    else
                    {
                      $AccName = $billing['firstname'].' '.$billing['lastname'];
                      
                         $accountid=$this->checkExistingaccount($AccName);
                         if(!empty($accountid))                          
                         {
                          //echo "inif".$accountid;
                          $xml.='<FL val="ACCOUNTSID">'.$accountid.'</FL>';
                         }
                        else
                        {

                          $accountid=$this->createAccount($shipping,$billing,$AccName,$customer_email);
                          //echo "inelse".$accountid;
                           $xml.='<FL val="ACCOUNTSID">'.$accountid.'</FL>';

                        }
                      ///$xml.='<FL val="Account Name">'.$billing['firstname'].' '.$billing['lastname'].'</FL>'; 
                    }
                  $grandtotal1 = round($grandtotal,2);
                  $shippingCost1 = round($shippingCost,2);
                  $xml.='<FL val="Email">'.$customer_email.'</FL>';
                  $xml.='<FL val="Billing Last Name">'.$billing['lastname'].'</FL>';
                  $xml.='<FL val="Shipping First Name">'.$shipping['firstname'].'</FL>';
                  $xml.='<FL val="Shipping Last Name">'.$shipping['lastname'].'</FL>';
                  $xml.='<FL val="Total">'.$grandtotal1.'</FL>';
                  $xml.='<FL val="Brand">VersaTables</FL>';
                  $xml.='<FL val="Subject">'.$billing['firstname'].','.$billing['lastname'].'- Salesorder</FL>';
                  $xml.='<FL val="Order ID">'.$order_id.'</FL>';
                  $xml.='<FL val="Due Date">'.Date('Y-m-d').'</FL>';
                  $xml.='<FL val="Customer Name_ID">'.$contactid.'</FL>';
                  //$xml.='<FL val="Contact Name">John Smith</FL>';
                  $xml.='<FL val="Order Status">'.$order_status.'</FL>';
                  
                  $xml.='<FL val="IP Address">'.$ipaddress.'</FL>';
                  $xml.='<FL val="IP Address">'.$ipaddress.'</FL>';
                  $xml.='<FL val="Source">Versatables Website Purchase</FL>';
                  $xml.=' <FL val="Shipping Method">'.$methodTitle.'</FL>';
                  $xml.=' <FL val="Shipping Charge">'.$shippingCost1.'</FL>';
                  $xml.=' <FL val="Billing Company Name">'.$shipping['company'].'</FL>';
                  $xml.=' <FL val="Shipping Company Name">'.$billing['company'].'</FL>';

                  $xml.=' <FL val="Billing Code">'.$shipping['postalcode'].'</FL>';
                  $xml.=' <FL val="Shipping Code">'.$billing['postalcode'].'</FL>';

                  $xml.=' <FL val="Tax">'.$tax_amount.'</FL>';
                  $xml.=' <FL val="Sub Total">'.$subtotal.'</FL>';
                  $xml.=' <FL val="Grand Total">'.$grandtotal.'</FL>';
                  $xml.=' <FL val="Total">'.$subtotal.'</FL>';
                  $xml.='<FL val="Discount">'.$discount.'</FL>';
                  $xml.=' <FL val="Total After Discount">'.$grandtotal.'</FL>';
                  $xml.=' <FL val="Billing Street">'.$billing['street'].'</FL>';
                  $xml.=' <FL val="Shipping Street">'.$shipping['street'].'</FL>';
                  $xml.=' <FL val="Billing City">'.$shipping['city'].'</FL>';
                  $xml.='<FL val="Shipping City">'.$shipping['city'].'</FL>';
                  $xml.=' <FL val="Billing_State">'.$billing['region'].'</FL>';
                  $xml.='<FL val="Shipping_State">'.$shipping['region'].'</FL>';
                  $xml.=' <FL val="Shipping_Country">'.$billing['countryid'].'</FL>';
                  $xml.=' <FL val="Description">'.$ordercomment.'</FL>';
                  $xml.=' <FL val="Product Details">';
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
                                  $resultSearchitemID=$this->searchBy($productsku['sku']);
                                  //print_r($resultSearchitemID);
                            }
                          if(isset($resultSearchitemID) && !empty($resultSearchitemID))
                          {
                            //echo "inisel";
                            $serachitemid = $resultSearchitemID;
                          }else{
                            //echo "insels";
                            $serachitemid =  $this->productAdd($productdata,$optionsarray);
                          }
                          
                          if(!empty($serachitemid))
                            {
                                 $xml.=$this->addProduct($item,$serachitemid,$counter);
                                 $this->updateProductByid($serachitemid,$productdata,$optionsarray);
                            }

                            $counter++;
                      } 
                  $xml.='</FL></row></SalesOrders>';
                  $query.=$xml;
                 
              /*  echo "<PRE>";
                  print_r($productdata);
                 die("dsfdsfds");*/
                  $result=$this->curlRequest($url,$query);
				//  print_r($result);
				  //die();
                // echo "testing 1231<br>";
    }

  public function createContact($data)
  {
          $xmlcontact='<Contacts><row no="1">';
          $xmlcontact.='<FL val="First Name">'.$data['fname'].'</FL>';
          $xmlcontact.='<FL val="Last Name">'.$data['lname'].'</FL>';
          $xmlcontact.='<FL val="Phone">'.$data['phone'].'</FL>';
          $xmlcontact.='<FL val="Email">'.$data['email'].'</FL>';
          $xmlcontact.='</row></Contacts>';
          $url = "https://crm.zoho.com/crm/private/json/Contacts/insertRecords?";    
          $query = "authtoken=".$this->apiauthtoken."&scope=crmapi&newFormat=1&wfTrigger=true&xmlData=".$xmlcontact; 
          $resp = $this->curlRequest($url,$query); 
          $resultcontact =json_decode($resp,true);

          return @$resultcontact['response']['result']['recorddetail']['FL'][0]['content'];
         
  }

   public function createAccount($shipping,$billing,$AccName,$email)
  {
          $xmlaccount='<Accounts><row no="1">';
          $xmlaccount.='<FL val="Account Name">'.$AccName.'</FL>';
          $xmlaccount.='<FL val="Billing Street">'.$billing['street'].'</FL>';
          $xmlaccount.='<FL val="Shipping Street">'.$shipping['street'].'</FL>';
          $xmlaccount.='<FL val="Billing City">'.$billing['city'].'</FL>';
          $xmlaccount.='<FL val="Shipping City">'.$shipping['city'].'</FL>';
          $xmlaccount.='<FL val="Billing State">'.$billing['region'].'</FL>';
          $xmlaccount.='<FL val="Shipping State">'.$shipping['region'].'</FL>';
          $xmlaccount.='<FL val="Billing Code">'.$billing['postalcode'].'</FL>';
          $xmlaccount.='<FL val="Shipping Code">'.$shipping['postalcode'].'</FL>';
           $xmlaccount.='<FL val="Billing Country">'.$billing['countryid'].'</FL>';
           $xmlaccount.='<FL val="Phone">'.$billing['telephone'].'</FL>';
          
          $xmlaccount.='<FL val="Shipping Country">'.$shipping['countryid'].'</FL>';
          //$xmlaccount.='<FL val="Source">Versatable Website Order</FL>';
          $xmlaccount.='<FL val="Brand">Versatable</FL>';

          $xmlaccount.='<FL val="Email">'.$email.'</FL>';
          $xmlaccount.='</row></Accounts>';
          $url = "https://crm.zoho.com/crm/private/json/Accounts/insertRecords?";    
          $query = "authtoken=".$this->apiauthtoken."&scope=crmapi&newFormat=1&wfTrigger=true&xmlData=".$xmlaccount; 
          $resp = $this->curlRequest($url,$query); 
          $resultcontact =json_decode($resp,true);

          return @$resultcontact['response']['result']['recorddetail']['FL'][0]['content'];
         
  }

  public function checkExistingaccount($AccName)
    {
         $url = "https://crm.zoho.com/crm/private/json/Accounts/searchRecords";
         $query ="authtoken=".$this->apiauthtoken."&scope=crmapi&criteria=(Account Name:".$AccName.")";
         //echo "urlQuery----".$url.$query;
           $srch_result = json_decode($this->curlRequest($url,$query),true);
           $accountId ='';
            if(isset($srch_result['response']['result'])){
              if(isset($srch_result['response']['result']['Accounts']['row']['FL']))
              {
                $contact_result = @$srch_result['response']['result']['Accounts']['row']['FL'];

                    foreach ($contact_result as $rows){
                        //print_r($rows);
                        if($rows['val'] == 'ACCOUNTID')
                                 {                          
                                    $accountId = $rows['content'];
                                  }
                         }
              }
                 
            }
            return $accountId;

        } 

    public function checkExistingContact($email)
    {
         $url = "https://crm.zoho.com/crm/private/json/Contacts/searchRecords";
         $query ="authtoken=".$this->apiauthtoken."&scope=crmapi&criteria=(Email:".$email.")";
         //echo "urlQuery----".$url.$query;
           $srch_result = json_decode($this->curlRequest($url,$query),true);

           $contactId ='';
            if(isset($srch_result['response']['result'])){
                $contact_result = $srch_result['response']['result']['Contacts']['row']['FL'];
                    foreach ($contact_result as $rows){
                        //print_r($rows);
                        if($rows['val'] == 'CONTACTID')
                                 {                          
                                    $contactId = $rows['content'];
                                  }
                         }
                 
            }
            return $contactId;

        }           
            public function curlRequest($url,$query)
              {

                $ch1 = curl_init();
                curl_setopt($ch1, CURLOPT_URL, $url);
                curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch1, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch1, CURLOPT_POST, 1);
                curl_setopt($ch1, CURLOPT_POSTFIELDS, $query);
                $response1 = curl_exec($ch1);
                curl_close($ch1);
                return $response1;
                }

            public function addProduct($item,$serachitemid,$counter)
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
                        $total=$total-$discntAmt;
                      }
           
                     return '<product no="'.$counter.'"><FL val="Product Id">'.$serachitemid .'</FL><FL val="Quantity">'.$quantity.'</FL><FL val="List Price">'.$price.'</FL><FL val="Unit Price">'.$price.'</FL><FL val="Discount">'.$discntAmt.'</FL><FL val="Total">'.$Amount.'</FL><FL val="Net Total">'.$total.'</FL></product>'; 
                     }


             public function updateProductByid($pid,$data,$selctedoptionval)
                    {
                      $xml="";
                      $xml.='<Products><row no="1">';

                      if(isset($selctedoptionval['Width'])){
                        $xml.='<FL val="Width" >'.$selctedoptionval['Width'].'</FL>';
                      }
                      if(isset($selctedoptionval['Depth'])){
                        $xml.='<FL val="Depth" >'.$selctedoptionval['Depth'].'</FL>';
                      }
                      if(isset($selctedoptionval['Frame Color'])){
                        $xml.='<FL val="Frame Color" >'.$selctedoptionval['Frame Color'].'</FL>';
                      }
                      if(isset($selctedoptionval['Surface Color'])){
                        $xml.='<FL val="Surface Color" >'.$selctedoptionval['Surface Color'].'</FL>';
                      }
                      if(isset($selctedoptionval['Color'])){
                        $xml.='<FL val="Color" >'.$selctedoptionval['Color'].'</FL>';
                      }
                      if(isset($selctedoptionval['Select Users'])){
                        $xml.='<FL val="Select Users" >'.$selctedoptionval['Select Users'].'</FL>';
                      }
                      if(isset($selctedoptionval['Control Switch'])){
                        $xml.='<FL val="Control Switch" >'.$selctedoptionval['Control Switch'].'</FL>';
                      }

                      if(isset($selctedoptionval['Legacy SKU'])){
                        $xml.='<FL val="Legacy SKU" >'.$selctedoptionval['Legacy SKU'].'</FL>';
                      }
                       $xml.='<FL val="Parent SKU" >'.$data['skucode'].'</FL>';
                      $xml.='<FL val="Description">'.urlencode($data['Description']).'</FL>';
                      $xml.='<FL val="Taxable">true</FL>';
                      $xml.='</row></Products>';
                      $updateurl = "https://crm.zoho.com/crm/private/xml/Products/updateRecords?";
                                  $updatequery = "authtoken=03d95794d824e8c2c65a2a2b6cfedc65&scope=crmapi&id=".$pid."&xmlData=".$xml;
                                  $updateResult = json_decode($this->curlRequest($updateurl,$updatequery),true);
                  

                    }
                     
                       
            public  function searchBy($sku)
             {
              $serchUrl  = "https://crm.zoho.com/crm/private/json/Products/searchRecords?";
             
              $serchquery = "authtoken=03d95794d824e8c2c65a2a2b6cfedc65&scope=crmapi&criteria=(Product Code:".$sku.")";
              $serachResult = json_decode($this->curlRequest($serchUrl,$serchquery),true);
                if(isset($serachResult['response']['result']['Products']))
                {
                    if(isset($serachResult['response']['result']['Products']['row']['FL']))
                    {
                      return $serachResult['response']['result']['Products']['row']['FL'][0]['content'];
                    }                
                
                 }
             
               //return json_decode($serachResult,true);
              }
              public function uploadProductimage($recordid,$path)
              {
              $imageurl = "https://crm.zoho.com/crm/private/xml/Products/uploadPhoto?authtoken=".$this->apiauthtoken."&scope=crmapi";
              $ch=curl_init();
              curl_setopt($ch,CURLOPT_HEADER,0);
              curl_setopt($ch,CURLOPT_VERBOSE,0);
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
              curl_setopt($ch,CURLOPT_URL,$imageurl);
              curl_setopt($ch,CURLOPT_POST,true);
              $post=array("id"=>$recordid,"content"=> $path);
              curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
              $response=curl_exec($ch);
              echo $response;
                //echo $imageurl.print_r($post);
              }
               public function productAdd($data,$optionsarray)
              { 
     /*
                echo "<pre>";
                print_r($optionsarray);*/
                $xml="";
                $xml.='<Products><row no="1">';
                $xml.='<FL val="Product Owner">Christian Voelkers</FL>';
                $xml.='<FL val="Product Name">'.$data['name'].'</FL>';
                
                      if(isset($optionsarray['Width'])){
                      $xml.='<FL val="Width" >'.$optionsarray['Width'].'</FL>';
                      }
                      if(isset($optionsarray['Depth'])){
                      $xml.='<FL val="Depth" >'.$optionsarray['Depth'].'</FL>';
                      }
                      if(isset($optionsarray['Frame Color'])){
                      $xml.='<FL val="Frame Color" >'.$optionsarray['Frame Color'].'</FL>';
                      }
                      if(isset($optionsarray['Surface Color'])){
                      $xml.='<FL val="Surface Color" >'.$optionsarray['Surface Color'].'</FL>';
                      }
                      if(isset($optionsarray['Color'])){
                      $xml.='<FL val="Color" >'.$optionsarray['Color'].'</FL>';
                      }
                      if(isset($optionsarray['Select Users'])){
                      $xml.='<FL val="Select Users" >'.$optionsarray['Select Users'].'</FL>';
                      } 
                      if(isset($optionsarray['Height Controller Switch'])){
                      $xml.='<FL val="Control Switch" >'.$optionsarray['Height Controller Switch'].'</FL>';
                      }
                $xml.='<FL val="Product Detail" >'.$data['option'].'</FL>';  
                $xml.='<FL val="Product Code">'.$data['code'].'</FL>';
                $xml.='<FL val="Created Time">'.$data['created_at'].'</FL>';
                $xml.='<FL val="Modified Time">'.$data['updated_at'].'</FL>';
                $xml.='<FL val="Qty in Stock">'.$data['qty'].'</FL>';
                $xml.='<FL val="Product Active">true</FL>';
                $xml.='<FL val="Unit Price">'.$data['price'].'</FL>';
                $xml.='<FL val="Description">'.urlencode($data['Description']).'</FL>';
                $xml.='<FL val="Taxable">true</FL>';
                $xml.='</row></Products>';
                $url = "https://crm.zoho.com/crm/private/json/Products/insertRecords?";    
                $query = "authtoken=".$this->apiauthtoken."&scope=crmapi&newFormat=1&wfTrigger=true&xmlData=".$xml; 
                // echo $url."?".$query;
                $resp = $this->curlRequest($url,$query); 
                //echo "<PRE>";
                //print_r($resp);

                $resultcontact =json_decode($resp,true);
                return @$resultcontact['response']['result']['recorddetail']['FL'][0]['content'];
 
              }
}

