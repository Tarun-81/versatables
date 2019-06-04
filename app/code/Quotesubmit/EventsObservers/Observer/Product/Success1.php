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
        $url = "https://crm.zoho.com/crm/private/json/Quotes/insertRecords?";   
        $query = "authtoken=".$this->apiauthtoken."&scope=crmapi&newFormat=1&wfTrigger=true&xmlData=";
        $Quote_ids = $observer->getEvent()->getquote_ids()[0];

	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
	$connection = $resource->getConnection();
	$tableName = $resource->getTableName('lof_rfq_quote'); 
	$sql = $connection->select()->from($tableName)->where('entity_id = ?', $Quote_ids); 
        $result = $connection->fetchRow($sql);
		
		/*echo "<PRE>";
		print_r($result);
		*/
	$tableName = $resource->getTableName('quote_item'); 
	$sql = $connection->select()->from($tableName)->where('quote_id = ?', $result["quote_id"]); 
        $resultitems = $connection->fetchAll($sql);
		
		/*echo "quote_item<br/><PRE>";
		print_r($resultitems);
		*/
	$tableName = $resource->getTableName('quote'); 
	$sql = $connection->select()->from($tableName)->where('entity_id = ?', $result["quote_id"]); 
        $orignalquote = $connection->fetchRow($sql);
		/*echo "quote_item<br/><PRE>";
		print_r($orignalquote);
	*/
		$customeremail=@$orignalquote['customer_email'];
		$subtotal=@$orignalquote['subtotal'];
		$grand_total=@$orignalquote['grand_total'];
		$ip=@$orignalquote['remote_ip'];
		$quotegeneratedid=@$result['increment_id'];
		$expiry=@$result['expiry'];
		$postcode=@$orignalquote['postcode'];
		$city=@$orignalquote['city'];
		$company=@$result['company'];
		$telephone=@$orignalquote['telephone'];
		$customer_note=@$orignalquote['customer_note'];
		$TermsConditions = "All products are designed and assembled in the USA Price quote is good for 90 days from creation date Lifetime Warrenty on all products Payment Terms : Wire Transfer , Credit Card, Check , Net 30";
		$address=@$orignalquote['address'];
		$city=@$orignalquote['address'];
		$company=@$result['company'];
		$telephone=@$orignalquote['telephone'];
		
		$dateget = date("Y-m-d");
		$expiredate =  date('Y-m-d', strtotime($dateget. ' + 90 days'))."<br>";
		//echo $expiredate;
		
                  $xml='<Quotes><row no="1">';
                  $xml.='<FL val="Subject">Christian Voelkers - Quote</FL>';
                  $xml.='<FL val="Potential Name">Furniture System COmpany-</FL>';
                  $xml.='<FL val="Sub Total">'.$subtotal.'</FL>';
				  $xml.='<FL val="Description">'.$customer_note.'</FL>';
				  $xml.='<FL val="Terms and Conditions">'.$TermsConditions.'</FL>';
                  $xml.='<FL val="Grand Total">'.$grand_total.'</FL>';
                  $xml.='<FL val="Billing Street">'.@$result["street"].'</FL>';
                  $xml.='<FL val="Shipping Street">'.@$result["street"].'</FL>';
                  $xml.='<FL val="Billing City">'.@$result["city"].'</FL>';
                  $xml.='<FL val="Shipping City">'.@$result["city"].'</FL>';
                  $xml.='<FL val="Billing State">'.@$result["region"].'</FL>';
                  $xml.='<FL val="Shipping State">'.@$result["region"].'</FL>';
                  $xml.='<FL val="Discount">00</FL>';
                  $xml.='<FL val="VSD Quote Number">'.$quotegeneratedid.'</FL>';
                  $xml.='<FL val="Created At">'.Date('Y-m-d').'</FL>';
                  $xml.='<FL val="Email">'.@$result["email"].'</FL>';
                  $xml.='<FL val="Name">'.@$result["first_name"].' '.@$result["last_name"].'</FL>';
                  //$xml.='<FL val="Industry">Advertising</FL>';
                  $xml.='<FL val="SOURCE">VersaTables Website Quote</FL>';
                  $xml.='<FL val="Quote Expiration Date">'.$expiredate.'</FL>';
                  $xml.='<FL val="Date">'.date('Y-m-d').'</FL>';
                  $xml.=' <FL val="Product Details">'; 


		  $counter=1;

                  foreach ($resultitems as $item)
                        {	
						     
						
                          $items[$counter]['name']=@$item['name'];
                         
                        // echo "------------------------------";
                       //   print_R($item->getData());
                      //   echo "------------------------------";
                          $options='';
						  
                          $items[$counter]['ordernumber']=$quotegeneratedid;             
                          $items[$counter]['price']=@$item['price'];
						 // $itemoptionsval = $this->Getoptionvalues($connection,$resource,@$item['item_id']);
                         
                          $items[$counter]['sku']=@$item['sku'];
                          $items[$counter]['quantity'] = @$item['qty'];
                          $product=$this->getLoadProduct(@$item['product_id']);


                          $productdata=array();
                          $productdata['id']=$product->getData('entity_id');
                          $productdata['name']=$product->getData('name');
                          $productdata['option']=$options;
                          $productdata['code']=$product->getData('sku');
                          $productdata['price']=$product->getData('price');
                          $productdata['Description']=strip_tags($product->getData('description'));
                          $items[$counter]['description']=strip_tags($product->getData('description'));
                          $productdata['created_at']=$product->getData('created_at');
                          $productdata['updated_at']=$product->getData('updated_at');

                          $productdata['quantity_in_stock']=$product->getData('quantity_and_stock_status/qty');
                         // print_r($productdata);
                          $serachitemid =  $this->productAdd($productdata);
                          if(!empty($serachitemid))
                            {
                                 $xml.=$this->addProduct($item,$serachitemid,$counter);
                            }
				
                          $counter++;
                      }
					  echo "<br/>";
					  //print_r($options);

			$xml.='</FL>';
                          $xml.='<FL val="Company">'.$company.'</FL>';
                          $xml.='<FL val="Telephone">'.$telephone.'</FL>';
                          $xml.='<FL val="QuotesID">'.$quotegeneratedid.'</FL>';
                          $xml.='</row></Quotes>';   
                          $query.=$xml;
                 //echo $url.$query;
                  $result=$this->curlRequest($url,$query); 
				  //file_put_contents("resp.txt",print_r($result));
				  
              
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
                     $productid=@$item['product_id'];
                     $quantity=@$item['qty'];
                     $price=@$item['price'];
                     return '<product no="'.$counter.'"><FL val="Product Id">'.$serachitemid .'</FL><FL val="Quantity">'.$quantity.'</FL><FL val="List Price">'.$price.'</FL><FL val="Unit Price">'.$price.'</FL></product>'; 
                     }
            public  function searchBy($sku,$itemname)
             {
              $serchUrl  = "https://crm.zoho.com/crm/private/json/Products/searchRecords?";
             
              $serchquery = "authtoken=03d95794d824e8c2c65a2a2b6cfedc65&scope=crmapi&criteria=((Product Code:".$sku.")AND(Product Name:".$itemname."))";
              $serachResult = $this->curlRequest($serchUrl,$serchquery);
              return json_decode($serachResult,true);
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
               public function productAdd($data)
              { 
                
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
                $xml.='<FL val="Description">'.$data['Description'].'</FL>';
                $xml.='<FL val="Taxable">true</FL>';
                $xml.='</row></Products>';
                $url = "https://crm.zoho.com/crm/private/json/Products/insertRecords?";    
                $query = "authtoken=".$this->apiauthtoken."&scope=crmapi&newFormat=1&wfTrigger=true&xmlData=".$xml; 

                $resp = $this->curlRequest($url,$query); 


                $resultcontact =json_decode($resp,true);

                return @$resultcontact['response']['result']['recorddetail']['FL'][0]['content'];
 
              }
}

