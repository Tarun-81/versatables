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

namespace Lof\RequestForQuote\Block\Account;

class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Url $customerUrl,
        \Lof\RequestForQuote\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_customerUrl = $customerUrl;
        $this->dataHelper      = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        $enable = $this->dataHelper->isEnabledQuote();
        $enable_miniquote = $this->dataHelper->getConfig("general/enable_miniquote");
        $enable_miniquote = ($enable_miniquote!=null)?(int)$enable_miniquote:1;
        if($enable && $enable_miniquote){
            return $this->getUrl('quotation/quote/history/');
        } else {
            return "";
        }
        
    }
    public function _toHtml() {
        $enable = $this->dataHelper->isEnabledQuote();
        $enable_miniquote = $this->dataHelper->getConfig("general/enable_miniquote");
        $enable_miniquote = ($enable_miniquote!=null)?(int)$enable_miniquote:1;
        if($enable && $enable_miniquote){
            return parent::_toHtml();
        }
        return "";
    }
}
