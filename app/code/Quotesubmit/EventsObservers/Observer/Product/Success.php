<?php

namespace Quotesubmit\EventsObservers\Observer\Product;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
class Success implements ObserverInterface
{ 

    protected $orderRepository;
    public $apiauthtoken = "03d95794d824e8c2c65a2a2b6cfedc65";
   
    
    protected $_productloader;
	
    public function __construct(
    OrderRepositoryInterface $OrderRepositoryInterface, 
	\Magento\Catalog\Model\ProductFactory $_productloader
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
        
         $accessToken =  $this->generate_access_token(); 
       //echo  $accessToken;

        $Quote_ids = $observer->getEvent()->getquote_ids()[0];
    		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    		$quoteFactory = $objectManager->create('\Magento\Quote\Model\QuoteFactory');
		

        $configurationHelper=$objectManager->get('Magento\Catalog\Helper\Product\Configuration');
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('lof_rfq_quote'); 
        $sql = $connection->select()->from($tableName)->where('entity_id = ?', $Quote_ids); 
        $result = $connection->fetchRow($sql);
    
        $tableName = $resource->getTableName('quote_item'); 
        $sql = $connection->select()->from($tableName)->where('quote_id = ?', $result["quote_id"]);
        $resultitems = $connection->fetchAll($sql);  

        $tableName = $resource->getTableName('quote_address'); 
        $sql = $connection->select()->from($tableName)->where('quote_id = ?', $result["quote_id"]);
        $resultquote_address = $connection->fetchAll($sql);     
	
        $region = "";
		if(isset($resultquote_address[0]))
        {
          $region = $resultquote_address[0]['region'];
        }
		
        $tableName = $resource->getTableName('quote'); 
        $sql = $connection->select()->from($tableName)->where('entity_id = ?', $result["quote_id"]); 
        $orignalquote = $connection->fetchRow($sql);

            $customeremail=@$orignalquote['customer_email'];   
            $subtotal=@$orignalquote['subtotal'];
            $grand_total=@$orignalquote['grand_total'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $quotegeneratedid=@$result['increment_id'];
            $expiry=@$result['expiry'];
            $customer_note=@$orignalquote['customer_note'];
            //echo $customer_note;
            $address=@$result['address'];
            $city=@$result['city'];
            $company=@$result['company'];
            $telephone=@$result['telephone'];
            $country = @$result['country_id'];
            $postcode = @$result['postcode'];
           
           // echo "<pre>";
		   // print_r($grand_total);
		   // die();
  /*Question data*/

  
            $questionss = $result['question'];	
          
            $adress2val ='';
            $industryType ='';
            $source ='';
            $industry ='';
			// $totaltax =00;
            if($questionss)
            {
            	$unserquestion  = unserialize($questionss);
                if(!empty($unserquestion))
            	{
            		//$question_3 = $unserquestion;
            		
            		$question_1 = isset($unserquestion['question_1'])?$unserquestion['question_1']:'';
            		if($question_1)
            		{
            			if($question_1['label'] == "Address 2")
            			{
            				$adress2val = $question_1['value'];
            			}
            		}
            		$question_2 = isset($unserquestion['question_2'])?$unserquestion['question_2']:'';
            		if($question_2)
            		{
            			if($question_2['label'] == "What Industry Are You In?")
            			{
            				$industry = $question_2['value'];
            			}
            		}
            		$question_3 = isset($unserquestion['question_3'])?$unserquestion['question_3']:'';
            		if($question_3)
            		{
            			if($question_3['label'] == "How Did You Hear About Us?")
            			{
            				$source = $question_3['value'];
            			}
            		}
            		$question_4 = isset($unserquestion['question_4'])?$unserquestion['question_4']:'';
            		if($question_4)
            		{
            			if($question_4['label'] == "Type?")
            			{
            				$industryType = $question_4['value'];
            			}
            		}

            	} 
            }

            $subtotal=@$orignalquote['subtotal'];
			
                    if(@$orignalquote['subtotal_with_discount'])
                    {
                    $discount1=@$orignalquote['subtotal']-@$orignalquote['subtotal_with_discount'];
                    }else
                    {
                      $discount1="0.00";
                    }
                    // $grand_total=@$orignalquote['grand_total'];
                   /*     Question data END        */
                   $salesorderarray = array();
                   if($region == "CA" && $region == "CA"){
                    $taxper = 8.25;
                    $totaltax= $subtotal*8.25/100;
                    $salesorderarray['$line_tax'][] = array("percentage"=>$taxper,"name"=>"Sales Tax");
                    $grand_t=floatval($totaltax)+floatval($subtotal);
                    $grand_total =  round(floatval($grand_t), 2);
                  }else{
                    $totaltax="0.00";
                  }

                  $TermsConditions = "All products are designed and assembled in the USA Price quote is good for 180 days from creation date Lifetime Warrenty on all products Payment Terms : Wire Transfer , Credit Card, Check , Net 30";
                  $stage = "Not Contacted";
                  $Brand = "VersaTables";
                  $salesorderarray['Subject'] = $result['last_name'].' , '.@$result['first_name'].' - Quote';
                  //$salesorderarray['Deal_Name']['name'] = "Furniture System COmpany-";
                  $salesorderarray['Sub_Total'] = round($subtotal,2);
                  $salesorderarray['Grand_Total'] = round($grand_total,2);
                  $salesorderarray['First_Name'] = $result['first_name'];
                  $salesorderarray['Tax'] = round($totaltax,2);
                  $salesorderarray['Notes1'] = $customer_note;
                 
                 
                  
                  $salesorderarray['Last_Name'] = $result["last_name"];
                  $salesorderarray['Name1'] = $result["first_name"].' '.$result["last_name"];
                  $salesorderarray['Brands'] = $Brand;
                  $salesorderarray['Quote_Stage'] = "Quote Sent";
                  $salesorderarray['SOURCES'] = "VERSATABLES WEBSITE QUOTE";
                  $salesorderarray['IP_Address'] = $ip;
                  
                  $salesorderarray['Email'] = $result["email"];
                  $salesorderarray['Telephone'] = $telephone;
                  $salesorderarray['Company'] = $company;
                 $salesorderarray['Terms_and_Conditions'] = $TermsConditions;           
                 
                  $salesorderarray['Billing_Street'] = $adress2val;
                  $salesorderarray['Billing_State']= $region;                 
                  $salesorderarray['Address']= $address;
                  $salesorderarray['Billing_City']= $city;
                  $salesorderarray['Billing_Code']= $postcode;
                  $salesorderarray['telephone']= $telephone;
                  $salesorderarray['Billing_Country']= $country;
                 // $salesorderarray['Address2']= $adress2val;
                  /*
                  $salesorderarray['Shipping_Street'] = $address;
                  $salesorderarray['Shipping_State']= $region;                 
                  $salesorderarray['Address']= $address;
                  $salesorderarray['Shipping_City']= $city;
                  $salesorderarray['Shipping_Code']= $postcode;
                  $salesorderarray['Telephone']= $telephone;
                  $salesorderarray['Shipping_Country']= $country;*/
                  //$salesorderarray['Address2']= $adress2val;

                    $contactid=$this->checkExistingContact($result["email"],$accessToken);                   
                    if(!empty($contactid))
                    {                   
                     $contactid=$this->checkExistingContact($result["email"],$accessToken);
                    }else
                    {
                     $contactid = $this->createContact($salesorderarray,$accessToken);
                    // echo "INELSE".$contactid;   
                    }
                    $salesorderarray['Contact_Name']['id'] = $contactid;
                  $account=$this->checkExistingAccount($company,$accessToken);

                  if(!empty($account))
                  {
                  //echo "if";
                  $accountid=$this->checkExistingAccount($company,$accessToken);
                  //print_r($accountid);
                  }
                  else
                  { 
                  //echo "else";
                  $accountid = $this->createAccount($salesorderarray,$accessToken);
                  //print_r($accountid);
                  }
                  $salesorderarray['Account_Name']['id']=$accountid;
                   
                  
                  if($industryType != "")
                  {
                     $salesorderarray['Type'] = $industryType;
                     
                  }
                  if($source != "")
                  {
                    $salesorderarray['Referrered_By'] = $source;
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

                  //$salesorderjson.='"Discount":"00</FL>';
                  $salesorderarray['QuotesID'] = "VT".$quotegeneratedid;
                  $salesorderarray['VSD_Quote_Number'] = $quotegeneratedid;
                  $salesorderarray['Name'] = $result['first_name'].' '.$result['last_name'];
                  $salesorderarray['SOURCE'] = "VersaTables Website Quote";
                  //$salesorderarray['Quote_Expiration_Date'] = $expiry;
                  $salesorderarray['Date'] = date('Y-m-d');                 

      
     /************      ITEM DATA   **************/
                  $counter=1;
                  $totalDiscount = 0;
					
					/* custom sku code */
          $salesorder_itemarray = array();
                  foreach ($resultitems as $item)
                        { 
                          $tableName = $resource->getTableName('quote_item_option'); 
                          $sql = $connection->select()->from($tableName)->where('item_id = ?', $item["item_id"]); 
                          $quote_item_option = $connection->fetchRow($sql);
                          $selected_option = array();
                          if(isset($quote_item_option['value']) && !empty($quote_item_option['value']))
                          {
                             $unserquote_item  = json_decode($quote_item_option['value'],true); 
                             if(isset($unserquote_item['options']) && !empty($unserquote_item['options']))
                             {
                                 array_push($selected_option,$unserquote_item['options']);
                             }
								
                          }

                          $items[$counter]['name']=@$item['name'];          
                          $options='';              
                          $items[$counter]['ordernumber']=$quotegeneratedid;             
                          $items[$counter]['price']=@$item['price'];                         
                          $items[$counter]['sku']=@$item['sku'];
                          $items[$counter]['quantity'] = @$item['qty'];
                          $product=$this->getLoadProduct(@$item['product_id']);
                          $totalOption = array();
                          $customOptions = $objectManager->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($product);
                          $optionskeyloop = $customOptions->getData();
                          $optionKeyarry = array();
                          foreach ($optionskeyloop as $key => $value) {
                          	$optionKeyarry[$value['option_id']] = $value['title'];
                          }

                          foreach ($customOptions as $customOption) {
                            $values = $customOption->getValues();

                            if(isset($values) && !empty($values)){
                							foreach ($values as $value) {
                							   $valueData = $value->getData();
                                  //print_r($valueData);
                							   $totalOption[$valueData['option_id']][$valueData['option_type_id']] = $valueData['title'];
                							   }		
                                }				   
                                          
                							}
                             
                							$selctedoptionval = array();
                              $skuarray = array();
                              $legacy_array = array();
                              
                               
                           foreach($selected_option[0] as $key=>$val)
                           {  
                              if($optionKeyarry[$key] == "Sku" || $optionKeyarry[$key] == "sku" || $optionKeyarry[$key] == "SKU")
                              {
                                 //echo "inif".$optionKeyarry[$key];
                                 $skuarray[$optionKeyarry[$key]] = $val;
                              }
                              elseif(isset($optionKeyarry[$key]) && $optionKeyarry[$key] == "Legacy SKU")
                              {
                              	//$legacy_array
                                 $legacy_array[$optionKeyarry[$key]] = $val;


                              }
                              else
                              {
                                //echo "elseddd".$optionKeyarry[$key]."<br/>";
								//print_r($totalOption[$key][$val]);
                                $selctedoptionval[$optionKeyarry[$key]] = $totalOption[$key][$val];
								//print_r($totalOption);
                              }

                           }
                           
                          $productdata=array();
                          $productdata['id']=$product->getData('entity_id');
                          $productdata['name']=$product->getData('name');
                          $productdata['option']=$options;
                          $productdata['qty'] = $item['qty'];
                         

                          $productdata['price']=$product->getData('price');
                          $productdata['Description']=strip_tags($product->getData('description'));
                          $items[$counter]['description']=strip_tags($product->getData('description'));
                          $productdata['created_at']=$product->getData('created_at');
                          $productdata['updated_at']=$product->getData('updated_at');

                          $productdata['quantity_in_stock']=$product->getData('quantity_and_stock_status/qty');                  
                          if(isset($skuarray) && !empty($skuarray))
                         {

                            foreach ($skuarray as $value) {
                               $SKUVAL = $value;
                            }
                             $productdata['code'] = $SKUVAL; 
                              $srchrecord = $this->searchBy($SKUVAL,$accessToken);
                         }
                         else
                         {
                           $productdata['code']=$product->getData('sku');
                         }
                         
                         
                           if(!empty($srchrecord))
                           {
                             $serachitemid = $srchrecord;
                            // echo "inIF";
                           }else{
                            //echo "inELSE";
                             $serachitemid =  $this->productAdd($productdata,$selctedoptionval,$accessToken);
                           }
                         
                          if(!empty($serachitemid))
                            {
                              //print_r($this->lineitem($item,$serachitemid,$counter,$productdata));
                                 $salesorder_itemarray[] = $this->lineitem($item,$serachitemid,$counter,$productdata);
                                 echo "<br/>";
                         //print_r( $salesorder_itemarray);
                                 $this->updateProductByid($serachitemid,$productdata['Description'],$selctedoptionval,$legacy_array,$accessToken);
                            }
                         
                          $counter++;

                           $totalDiscount = $item['discount_amount']+$totalDiscount;  
                      }
                     $salesorderarray['Product_Details'] = $salesorder_itemarray;
                      //$salesorderarray['Discount'] = $totalDiscount;
                      //$salesorder_itemarray = array(); 
                      //echo json_encode($salesorderarray);
                   
                      $salesorder_jsondata='{
                        "data":['.json_encode($salesorderarray).']}';
                       //echo $salesorder_jsondata;
                        $url = "https://www.zohoapis.com/crm/v2/Quotes";
                        $resp = $this->curlRequest($url,$salesorder_jsondata,"POST",$accessToken); 
                       
                        return $resp;
                   
                    

      /************    END   ITEM DATA   **************/
              
    }

    /***********createContact IN   ZOHO*************/
      
      public function createContact($data,$accessToken)
      {
        $contactjson = '{
          "data": [
             {
                  "phone": "'.$data['Telephone'].'",
                  "Last_Name": "'.$data['Last_Name'].'",
                  "First_Name":"'.$data['First_Name'].'",
                  "Email": "'.$data['Email'].'"                        
              }]}';
          //echo $contactjson;
          $url = "https://www.zohoapis.com/crm/v2/Contacts?";                 
          $resp = $this->curlRequest($url,$contactjson,"POST",$accessToken); 
          return @$resp['data']['0']['details']['id'];
             
      }
    /***********END  createContact IN ZOHO*************/

    /********************checkExistingContact in zoho*************************************/
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
        

            public function lineitem($item,$serachitemid,$counter,$productdata)
                    {               
                      
                      $productid=@$item['product_id'];
                      $quantity=@$item['qty'];
                      $price=@$item['price'];
                      $Amount=$quantity*$price;
                      $total=$Amount;
                      if(isset($item['discount_amount']))
                      {
                        $discntAmt = $item['discount_amount'];
                        $total=$total-$discntAmt;
                      }
                      $Addproduct = array();
                      $Addproduct['product']['id'] = $serachitemid;
                      $Addproduct['product']['name'] = $productdata['name'];
                      $Addproduct['quantity'] = round($quantity,2);
                      $Addproduct['Discount'] = $discntAmt;
                      $Addproduct['net_total'] = round($total,2);
                      $Addproduct['list_price'] = round($price,2);
                      $Addproduct['unit_price'] = round($price,2);
                      $Addproduct['total'] = round($Amount,2);                                            
                         
                      return  $Addproduct;
                     }





            public function updateProductByid($pid,$data,$selctedoptionval,$legacy_array,$accessToken)
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

                    public function checkExistingAccount($company,$accessToken)
                    {
                         $url = "https://www.zohoapis.com/crm/v2/Accounts/search?criteria=(Company:equals:".$company.")";
                         //$query ="authtoken=".$this->apiauthtoken."&scope=crmapi&criteria=(Email:".$email.")";
                         
                           $srch_result = $this->curlRequest($url,"","GET",$accessToken);  
                           $accountId ='';
                            if(isset($srch_result['data']['0'])){
                                $accountId = $srch_result['data']['0']['id'];
                            } 
                           return $accountId;
                
                        } 
                  public function createAccount($salesorderarray,$accessToken)
                    {
                      $accountjson = '{
                                "data": [
                                   {
                                    "Company": "'.$salesorderarray['Company'].'",
                                    "Billing_Country":"'.$salesorderarray['Billing_Country'].'",
                                    "Billing_Street": "'.$salesorderarray['Billing_Street'].'",
                                    "Billing_Code":"'.$salesorderarray['Billing_Code'].'",
                                    "Billing_City": "'.$salesorderarray['Billing_City'].'",
                                    "Billing_State":"'.$salesorderarray['Billing_State'].'",
                                    "Shipping_Street":"'.$salesorderarray['Shipping_Street'].'",
                                    "Shipping_State":"'.$salesorderarray['Shipping_State'].'",
                                    "Shipping_City":"'.$salesorderarray['Shipping_City'].'",
                                    "Shipping_Country":"'.$salesorderarray['Shipping_Country'].'",
                                    "Shipping_Code": "'.$salesorderarray['Shipping_Code'].'",
                                    "Account_Name":"'.$salesorderarray['Company'].'"
                                    }]}';
                                //echo $contactjson;
                                $url = "https://www.zohoapis.com/crm/v2/Accounts?";                 
                                $resp = $this->curlRequest($url,$accountjson,"POST",$accessToken); 
                                return @$resp['data']['0']['details']['id'];
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
              //return json_decode($serachResult,true);
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

              public function curlRequest($url,$query=null,$method,$accessToken =null)
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
                public function generate_access_token()
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
                   
                  return $result->access_token;
              }
}

