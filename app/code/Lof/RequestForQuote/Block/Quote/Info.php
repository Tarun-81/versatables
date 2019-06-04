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

namespace Lof\RequestForQuote\Block\Quote;

use Magento\Sales\Model\Order\Address;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Customer\Model\Context;

class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;


    public function __construct(
        TemplateContext $context,
        Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->_customerCollection = $customerCollection;
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('current_quote');
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getRfqQuote()
    {
        return $this->coreRegistry->registry('current_rfq_quote');
    }

    /**
     * Get url for printing order
     *
     * @param \Lof\RequestForQuote\Model\Quote $quote
     * @return string
     */
    public function getPrintUrl(\Lof\RequestForQuote\Model\Quote $quote)
    {
        return $this->getUrl('quotation/quote/print', ['quote_id' => $quote->getId()]);
    }
    /**
     * Get url for printing order
     *
     * @param \Lof\RequestForQuote\Model\Quote $quote
     * @return string
     */
    public function getPrintUrl2(\Lof\RequestForQuote\Model\Quote $quote)
    {
        return $this->getUrl('quotation/quote/print', ['quote_id' => $quote->getIncrementId()]);
    }
    /**
     * Get url for printing order
     *
     * @param \Lof\RequestForQuote\Model\Quote $quote
     * @return string
     */
    public function getSignInUrl(\Lof\RequestForQuote\Model\Quote $quote)
    {
        return $this->getUrl('quotation/quote/signin', ['quote_id' => $quote->getIncrementId()]);
    }
    public function checkExistsCustomer($email_address) {
        $checked = true;
        if($email_address) {
            $collection = $this->_customerCollection->create();
            $collection->addFieldToFilter("email", $email_address);
            if($collection->getSize()){
                $checked = true;
            } else {
                $checked = false;
            }
        }
        return $checked;
    }
}
