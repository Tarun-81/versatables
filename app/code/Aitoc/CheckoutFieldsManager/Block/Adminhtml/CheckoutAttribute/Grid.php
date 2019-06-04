<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

/**
 * Product attributes grid
 */

namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\CheckoutAttribute;

use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends AbstractGrid
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->collectionFactory = $collectionFactory;
        $this->_module = 'aitoccheckoutfieldsmanager';
        $this->_addButtonLabel = __('Add Attribute');
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        $this->setCollection($collection);
        $this->addColumn(
            'is_visible',
            [
                'header' => __('Visible on checkout page'),
                'sortable' => true,
                'index' => 'is_visible',
                'header_css_class' => 'col-system',
                'type' => 'options',
                'options' => [
                    '1' => __('Yes'),
                    '0' => __('No'),
                ],
                'column_css_class' => 'col-system'
            ]
        );
        $this->addColumn(
            'display_area',
            [
                'header' => __('Display Area'),
                'sortable' => true,
                'index' => 'display_area',
                'header_css_class' => 'col-system',
                'column_css_class' => 'col-system'
            ]
        );

        $this->removeColumn('is_user_defined');

        return parent::_prepareCollection();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('attribute_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setHideFormElement(true);

        $this->getMassactionBlock()->addItem(
            'show',
            [
                'label' => __('Show'),
                'url' => $this->getUrl('aitoccheckoutfieldsmanager/*/massShow', ['_current' => true]),
                'confirm' => __('Are you sure you want to show the selected attributes(s)?')
            ]
        );
        $this->getMassactionBlock()->addItem(
            'hide',
            [
                'label' => __('Hide'),
                'url' => $this->getUrl('aitoccheckoutfieldsmanager/*/massHide', ['_current' => true]),
                'confirm' => __('Are you sure you want to hide the selected attributes(s)?')
            ]
        );

        return $this;
    }
}
