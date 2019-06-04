<?php
namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Create;

Class InitEditor extends \Magento\Framework\View\Element\Template {

    protected $_wysiwygConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_wysiwygConfig = $wysiwygConfig;
    }

    public function getWysiwygConfig(){
        return $this->_wysiwygConfig;
    }
}