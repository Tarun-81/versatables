<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Catalog\Product;

use Magento\Framework\View\Element\Template;
use MageWorx\Downloads\Model\Attachment;
use MageWorx\Downloads\Model\Attachment\Product as AttachmentProduct;
use MageWorx\Downloads\Helper\Data as HelperData;

class Attachments extends  \MageWorx\Downloads\Block\AttachmentContainer
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *
     * @var \MageWorx\Downloads\Model\Attachment\Product
     */
    protected $attachmentProduct;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Registry $registry
     * @param HelperData $helperData
     * @param AttachmentProduct $attachmentProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Registry $registry,
        HelperData $helperData,
        AttachmentProduct $attachmentProduct,
        \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
        \MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory,
        \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->attachmentProduct = $attachmentProduct;
        parent::__construct($helperData, $httpContext, $context, $attachmentCollectionFactory, $sectionCollectionFactory, $attachmentFactory, $data);
        $this->setTabTitle();
    }

    /**
     * @param boolean $isUseCustomerGroupFilter
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection
     */
    public function getAttachmentCollection($isUseCustomerGroupFilter = true)
    {
        $collection = $this->attachmentProduct->getSelectedAttachmentsCollection($this->getProductId());
        $collection->addFieldToFilter('is_active', Attachment::STATUS_ENABLED);
        $collection->addFieldToFilter('section_table.is_active', \MageWorx\Downloads\Model\Section::STATUS_ENABLED);
        $collection->addStoreFilter($this->_storeManager->getStore()->getId());
        if ($isUseCustomerGroupFilter) {
            $collection->addCustomerGroupFilter($this->getCustomerGroupId());
        }
        $collection->getSelect()->order('name');
        $collection->load();
        $this->attachmentCollection = $collection;
        return $this->attachmentCollection;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->helperData->getProductDownloadsTitle();
    }

    /**
     * Retrieve array of attachment object that allow for view
     *
     * @return array
     */
    public function getAttachments()
    {
        if (!$this->attachments) {
            if ($this->helperData->isHideFiles()) {
                $collection = $this->getAttachmentCollection(true);
                $inGroupIds = $collection->getAllIds();
            } else {
                $collection = $this->getAttachmentCollection(false);
                $inGroupIds = $this->getAttachmentCollection(true)->getAllIds();
            }

            foreach ($collection->getItems() as $item) {
                if (!$this->isAllowByCount($item)) {
                    continue;
                }

                if ($this->isAllowByCustomerGroup($item, $inGroupIds)) {
                    $item->setIsInGroup('1');
                } else {
                    $this->isHasNotAllowedLinks = true;
                }

                $this->attachments[] = $item;
            }
        }

        return $this->attachments;
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->coreRegistry->registry('product');
        return $product ? $product->getId() : null;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $template = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template');
        $template->setFragment('catalog.product.list.mageworx.downloads.attachments');
        return parent::_prepareLayout();
    }

    /**
     * @return $this
     */
    public function setTabTitle()
    {
        $tabTitle = $this->helperData->getProductDownloadsTabTitle();
        $title = __($tabTitle) . '&nbsp;' . '<span class="counter">' .
            count($this->getAttachments()) . '</span>';
        $this->setTitle($title);
        return $this;
    }
}