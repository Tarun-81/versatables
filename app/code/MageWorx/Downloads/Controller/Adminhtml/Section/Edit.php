<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use Magento\Backend\App\Action\Context;
use MageWorx\Downloads\Model\SectionFactory;
use Magento\Framework\Registry;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Framework\View\Result\PageFactory;

class Edit extends \MageWorx\Downloads\Controller\Adminhtml\Section
{
    /**
     * Backend session
     *
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param SectionFactory $sectionFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        SectionFactory $sectionFactory,
        Context $context
    ) {
    
        $this->backendSession = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($registry, $sectionFactory, $context);
    }

    /**
     * Is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_Downloads::sections');
    }

    /**
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $sectionId = $this->getRequest()->getParam('section_id');
        /** @var \MageWorx\Downloads\Model\Section $section */
        $section = $this->initSection();

        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('MageWorx_Downloads::sections');
        $resultPage->getConfig()->getTitle()->set((__('Section')));
        if ($sectionId) {
            $section->getResource()->load($section, $sectionId);
            if (!$section->getId()) {
                $this->messageManager->addErrorMessage(__('The section no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'mageworx_downloads/*/edit',
                    [
                        'section_id' => $section->getId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }
        $title = $section->getId() ? $section->getName() : __('New Section');
        $resultPage->getConfig()->getTitle()->append($title);
        $data = $this->backendSession->getData('mageworx_downloads_section_data', true);
        if (!empty($data)) {
            $section->setData($data);
        }
        
        return $resultPage;
    }
}
