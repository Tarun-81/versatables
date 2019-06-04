<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Observer\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\Downloads\Model\ResourceModel\Attachment;
use Magento\Framework\Registry;
use Magento\Framework\App\ProductMetadataInterface;

class SaveProductAttachments implements ObserverInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var JsHelper
     */
    protected $jsHelper;

    /**
     * @var Attachment
     */
    protected $attachmentResource;

    /**
     * SaveProductAttachments constructor.
     * @param Context $context
     * @param JsHelper $jsHelper
     * @param Registry $coreRegistry
     * @param Attachment $attachmentResource
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Context $context,
        JsHelper $jsHelper,
        Registry $coreRegistry,
        Attachment $attachmentResource,
        ProductMetadataInterface $productMetadata
    ) {
        $this->context            = $context;
        $this->jsHelper           = $jsHelper;
        $this->coreRegistry       = $coreRegistry;
        $this->attachmentResource = $attachmentResource;
        $this->productMetadata    = $productMetadata;
    }


    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $post = $this->context->getRequest()->getPostValue('attachments', -1);
        $product = $this->coreRegistry->registry('product');

        if (!$product->getId()) {
            return $this;
        }

        if (version_compare($this->productMetadata->getVersion(), '2.1.0', '>=')) {
            $post = !empty($post['attachment']) ? array_column($post['attachment'], 'id') : [];
            $this->attachmentResource->saveAttachmentProductRelation($product, $post);
        } else {
            if ($post != '-1') {
                $post = $this->jsHelper->decodeGridSerializedInput($post);
                $product = $this->coreRegistry->registry('product');
                $this->attachmentResource->saveAttachmentProductRelation($product, $post);
            }
        }

        return $this;
    }
}
