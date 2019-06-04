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

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid;

/**
 * Flat quotation quote grid collection
 */
class Collection extends \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
{
    /**
     * Event prefix
     * @var string
     */
    protected $_eventPrefix = 'quotation_quote_grid_collection';

    /**
     * Event object
     * @var string
     */
    protected $_eventObject = 'quote_grid_collection';

    /**
     * Customer mode flag
     * @var bool
     */
    protected $_customerModeFlag = false;

    /**
     * Get SQL for get record count
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        if ($this->getIsCustomerMode()) {
            $this->_renderFilters();

            $unionSelect = clone $this->getSelect();

            $unionSelect->reset(\Zend_Db_Select::ORDER);
            $unionSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
            $unionSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);

            $countSelect = clone $this->getSelect();
            $countSelect->reset();
            $countSelect->from(['a' => $unionSelect], 'COUNT(*)');
        } else {
            $countSelect = parent::getSelectCountSql();
        }

        return $countSelect;
    }

    /**
     * Get customer mode flag value
     * @return bool
     */
    public function getIsCustomerMode()
    {
        return $this->_customerModeFlag;
    }

    /**
     * Set customer mode flag value
     * @param bool $value
     * @return $this
     */
    public function setIsCustomerMode($value)
    {
        $this->_customerModeFlag = (bool)$value;
        return $this;
    }

    /**
     * Model initialization
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('quotation_quote');
    }

    /**
     * Init collection select
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);
        $this->getSelect()->joinLeft(
            ['q' => $this->getTable('quote')],
            'q.entity_id=main_table.quote_id',
            $cols = '*',
            $schema = null
        );
        return $this;
    }
}
