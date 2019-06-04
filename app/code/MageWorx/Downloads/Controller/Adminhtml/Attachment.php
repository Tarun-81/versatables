<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use MageWorx\Downloads\Model\AttachmentFactory;
use Magento\Framework\Registry;
use MageWorx\Downloads\Model\Attachment\Source\ContentType;

abstract class Attachment extends \Magento\Backend\App\Action
{
    /**
     * Attachment factory
     *
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

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
     * @param AttachmentFactory $attachmentFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        AttachmentFactory $attachmentFactory,
        Context $context
    ) {

        $this->coreRegistry = $registry;
        $this->attachmentFactory = $attachmentFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        parent::__construct($context);
    }

    /**
     * @return \MageWorx\Downloads\Model\Attachment
     */
    protected function initAttachment()
    {
        $attachmentId = $this->getRequest()->getParam('attachment_id');
        $attachment   = $this->attachmentFactory->create();
        if ($attachmentId) {
            $attachment->getResource()->load($attachment, $attachmentId);
            $contentType = $attachment->isFileContent() ? ContentType::CONTENT_FILE : ContentType::CONTENT_URL;
            $attachment->setContentType($contentType);
        }
        $this->coreRegistry->register('mageworx_downloads_attachment', $attachment);
        return $attachment;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_Downloads::attachments');
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
