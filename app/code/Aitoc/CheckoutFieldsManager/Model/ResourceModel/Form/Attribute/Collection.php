<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\ResourceModel\Form\Attribute;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;

class Collection extends \Magento\Eav\Model\ResourceModel\Form\Attribute\Collection
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'Aitoc_CheckoutFieldsManager';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'aitoc_checkout';

    /**
     * Resource initialization
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            'Aitoc\CheckoutFieldsManager\Model\Attribute',
            'Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute'
        );
    }

    /**
     * Get EAV Store table
     *
     * Get table, where store-dependent attribute parameters are stored.
     *
     * @return string
     */
    protected function getEavStoreTable()
    {
        return $this->getTable('aitoc_checkout_eav_attribute_store');
    }

    /**
     * Add Display Area filter to collection
     *
     * @param string $code
     *
     * @return $this
     * @codeCoverageIgnore
     */
    public function addDisplayAreaFilter($code)
    {
        return $this->addFieldToFilter('ca.display_area', $code);
    }

    /**
     * Add Checkout Step filter to collection
     *
     * @param string $code
     *
     * @return $this
     */
    public function addCheckoutStepFilter($code)
    {
        return $this->addFieldToFilter('ca.checkout_step', $code);
    }

    /**
     * Set order by attribute sort order
     *
     * @param string $direction
     * @return $this
     */
    public function setSortOrder($direction = self::SORT_ORDER_ASC)
    {
        $this->setOrder('main_table.is_user_defined', self::SORT_ORDER_ASC);
        return $this->setOrder('ca.sort_order', $direction);
    }

    /**
     * @inheritdoc
     */
    protected function _beforeLoad()
    {
        $select = $this->getSelect();
        $connection = $this->getConnection();
        $entityType = $this->getEntityType();

        $caColumns = [];

        // join additional attribute data table
        $additionalTable = $entityType->getAdditionalAttributeTable();
        $caDescribe = $connection->describeTable($this->getTable($additionalTable));
        unset($caDescribe['attribute_id']);
        foreach (array_keys($caDescribe) as $columnName) {
            $caColumns[$columnName] = $columnName;
        }

        $select->join(
            ['ca' => $this->getTable($additionalTable)],
            'main_table.attribute_id = ca.attribute_id AND ca.display_area IS NOT NULL',
            $caColumns
        );

        // add scope values
        $storeId = (int)$this->getStore()->getId();
        $select->joinInner(
            ['sa' => $this->getEavStoreTable()],
            $connection->quoteInto(
                'sa.attribute_id = main_table.attribute_id AND sa.store_id IN (?)',
                [Store::DEFAULT_STORE_ID, $storeId]
            ),
            ''
        );

        $select->group('sa.attribute_id');

        // add store attribute label
        $storeLabelExpr = $connection->getCheckSql('al.value IS NULL', 'main_table.frontend_label', 'al.value');
        $joinExpression = $connection->quoteInto(
            'al.attribute_id = main_table.attribute_id AND al.store_id = ?',
            $storeId
        );
        $select->joinLeft(
            ['al' => $this->getTable('eav_attribute_label')],
            $joinExpression,
            ['store_label' => $storeLabelExpr]
        );

        $select->where('ca.display_area IS NOT NULL');

        return AbstractCollection::_beforeLoad();
    }
}
