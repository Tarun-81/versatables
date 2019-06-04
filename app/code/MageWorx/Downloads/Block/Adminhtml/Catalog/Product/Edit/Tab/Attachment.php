<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use MageWorx\Downloads\Model\Section\Source\IsActive as IsActiveOptions;
use MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory as AttachmentCollectionFactory;
use MageWorx\Downloads\Model\Attachment\Product as AttachmentProduct;
use Magento\Catalog\Controller\Adminhtml\Product\Builder as ProductBuilder;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;

/**
 * @method array getProductAttachments()
 * @method Attachment setProductAttachmnets(array $attachments)
 * @method Attachment setUseAjax(boolean $useAjax)
 */
class Attachment extends ExtendedGrid implements TabInterface
{
    /**
     * @var \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @var \MageWorx\Downloads\Model\Attachment\Product
     */
    protected $attachmentProduct;

    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Builder
     */
    protected $productBuilder;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $registry;

    /**
     * @var IsActiveOptions
     */
    protected $isActiveOptions;

    /**
     *
     * @param AttachmentCollectionFactory $attachmentCollectionFactory
     * @param AttachmentProduct $attachmentProduct
     * @param ProductBuilder $productBuilder
     * @param Registry $registry
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        AttachmentCollectionFactory $attachmentCollectionFactory,
        AttachmentProduct $attachmentProduct,
        ProductBuilder $productBuilder,
        Registry $registry,
        IsActiveOptions $isActiveOptions,
        Context $context,
        BackendHelper $backendHelper,
        array $data = []
    ) {

        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->attachmentProduct = $attachmentProduct;
        $this->productBuilder = $productBuilder;
        $this->registry = $registry;
        $this->isActiveOptions = $isActiveOptions;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('attachment_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getProduct()->getId()) {
            $this->setDefaultFilter(['in_attachments' => 1]);
        }
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->attachmentCollectionFactory->create();
        $collection->getSelect()->distinct();

        $storeId = $this->_request->getParam('store');
        if ($storeId) {
            $collection->addStoreFilter($storeId, true);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Disable mass action
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'in_attachments',
            [
                'name'   => 'in_attachments',
                'type'   => 'checkbox',
                'values' => $this->_getSelectedAttachments(),
                'index'  => 'attachment_id',
                'align'  => 'center',
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header'=> __('Name'),
                'align' => 'left',
                'index' => 'name',
            ]
        );

         $this->addColumn(
             'description',
             [
                'header'    => __('Description'),
                'align'     => 'left',
                'index'     => 'description'
             ]
         );

         $this->addColumn(
             'type',
             [
                'header'=> __('Type'),
                'align' => 'left',
                'index' => 'type',
             ]
         );

         $this->addColumn(
             'section_name',
             [
                'header'=> __('Section'),
                'align' => 'left',
                'index' => 'section_name',
             ]
         );

         $this->addColumn(
             'is_active',
             [
                'header'  => __('Status'),
                'index'   => 'is_active',
                'type'    => 'options',
                'options' => $this->isActiveOptions->toArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
             ]
         );

         return parent::_prepareColumns();
    }

    /**
     * Retrieve selected attachments
     *
     * @return array
     */
    protected function _getSelectedAttachments()
    {
        $selected = $this->attachmentProduct->getSelectedAttachments($this->getProduct());
        $data = [];

        foreach ($selected as $model) {
            $data[] = $model->getId();
        }

        return $data;
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedAttachments()
    {
        $selected = $this->_getSelectedAttachments();

        if (!is_array($selected)) {
            $selected = [];
        }
        return $selected;
    }

    /**
     * Get row url
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_urlBuilder->getUrl(
            '*/*/attachmentsGrid',
            [
                'id' => $this->getProduct()->getId(),
                'store' => $this->_request->getParam('store')
            ]
        );
    }

    /**
     * Get current product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (is_null($this->product)) {
            if ($this->registry->registry('current_product')) {
                $this->product = $this->registry->registry('current_product');
            } else {
                $product = $this->productBuilder->build($this->getRequest());
                $this->product = $product;
            }
        }
        return $this->product;
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_attachments') {
            $attachmentIds = $this->_getSelectedAttachments();
            if (empty($attachmentIds)) {
                $attachmentIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('attachment_id', ['in' => $attachmentIds]);
            } else {
                if ($attachmentIds) {
                    $this->getCollection()->addFieldToFilter('attachment_id', ['nin' => $attachmentIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Attachments');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mageworx_downloads/catalog_product/attachments', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
