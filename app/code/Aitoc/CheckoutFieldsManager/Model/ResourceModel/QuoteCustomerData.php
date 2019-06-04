<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\ResourceModel;

use Aitoc\CheckoutFieldsManager\Model\Spi\QuoteCustomerDataResourceInterface;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class QuoteCustomerData extends AbstractDb implements QuoteCustomerDataResourceInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'value_id';

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('aitoc_sales_quote_value', 'value_id');
    }

    /**
     * Update Customer Quote Checkout Data by Quote id
     *
     * @param array $insertData : Data for update
     * @param int $quoteId
     * @param array $attributeIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateQuoteAttributesData($insertData, $quoteId, $attributeIds = null)
    {
        if (!is_array($insertData)) {
            return;
        }
        if (!$attributeIds) {
            $attributeIds = array_unique(array_column($insertData, 'attribute_id'));
        }
        $connection = $this->getConnection();
        $table = $this->getMainTable();
        $select = $connection->select()
            ->from($table)
            ->where('quote_id = ?', $quoteId)
            ->where('attribute_id IN (?)', $attributeIds);
        $savedValues = $connection->fetchAll($select);
        /**
         * searching for matches with data from front and from DB.
         * If found - don't need to insert or delete.
         */
        foreach ($savedValues as $key => $row) {
            foreach ($insertData as $index => $customValue) {
                if ($row['attribute_id'] == $customValue['attribute_id'] && $row['value'] === $customValue['value']) {
                    unset($insertData[$index]);
                    unset($savedValues[$key]);
                    break;
                }
            }
        }
        $toDelete = array_column($savedValues, $this->_idFieldName);

        if (!empty($toDelete)) {
            $connection->delete($table, [$this->_idFieldName . ' IN (?)' => $toDelete]);
        }
        if (!empty($insertData)) {
            $connection->insertMultiple($table, $insertData);
        }
    }
}
