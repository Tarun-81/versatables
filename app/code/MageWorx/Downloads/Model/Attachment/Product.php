<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model\Attachment;

use MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory;

class Product
{
    /**
     * @var \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @param CollectionFactory $attachmentCollectionFactory
     */
    public function __construct(CollectionFactory $attachmentCollectionFactory)
    {
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
    }

    /**
     * @param int $productId
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection
     */
    public function getSelectedAttachmentsCollection($productId)
    {
        return $this->attachmentCollectionFactory->create()->addProductFilter($productId);
    }

    /**
     * Retrieve attachments with reset product count
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getSelectedAttachments(\Magento\Catalog\Model\Product $product)
    {
        if (!$product->hasSelectedAttachments()) {
            $attachments = [];
            $collection = $this->getSelectedAttachmentsCollection($product->getId());
            
            foreach ($collection as $attachment) {
                $attachments[] = $attachment;
            }
            $product->setSelectedAttachments($attachments);
        }
        return $product->getData('selected_attachments');
    }
}
