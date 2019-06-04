<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action\Context;
use MageWorx\Downloads\Model\AttachmentFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\Downloads\Controller\Adminhtml\Attachment as AttachmentController;
use Magento\Framework\Registry;
use MageWorx\Downloads\Model\Attachment;

class InlineEdit extends AttachmentController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     *
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param AttachmentFactory $attachmentFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Registry $registry,
        AttachmentFactory $attachmentFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $attachmentFactory, $context);

    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the sent data.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $attachmentId) {
            /** @var \MageWorx\Downloads\Model\Attachment $attachment */
            $attachment = $this->attachmentFactory->create();
            $attachment->getResource()->load($attachment, $attachmentId);
            try {
                $attachmentData = $this->filterData($postItems[$attachmentId]);
                $attachment->addData($attachmentData);

                if ($attachment->getData('url')) {
                    $attachment->clearAttachment();
                }
                $attachment->getResource()->save($attachment);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithAttachmentId($attachment, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithAttachmentId($attachment, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithAttachmentId(
                    $attachment,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add attachment id to error message
     *
     * @param Attachment $attachment
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithAttachmentId(Attachment $attachment, $errorText)
    {
        return '[Attachment ID: ' . $attachment->getId() . '] ' . $errorText;
    }
}
