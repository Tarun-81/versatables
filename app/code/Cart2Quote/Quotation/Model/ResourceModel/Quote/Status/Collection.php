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

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Status;

/**
 * Flat quotaion quote status history collection
 */
class Collection extends \Cart2Quote\Quotation\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * Get collection data as options array
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('status', 'label');
    }

    /**
     * Get collection data as options hash
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('status', 'label');
    }

    /**
     * Add state code filter to collection
     * @param string $state
     * @return $this
     */
    public function addStateFilter($state)
    {
        $this->joinStates();
        $this->getSelect()->where('state_table.state=?', $state);
        return $this;
    }

    /**
     * Join quote states table
     * @return $this
     */
    public function joinStates()
    {
        if (!$this->getFlag('states_joined')) {
            $this->_idFieldName = 'status_state';
            $this->getSelect()->joinLeft(
                ['state_table' => $this->getTable('quotation_quote_status_state')],
                'main_table.status=state_table.status',
                ['state', 'is_default', 'visible_on_front']
            );
            $this->setFlag('states_joined', true);
        }
        return $this;
    }

    /**
     * Define label order
     * @param string $dir
     * @return $this
     */
    public function orderByLabel($dir = 'ASC')
    {
        $this->getSelect()->order('main_table.label ' . $dir);
        return $this;
    }

    /**
     * Internal constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Cart2Quote\Quotation\Model\Quote\Status',
            'Cart2Quote\Quotation\Model\ResourceModel\Quote\Status'
        );
    }
}
