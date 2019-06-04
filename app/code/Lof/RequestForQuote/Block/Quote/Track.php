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

use Magento\Customer\Model\Context;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class Track extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Url
     */
    protected $_catalogUrlBuilder;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * \Magento\Directory\Block\Data
     */
    protected $_directoryData;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Directory\Block\Data $directoryData,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        CustomerRepository $customerRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    ) {
        $this->_cartHelper        = $cartHelper;
        $this->_catalogUrlBuilder = $catalogUrlBuilder;
        $this->_coreRegistry      = $coreRegistry;
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->_isScopePrivate    = true;
        $this->httpContext        = $httpContext;
        $this->_directoryData     = $directoryData;
        $this->_configCacheType   = $configCacheType;
        $this->_customerSession   = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->urlHelper          = $urlHelper;
    }
    /**
     * Get active quote
     *
     * @return Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->_coreRegistry->registry('current_quote');
        }
        return $this->_quote;
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->customerRepository->getById($this->_customerSession->getCustomerId());
        } else {
            return null;
        }
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getRfqQuote()
    {
        return $this->_coreRegistry->registry('current_rfq_quote');
    }
    
}
