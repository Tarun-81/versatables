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

namespace Lof\RequestForQuotePdf\Block\Adminhtml\Quote;

use Magento\Customer\Model\Context;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class Pdf extends \Magento\Framework\View\Element\Template
{
	public function __construct(\Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context);

    }
    public function getQuote() {
        return $this->getParentBlock()->getQuote();
    }
	public function getPreviewPdfUrl(){
		$quote_id = 0;
		if($quote = $this->getQuote()){
			$quote_id = $quote->getId();
		}
		
        return $this->getUrl("quotationpdf/quote/pdf", ["quote_id"=>$quote_id]);
    }
}