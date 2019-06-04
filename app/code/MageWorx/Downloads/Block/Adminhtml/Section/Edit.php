<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Section;

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
        $this->_objectId = 'section_id';
        $this->_blockGroup = 'MageWorx_Downloads';
        $this->_controller = 'adminhtml_section';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Section'));
        $this->buttonList->update('delete', 'label', __('Delete Section'));
    }

    /**
     * Retrieve text for header element depending on loaded section
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \MageWorx\Downloads\Model\Section $section */
        $section = $this->coreRegistry->registry('mageworx_downloads_section');
        if ($section && $section->getId()) {
            return __("Edit Section '%1'", $this->escapeHtml($section->getTitle()));
        }
        return __('New Section');
    }
}
