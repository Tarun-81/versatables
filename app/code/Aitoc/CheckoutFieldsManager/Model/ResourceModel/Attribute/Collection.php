<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute;

class Collection extends \Magento\Eav\Model\ResourceModel\Attribute\Collection
{
    /**
     * Default attribute entity type code
     *
     * @var string
     */
    protected $entityTypeCode = 'aitoc_checkout';

    /**
     * Resource model initialization
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init(
            'Aitoc\CheckoutFieldsManager\Model\Entity\Attribute',
            'Aitoc\CheckoutFieldsManager\Model\ResourceModel\Entity\Attribute'
        );
    }

    /**
     * Default attribute entity type code
     *
     * {@inheritdoc}
     */
    protected function _getEntityTypeCode()
    {
        return $this->entityTypeCode;
    }

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * {@inheritdoc}
     */
    protected function _getEavWebsiteTable()
    {
        return null;
    }

    /**
     * Initialize select object
     *
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $entityType = $this->getEntityType();
        $additionalTable = $entityType->getAdditionalAttributeTable();
        $columns = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        unset($columns['attribute_id']);

        $this->getSelect()->from(
            ['main_table' => $this->getResource()->getMainTable()],
            array_keys($columns)
        )->join(
            ['additional_table' => $this->getTable($additionalTable)],
            'additional_table.attribute_id = main_table.attribute_id'
        )->where(
            'main_table.entity_type_id = ?',
            (int)$entityType->getId()
        );

        return $this;
    }

    public function addEditableFilter()
    {
        return $this->addFieldToFilter('additional_table.is_visible', 1)
            ->addFieldToFilter('main_table.frontend_input', ['neq' => 'label']);
    }
}
