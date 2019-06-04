<?php

namespace AppsyscoQuote\QuoteForm\Controller\Index;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class Quote extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_request = $request;
        $this->_transportBuilder = $transportBuilder;
		$this->inlineTranslation = $inlineTranslation;
        $this->_storeManager = $storeManager;
		$this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
	 public $apiauthtoken = "03d95794d824e8c2c65a2a2b6cfedc65";
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
      
        if (!empty($post)) {
			try{

				$this->postdatainzoho($post);
				$postObject = new \Magento\Framework\DataObject();
				$postObject->setData($post);
				$error = false;
				$customerName=$post['first_name'].' '.$post['last_name'];
				$userSubject= 'Get A Quote';
				$fromEmail= $post['email'];
				$this->inlineTranslation->suspend();
				// $templateVars = [
				//			'store' => 1,
				//			'customer_name' => $customerName,
				//			'subject' => $userSubject,
				//			'message'   => $message
				//		];
				//$from = ['email' => $fromEmail, 'name' => $userSubject];
			        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                                $to = $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope);
                                //$to = "checkdeveloper001@gmail.com";
				 $templateOptions = [
				  'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
				  'store' => 1
				];
				$parseDataVars = new \Magento\Framework\DataObject();
				$parseDataVars->setData([
								'first_name' => $post['first_name'],
								'last_name' => $post['last_name'],
								'telephone' => $post['telephone'],
								'email' => $post['email'],
								'industry_name' => $post['industry_name'],
								'hear_about' => $post['hear_about'],
								'business_type' => $post['business_type'],
								'comment' => $post['comment']
							]);
				$transport = $this->_transportBuilder->setTemplateIdentifier("get_quote", $storeScope)
						->setTemplateOptions($templateOptions)
						->setTemplateVars(array('data' => $parseDataVars))
						->setFrom([
							'email' => $this->scopeConfig->getValue('trans_email/ident_general/name', $storeScope),
							'name' => $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope)
						])
						->addTo($to)            
						->getTransport();
				$transport2 = $this->_transportBuilder->setTemplateIdentifier("get_quote_to_customer", $storeScope)
						->setTemplateOptions($templateOptions)
						->setTemplateVars(array('data' => $parseDataVars))
						->setFrom([
							'email' => $this->scopeConfig->getValue('trans_email/ident_general/name', $storeScope),
							'name' => $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope)
						])
						->addTo($fromEmail)            
						->getTransport();
				$transport->sendMessage();
				$transport2->sendMessage();
				$this->inlineTranslation->resume();

				$this->messageManager->addSuccess(__('Form successfully submitted'));

				// $this->_redirect('/get-quote');
				// return;
				
			} catch(\Exception $e){
				$this->inlineTranslation->resume();
				$this->messageManager->addError(__('Sorry! Your Quote are not submitted.'.$e->getMessage())
				);
				//$this->_redirect('/get-quote');
				//return;
			}
			
                        // Redirect to your form page (or anywhere you want...)
             		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            		$resultRedirect->setUrl('/get-quote');
            		return $resultRedirect;
        } else {
			$this->_redirect('*/*/');
			return;
		}
        // 2. GET request : Render the booking page 
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    public function postdatainzoho($post)
    {
    	$url = "https://crm.zoho.com/crm/private/json/Leads/insertRecords?";   
		$query = "authtoken=".$this->apiauthtoken."&scope=crmapi&newFormat=1&wfTrigger=true&xmlData=";
    	
		$xml='<Leads><row no="1">';
		$xml.='<FL val="First Name">'.@$post["first_name"].' </FL>';
		$xml.='<FL val="Last Name">'.@$post["last_name"].' </FL>';
		$xml.='<FL val="Email">'.@$post["email"].' </FL>';
		$xml.='<FL val="Description">'.@$post["comment"].' </FL>';
		$xml.='<FL val="Type">'.@$post["business_type"].' </FL>';
		$xml.='<FL val="Industry">'.@$post["industry_name"].' </FL>';
		$xml.='<FL val="Referrered By">'.@$post["hear_about"].' </FL>';
		$xml.='<FL val="Mobile">'.@$post["telephone"].' </FL>';
		$xml.='</row></Leads>';   
		$query.=$xml;
		$result=$this->curlRequest($url,$query); 
		return $result;
		///echo "<PRE>";
    	//print_r($result);
    	//die;
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
}