<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Observer\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Model\Attachment\Source\ContentType;
use MageWorx\Downloads\Model\AttachmentFactory;

class SaveNewProductAttachments implements ObserverInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * Attachment factory
     *
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var bool
     */
    protected $hasRequiredData = false;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * SaveNewProductAttachments constructor.
     * @param Context $context
     * @param AttachmentFactory $attachmentFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        AttachmentFactory $attachmentFactory,
        Registry $coreRegistry
    ) {
        $this->context           = $context;
        $this->messageManager    = $context->getMessageManager();
        $this->attachmentFactory = $attachmentFactory;
        $this->coreRegistry      = $coreRegistry;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $product   = $this->coreRegistry->registry('product');
        $productId = $product->getId();
        $data      = $this->context->getRequest()->getPostValue('new_attachments', -1);

        $this->checkRequiredData($data);

        if ($this->hasRequiredData && $productId) {
            $data = $this->prepareData($data);

            $totalAttachmentsQuantity = $this->getTotalAttachementsQuantity($data);

            $fileIndex = -1;

            while (++$fileIndex < $totalAttachmentsQuantity) {

                $attachment = $this->attachmentFactory->create();

                $attachment->addData($data);

                if ($attachment->getContentType() == ContentType::CONTENT_FILE) {

                    $file               = $data['multifile'][$fileIndex]['file'];
                    $attachmentNickname = $data['multifile'][$fileIndex]['name'];
                    $size               = $data['multifile'][$fileIndex]['size'];

                    $attachment->setFilename($file);
                    $attachment->setType(substr($file, strrpos($file, '.') + 1));
                    $attachment->setUrl('');
                    $attachment->setSize($size);

                } elseif ($attachment->getContentType() == ContentType::CONTENT_URL) {

                    $attachmentNickname = $attachment->getUrl();

                    $attachment->setFilename('');
                    $attachment->setType('');
                }

                if (!$attachment->getName()) {
                    $attachment->setName($attachmentNickname);
                }

                $attachment->setProductsData([$productId]);

                try {
                    $attachment->getResource()->save($attachment);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage(
                        $e,
                        __('Something went wrong while saving the attachment %1.', $attachmentNickname)
                    );
                }
            }
        }

        return $this;
    }

    /**
     * @param $data
     * @return int
     */
    protected function getTotalAttachementsQuantity($data)
    {
        if ($data['content_type'] == ContentType::CONTENT_URL) {
            return 1;
        }
        return count($data['multifile']);
    }

    /**
     * Prepares specific data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        if (array_search(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $data['stores']) !== false) {
            $data['stores'] = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }

        return $data;
    }

    /**
     * @param $data
     */
    protected function checkRequiredData($data)
    {
        if ($data != -1
            && isset($data['customer_group_ids'])
            && isset($data['stores'])
            && (isset($data['multifile']) || $data['url'] !== '')
        ) {
            $this->hasRequiredData = true;
        } else {
            $this->hasRequiredData = false;
        }
    }
}
