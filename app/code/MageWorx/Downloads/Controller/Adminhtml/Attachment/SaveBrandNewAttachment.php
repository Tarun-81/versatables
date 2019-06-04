<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use MageWorx\Downloads\Controller\Adminhtml\Attachment as AttachmentController;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Model\Attachment\Source\ContentType;

/**
 * Class SaveBrandNewAttachment
 * @package MageWorx\Downloads\Controller\Adminhtml\Attachment
 */
class SaveBrandNewAttachment extends Save
{
    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function getTotalAttachementsQuantity($data)
    {
        $result = 0;   // Worst-case scenario.
        if (isset($data['content_type'])) {
            if ($data['content_type'] == ContentType::CONTENT_URL) {
                $result = 1;
            } elseif ($data['content_type'] == ContentType::CONTENT_FILE) {
                if (isset($data['multifile'])) {
                    $result = count($data['multifile']);
                }
            }
        }
        return $result;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('attachment');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->filterData($data);

            $totalAttachmentsQuantity = $this->getTotalAttachementsQuantity($data);
            $successfullySavedAttachementsQuantity = 0;

            $fileIndex = -1;
            while (++$fileIndex < $totalAttachmentsQuantity) {
                if ($fileIndex > 0) {
                    $this->coreRegistry->unregister('mageworx_downloads_attachment');
                }
                $attachment = $this->initAttachment();
                $attachment->addData($data);

                if ($attachment->getContentType() == ContentType::CONTENT_FILE) {
                    $file = $data['multifile'][$fileIndex];
                    $attachmentNickname = $data['realname'][$fileIndex];

                    if ($file) {
                        $attachment->setFilename($file);
                        $attachment->setType(substr($file, strrpos($file, '.') + 1));
                        $attachment->setUrl('');
                        $attachment->setSize(filesize($this->fileLinkModel->getBaseDir() . $file));
                    }
                } elseif ($attachment->getContentType() == ContentType::CONTENT_URL) {
                    $attachment->setFilename('');
                    $attachment->setType('');
                    $attachmentNickname = $attachment->getUrl();
                }

                if (!$attachment->getName()) {
                    $attachment->setName($attachmentNickname);
                }

                $productIds = $this->getProductIds($data);
                $attachment->setProductsData($productIds);

                $this->_eventManager->dispatch(
                    'mageworx_downloads_attachment_prepare_save',
                    [
                        'attachment' => $attachment,
                        'request' => $this->getRequest()
                    ]
                );
                try {
                    $attachment->getResource()->save($attachment);
                    $successfullySavedAttachementsQuantity++;
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the attachment %1.', $attachmentNickname));
                }

            }

            $this->_getSession()->setMageWorxDownloadsAttachmentData(false);

            $errorOccured = (0 == $totalAttachmentsQuantity || $totalAttachmentsQuantity != $successfullySavedAttachementsQuantity);

            if ($errorOccured) {

                $isRedirectToTheCreationPage = true;

                if (0 != $successfullySavedAttachementsQuantity) {
                    $this->messageManager->addWarningMessage(__('Only %1 attachment(s) out of %2 have been saved.', $successfullySavedAttachementsQuantity, $totalAttachmentsQuantity));
                    $isRedirectToTheCreationPage = false; // At this point better not to go back to the edit page. User must see which files were uploaded, and which were not.
                } else {
                    $this->_getSession()->setMageWorxDownloadsAttachmentData($data);

                    if (0 == $totalAttachmentsQuantity) {
                        $this->messageManager->addErrorMessage(__('At least one file must be attached.'));
                    }
                }
            } else {
                if (1 == $successfullySavedAttachementsQuantity) {
                    $this->messageManager->addSuccessMessage(__('The attachment has been saved.'));
                } else {
                    $this->messageManager->addSuccessMessage(__('%1 attachments have been saved.', $successfullySavedAttachementsQuantity));
                }
            }

            if (($errorOccured && $isRedirectToTheCreationPage) || (!$errorOccured && $this->getRequest()->getParam('back'))) {
                $resultRedirect->setPath(
                    'mageworx_downloads/*/create',
                    [
                        '_current' => true
                    ]
                );
            } else {
                $resultRedirect->setPath('mageworx_downloads/*/');
            }
        }

        return $resultRedirect;
    }
}
