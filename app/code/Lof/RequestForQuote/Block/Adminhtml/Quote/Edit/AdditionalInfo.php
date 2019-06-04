<?php

namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Edit;

class AdditionalInfo extends \Magento\Backend\Block\Template
{

    protected $_coreRegistry;

    protected $moduleHelper;

    protected $_wysiwygConfig;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Lof\RequestForQuote\Helper\Data $moduleHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->moduleHelper = $moduleHelper;
        $this->_wysiwygConfig = $wysiwygConfig;
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getMageQuote()
    {
        return $this->_coreRegistry->registry('mage_quote');
    }

    /**
     * @return \Lof\RequestForQuote\Helper\Data
     */
    public function getModuleHelper()
    {
        return $this->moduleHelper;
    }

    /**
     * @param $html
     * @return null|string|string[]
     */
    public function stripScriptTags($html)
    {
        return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
    }

    /**
     * @return mixed
     */
    public function getQuote()
    {
        return $this->getParentBlock()->getQuote();
    }

    public function getWysiwygConfig(){
        return $this->_wysiwygConfig;
    }

}