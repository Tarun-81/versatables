<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Model\ResourceModel;

/**
 * Quote resource model
 * @package Cart2Quote\Quotation\Model\ResourceModel
 */
class Quote extends \Magento\Quote\Model\ResourceModel\Quote
{
    /**
     * Use is object new method for save of object
     * @var bool
     */
    protected $_useIsObjectNew = true;

    /**
     * Primary key auto increment flag
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        //quote_id refers to the quotation_quote whereas the entity_id is the magento quote
        if ($object instanceof \Cart2Quote\Quotation\Model\Quote && $object->getQuoteId()) {
            return parent::save($object);
        }

        return $object->getResource()->save($object);
    }

    /**
     * Initialize table and PK name
     * @return void
     */
    protected function _construct()
    {
        $this->_init('quotation_quote', 'quote_id');
    }

    /**
     * Retrieve select object for load object data
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $mainTable = $this->getMainTable();
        $field = $this->getConnection()->quoteIdentifier(sprintf('%s.%s', $mainTable, $field));
        $select = $this->getConnection()->select()
            ->from($mainTable)
            ->joinLeft(
                ['q' => $this->getTable('quote')],
                '`q`.entity_id = ' . $mainTable . '.quote_id'
            )
            ->where($field . '=?', $value);

        return $select;
    }

    /**
     * Perform actions before object save
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\Object $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object instanceof \Cart2Quote\Quotation\Model\EntityInterface && $object->getIncrementId() == null) {
            $newIncrementId = $this->sequenceManager->getSequence(
                $object->getEntityType(),
                $object->getStore()->getGroup()->getDefaultStoreId()
            )->getNextValue();

            $object->setIncrementId($newIncrementId);
        }
        parent::_beforeSave($object);

        return $this;
    }
}
