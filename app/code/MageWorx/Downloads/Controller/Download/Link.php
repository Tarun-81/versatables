<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Download;

use Magento\Framework\App\Action\Context;
use MageWorx\Downloads\Helper\Download as DownloadHelper;
use MageWorx\Downloads\Model\Attachment;
use MageWorx\Downloads\Model\AttachmentFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Downloadable\Helper\File as DownloadableHelperFile;
use Magento\Customer\Model\Session as CustomerSession;
use MageWorx\Downloads\Helper\StoreUrl as HelperStoreUrl;
use MageWorx\Downloads\Model\Attachment\Link as AttachmentLinkModel;

/**
 * Class Link
 * @package MageWorx\Downloads\Controller\Download
 */
class Link extends \MageWorx\Downloads\Controller\Download
{
    /**
     * @var AttachmentFactory
     */
    private $attachmentModelFactory;
    /**
     * @var DownloadableHelperFile
     */
    private $downloadableHelperFile;
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var HelperStoreUrl
     */
    private $helperStoreUrl;
    /**
     * @var AttachmentLinkModel
     */
    private $attachmentLinkModel;

    /**
     * Link constructor.
     * @param AttachmentFactory $attachmentModelFactory
     * @param DownloadableHelperFile $downloadableHelperFile
     * @param CustomerSession $customerSession
     * @param HelperStoreUrl $helperStoreUrl
     * @param AttachmentLinkModel $attachmentLinkModel
     * @param DownloadHelper $downloadHelper
     * @param Context $context
     */
    public function __construct(
        AttachmentFactory $attachmentModelFactory,
        DownloadableHelperFile $downloadableHelperFile,
        CustomerSession $customerSession,
        HelperStoreUrl $helperStoreUrl,
        AttachmentLinkModel $attachmentLinkModel,
        DownloadHelper $downloadHelper,
        Context $context
    ) {
        $this->attachmentModelFactory = $attachmentModelFactory;
        $this->downloadableHelperFile = $downloadableHelperFile;
        $this->customerSession = $customerSession;
        $this->helperStoreUrl = $helperStoreUrl;
        $this->attachmentLinkModel = $attachmentLinkModel;

        parent::__construct($downloadHelper, $context);
    }

    /**
     * Download link action
     *
     * @return void|ResponseInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id', 0);

        $attachment = $this->attachmentModelFactory->create();

        $attachment->getResource()->load($attachment, $id);

        if (!$attachment->getId() || !$attachment->getContentType()) {
            $this->messageManager->addNoticeMessage(__("We can't find the link or file you requested."));
            return $this->_redirect($this->helperStoreUrl->getStoreBaseUrl());
        }

        if ($this->isDownloadsEnable($attachment)) {
            $resource     = '';
            $resourceType = '';
            try {
                if ($attachment->isFileContent()) {
                    $resource = $this->downloadableHelperFile->getFilePath(
                        $this->attachmentLinkModel->getBasePath(),
                        $attachment->getFilename()
                    );
                    $resourceType = DownloadHelper::LINK_TYPE_FILE;
                    $this->_processDownload($resource, $resourceType);
                    $attachment->setDownloads($attachment->getDownloads() + 1);
                    $attachment->getResource()->save($attachment);
                    return;
                } elseif ($attachment->isUrlContent()) {
                    $attachment->setDownloads($attachment->getDownloads() + 1);
                    $attachment->getResource()->save($attachment);

                    if (strpos($attachment->getUrl(), '://') === false) {
                        $url = $this->helperStoreUrl->getUrl(ltrim($attachment->getUrl(), '/'));
                    } else {
                        $url = $attachment->getUrl();
                    }
                    return $this->_redirect($url);
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while getting the requested content.'));
            }
        } elseif ($attachment->getDownloadsLimit() && $attachment->getDownloadsLeft() == 0) {
            $this->messageManager->addNoticeMessage(__('The link is not available.'));
        } else {
            $this->messageManager->addErrorMessage(__('Something went wrong while getting the requested content.'));
        }
        return $this->_redirect($this->helperStoreUrl->getStoreBaseUrl());
    }

    /**
     * Check if downloads enable
     *
     * @param \MageWorx\Downloads\Model\Attachment
     * @return boolean
     */
    protected function isDownloadsEnable($attachment)
    {
        if ($attachment->getIsActive() == Attachment::STATUS_ENABLED
            && (!$attachment->getDownloadsLimit() || $attachment->getDownloadsLeft() > 0)
            && in_array($this->customerSession->getCustomerGroupId(), $attachment->getCustomerGroupIds())
        ) {
            return true;
        }
        return false;
    }
}
