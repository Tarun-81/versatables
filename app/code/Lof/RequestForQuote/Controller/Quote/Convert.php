<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuote\Controller\Quote;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as   QuoteFactory;

class Convert extends \Lof\RequestForQuote\Controller\AbstractIndex
{
	protected $formKey;   
protected $cart;
protected $product;
protected $_coreRegistry;

public function __construct(
\Magento\Framework\App\Action\Context $context,
\Magento\Framework\Registry $registry,
\Magento\Framework\Data\Form\FormKey $formKey,
\Magento\Checkout\Model\Cart $cart,
\Magento\Catalog\Model\Product $product,
\Magento\Quote\Model\QuoteFactory $mageQuoteFactory,
\Lof\RequestForQuote\Model\QuoteFactory $quoteFactory,
\Magento\Quote\Model\QuoteRepository $quoteRepository,
array $data = []) {
    $this->formKey = $formKey;
    $this->_coreRegistry = $registry;
    $this->cart = $cart;
    $this->product = $product;
    $this->mageQuoteFactory = $mageQuoteFactory;
    $this->quoteFactory = $quoteFactory;
    $this->quoteRepository = $quoteRepository;    
    parent::__construct($context);
}


    public function execute()
    {
    	$mageQuote = $quote = null;
        if ($quoteId = $this->getRequest()->getParam('quote_id')) {
            $quote = $this->quoteFactory->create()->load($quoteId);

			$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
			$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
			$base_url = $storeManager->getStore()->getBaseUrl();
			//echo $base_url;die;
			//$quote_cart = $objectManager->get('\Magento\Checkout\Model\Cart');
            $this->_coreRegistry->register('current_rfq_quote', $quote);
            $mageQuote = $this->mageQuoteFactory->create()->load($quote->getQuoteId());
            $this->_coreRegistry->register('current_quote', $mageQuote);
            foreach ($mageQuote->getAllItems() as $item) {
                    $pid = $item->getProductId();
                    $_product = $objectManager->get('\Magento\Catalog\Model\Product')->load($pid);
                   // $params = $pid;
                    //$params['product'] = $pid;
                    $optionss = $item->getBuyRequest()->getData('options');
                    //$params['options'] = $optionss;
					/*$params = array(
						'form_key' => $this->getRequest()->getParam('form_key'),
						'product' => $pid,
						'options' => $optionss,
						'qty' => 1,
						'type' => 'simple'
					);*/
					//print_r($optionss);die;
					$option_a = '';
					foreach($optionss as $optionss => $val){
						$option_a .= '&options['.$optionss.']='.$val;
					}
					//echo $option_a;die;
					$url = $base_url.'checkout/cart/add?product='.$pid.'&qty=1'.$option_a;
					echo $url;die;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_exec($ch);
					curl_close($ch);
					
                   // echo $_product->getName()."<br>";
                    //$quote_cart->addProduct($_product, $params);
            }
            //$quote_cart->save();
        }
        //print_r($optionss);die;
	}
}