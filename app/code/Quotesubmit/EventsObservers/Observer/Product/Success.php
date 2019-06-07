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

        $url = "https://crm.zoho.com/crm/private/json/Quotes/insertRecords?";   
        $query = "authtoken=".$this->apiauthtoken."&scope=crmapi&newFormat=1&wfTrigger=true&xmlData=";
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
            $address=@$result['address'];
            $city=@$result['city'];
            $company=@$result['company'];
            $telephone=@$result['telephone'];
            $country = @$result['country_id'];
            $postcode = @$result['postcode'];

  /*Question data*/

  
            $questionss = $result['question'];	
          
            $adress2val ='';
            $industryType ='';
            $source ='';
            $industry ='';

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
                    $grand_total=@$orignalquote['grand_total'];
        /*     Question data END        */

                  $TermsConditions = "All products are designed and assembled in the USA Price quote is good for 90 days from creation date Lifetime Warrenty on all products Payment Terms : Wire Transfer , Credit Card, Check , Net 30";
                  $stage = "Not Contacted";
                  $Brand = "VersaTables";
                  $xml='<Quotes><row no="1">';
                  $xml.='<FL val="Subject">'.@$result["last_name"].' , '.@$result["first_name"].' - Quote</FL>';
                  $xml.='<FL val="Potential Name">Furniture System COmpany-</FL>';
                  $xml.='<FL val="Sub Total">'.$subtotal.'</FL>';
                  $xml.='<FL val="First Name">'.@$result["first_name"].'</FL>';
                  $xml.='<FL val="Last Name">'.@$result["last_name"].'</FL>';
                  $xml.='<FL val="Account Name">'.@$result["first_name"].' '.@$result["last_name"].'</FL>';
                  $xml.='<FL val="Notes">'.$customer_note.'</FL>';
                  $xml.='<FL val="Terms and Conditions">'.$TermsConditions.'</FL>';
                  $xml.='<FL val="Quote Stage">'.$stage.'</FL>';
                  $xml.='<FL val="Brand">'.$Brand.'</FL>';
                  $xml.='<FL val="Grand Total">'.$grand_total.'</FL>';
                  $xml.='<FL val="Status">pending</FL>';
                  $xml.='<FL val="IP Address">'.$ip.'</FL>';
                   $xml.='<FL val="Grand Total">'.$grand_total.'</FL>';
                    $xml.='<FL val="Sub Total">'.$subtotal.'</FL>';
                    $xml.='<FL val="Discount">'.$discount1.'</FL>';

                  $xml.='<FL val="Telephone">'.$telephone.'</FL>';
                  $xml.='<FL val="Company">'.$company.'</FL>';
                  $xml.='<FL val="Address">'.@$address.'</FL>';
                  $xml.='<FL val="Billing Street">'.@$address.'</FL>';
                  $xml.='<FL val="Shipping Street">'.@$address.'</FL>';
                  $xml.='<FL val="Billing City">'.@$city.'</FL>';
                  $xml.='<FL val="Shipping City">'.@$city.'</FL>';
                  $xml.='<FL val="Billing State">'.@$region.'</FL>';
                  $xml.='<FL val="Shipping State">'.@$region.'</FL>';
                  $xml.='<FL val="Billing Code">'.@$postcode.'</FL>';
                  $xml.='<FL val="Shipping Code">'.@$postcode.'</FL>';
                  $xml.='<FL val="Billing Country">'.@$country.'</FL>';
                  $xml.='<FL val="Shipping Country">'.@$country.'</FL>';
                  if($adress2val != "")
                  {
                    $xml.='<FL val="Address2">'.@$adress2val.'</FL>';
                  }
                  if($industryType != "")
                  {
                     $xml.='<FL val="Type">'.@$industryType.'</FL>';
                  }
                  if($source != "")
                  {
                    $xml.='<FL val="Referred  By">'.@$source.'</FL>';
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

                  $xml.='<FL val="Discount">00</FL>';
                  $xml.='<FL val="QuotesID">'."VT".$quotegeneratedid.'</FL>';
                  $xml.='<FL val="VSD Quote Number">'.$quotegeneratedid.'</FL>';
                  $xml.='<FL val="Created At">'.Date('Y-m-d').'</FL>';
                  $xml.='<FL val="Email">'.@$result["email"].'</FL>';
                  $xml.='<FL val="Name">'.@$result["first_name"].' '.@$result["last_name"].'</FL>';
                  //$xml.='<FL val="Industry">Advertising</FL>';
                  $xml.='<FL val="SOURCE">VersaTables Website Quote</FL>';
                  $xml.='<FL val="Quote Expiration Date">'.@$expiry.'</FL>';
                  $xml.='<FL val="Date">'.date('Y-m-d').'</FL>';
                  

                  $xml.=' <FL val="Product Details">'; 
				 

     /************      ITEM DATA   **************/
                  $counter=1;
                  $totalDiscount = 0;
					
					/* custom sku code */
        
                  foreach ($resultitems as $item)
                        { 
                          $tableName = $resource->getTableName('quote_item_option'); 
                          $sql = $connection->select()->from($tableName)->where('item_id = ?', $item["item_id"]); 
                          $quote_item_option = $connection->fetchRow($sql);
                          $selected_option = array();
                          if(isset($quote_item_option['value']) && !empty($quote_item_option['value']))
                          {
								             $unserquote_item  = json_decode($quote_item_option['value'],true); 
                                 array_push($selected_option,$unserquote_item['options']);
								
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
                              if($optionKeyarry[$key] == "Sku" || $optionKeyarry[$key] == "sku")
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
                                //echo "elseddd".$optionKeyarry[$key];
                                $selctedoptionval[$optionKeyarry[$key]] = $totalOption[$key][$val];
                              }

                           }
                                                     
                        //die;
                          $productdata=array();
                          $productdata['id']=$product->getData('entity_id');
                          $productdata['name']=$product->getData('name');
                          $productdata['option']=$options;
                         

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
                              $srchrecord = $this->searchBy($SKUVAL);
                         }
                         else
                         {
                           $productdata['code']=$product->getData('sku');
                         }
                          
                         // print_r($srchrecord);
                           if(!empty($srchrecord))
                           {
                             $serachitemid = $srchrecord;
                             //echo "inIF";
                           }else{
                            //echo "inELSE";
                             $serachitemid =  $this->productAdd($productdata,$selctedoptionval);
                           }
                          
                          if(!empty($serachitemid))
                            {
                                 $xml.=$this->addProduct($item,$serachitemid,$counter);
                                 $this->updateProductByid($serachitemid,$productdata['Description'],$selctedoptionval,$legacy_array);
                            }
                         
                          $counter++;

                           $totalDiscount = $item['discount_amount']+$totalDiscount;
                      }

                    $xml.='</FL>'; 
                     $xml.='<FL val="Discount">'.$totalDiscount.'</FL>';
                                            
                   // $xml.='<FL val="QuotesID">'.$quotegeneratedid.'</FL>';
                    $xml.='</row></Quotes>';   
                    $query.=$xml;                   
                    //  echo $url.$query;
                   
                    $result=$this->curlRequest($url,$query); 

      /************    END   ITEM DATA   **************/
              
    }

    /***********createContact IN   ZOHO*************/
      
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
    /***********END  createContact IN ZOHO*************/

    /********************checkExistingContact in zoho*************************************/
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
        /*************************************/         
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
                     return '<product no="'.$counter.'"><FL val="Product Id">'.$serachitemid .'</FL><FL val="Quantity">'.$quantity.'</FL><FL val="List Price">'.$price.'</FL><FL val="Unit Price">'.$price.'</FL><FL val="Total">'.$Amount.'</FL><FL val="Net Total">'.$total.'</FL><FL val="Discount">'.$discntAmt.'</FL></product>'; 
                     }
            public function updateProductByid($pid,$data,$selctedoptionval,$legacy_array)
                    {
                      /*$getUrl  = "https://crm.zoho.com/crm/private/json/Products/getRecordById?";

                      $getquery = "authtoken=03d95794d824e8c2c65a2a2b6cfedc65&scope=crmapi&id=".$pid;
                      $getResult = json_decode($this->curlRequest($getUrl,$getquery),true);*/

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
                       if(isset($legacy_array['Legacy SKU'])){
                        $xml.='<FL val="Legacy SKU" >'.$legacy_array['Legacy SKU'].'</FL>';
                      }
                      $xml.='<FL val="Description">'.urlencode($data).'</FL>';
                      $xml.='<FL val="Taxable">true</FL>';
                      $xml.='</row></Products>';
                      $updateurl = "https://crm.zoho.com/crm/private/xml/Products/updateRecords?";
                                  $updatequery = "authtoken=03d95794d824e8c2c65a2a2b6cfedc65&scope=crmapi&id=".$pid."&xmlData=".$xml;
                                  $updateResult = json_decode($this->curlRequest($updateurl,$updatequery),true);
                  
                   /* if(isset($getResult['response']['result']['Products']))
                    {
                        if(isset($getResult['response']['result']['Products']['row']['FL']))
                        {                        
                            $contents = $getResult['response']['result']['Products']['row']['FL'];
                            foreach ($contents as $key => $value) {
                               //echo "<br/>".$value['val'];
                                if($value['val'] == "Description"){
                                  $description =  $getResult['response']['result']['Products']['row']['FL'];
                                   //return $description;
                                }
                                else
                                {
                                  $xmldata ='<Products><row no="1"><FL val="Description">'.urlencode($data).'</FL></row></Products>';
                                  
                                  //echo "<br/><pre>";
                                  //print_r($updateResult);
                                  //return $updateResult;
                                }
                            }                    

                         
                        }     


                    }*/

                    }

 

            public  function searchBy($sku)
             {
                  $serchUrl  = "https://crm.zoho.com/crm/private/json/Products/searchRecords?";                 
               
                  $serchquery = "authtoken=03d95794d824e8c2c65a2a2b6cfedc65&scope=crmapi&criteria=(Product Code:".$sku.")";
                 //echo $serchUrl.$serchquery;
                  
                $serachResult = $this->curlRequest($serchUrl,$serchquery);
                 $resultproduct = json_decode($serachResult,true);

                 $productArray = @$resultproduct['response']['result']['Products'];
                 if(isset($resultproduct['response']['result']['Products'])){
                 
                 return @$resultproduct['response']['result']['Products']['row']['FL'][0]['content'];
                 //echo "<PRE>";
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
               public function productAdd($data,$selctedoptionval)
              { 
              	/*echo "<PRE>";
              	print_r($selctedoptionval);
                die;*/
                  $xml="";
                  $xml.='<Products><row no="1">';
                  $xml.='<FL val="Product Owner">Christian Voelkers</FL>';
                  $xml.='<FL val="Product Name">'.$data['name'].'</FL>';
                  $xml.='<FL val="Product Detail" >'.$data['option'].'</FL>';  
                  $xml.='<FL val="Product Code">'.$data['code'].'</FL>';
                  $xml.='<FL val="Created Time">'.$data['created_at'].'</FL>';
                  $xml.='<FL val="Modified Time">'.$data['updated_at'].'</FL>';
                  $xml.='<FL val="Qty in Stock">'.$data['quantity_in_stock'].'</FL>';
                  $xml.='<FL val="Product Active">true</FL>';
                  $xml.='<FL val="Unit Price">'.$data['price'].'</FL>';
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
                  $xml.='<FL val="Description">'.urlencode($data['Description']).'</FL>';
                  $xml.='<FL val="Taxable">true</FL>';
                  $xml.='</row></Products>';


                  $url = "https://crm.zoho.com/crm/private/json/Products/insertRecords?";    
                  $query = "authtoken=".$this->apiauthtoken."&scope=crmapi&newFormat=1&wfTrigger=true&xmlData=".$xml; 

                  $resp = $this->curlRequest($url,$query); 


                  $resultcontact =json_decode($resp,true);

                return @$resultcontact['response']['result']['recorddetail']['FL'][0]['content'];
 
              }
}

