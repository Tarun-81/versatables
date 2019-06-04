<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use MageWorx\Downloads\Model\SectionFactory;
use Magento\Framework\Registry;

abstract class Section extends \Magento\Backend\App\Action
{
    /**
     * Section factory
     *
     * @var SectionFactory
     */
    protected $sectionFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     *
     * @param Registry $registry
     * @param \MageWorx\Downloads\Controller\Adminhtml\SectionFactory $sectionFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        SectionFactory $sectionFactory,
        Context $context
    ) {
    
        $this->coreRegistry = $registry;
        $this->sectionFactory = $sectionFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        parent::__construct($context);
    }

    /**
     * @return \MageWorx\Downloads\Model\Section
     */
    protected function initSection()
    {
        $sectionId = $this->getRequest()->getParam('section_id');
        $section   = $this->sectionFactory->create();
        if ($sectionId) {
            $section->getResource()->load($section, $sectionId);
        }
        $this->coreRegistry->register('mageworx_downloads_section', $section);
        return $section;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_Downloads::sections');
    }

    /**
     *
     * @param array $data
     * @return array
     */
    protected function filterData($data)
    {
        return $data;
    }
}
