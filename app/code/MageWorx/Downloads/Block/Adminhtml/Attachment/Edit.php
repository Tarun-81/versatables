<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Attachment;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends FormContainer
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
    
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'attachment_id';
        $this->_blockGroup = 'MageWorx_Downloads';
        $this->_controller = 'adminhtml_attachment';
        parent::_construct();
    }

    /**
     * Retrieve text for header element depending on loaded attachment
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \MageWorx\Downloads\Model\Attachment $attachment */
        $attachment = $this->coreRegistry->registry('mageworx_downloads_attachment');
        if ($attachment && $attachment->getId()) {
            return __("Edit Attachment '%1'", $this->escapeHtml($attachment->getTitle()));
        }
        return __('New Attachment');
    }
}
