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

namespace Cart2Quote\Quotation\Model\ResourceModel\Collection;

/**
 * Flat sales abstract collection
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Zend_Db_Select
     */
    protected $_countSelect;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot
     */
    protected $entitySnapshot;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @throws \Zend_Exception
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->entitySnapshot = $entitySnapshot;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * get select count sql
     * @return \Zend_Db_Select
     */
    public function getSelectCountSql()
    {
        if (!$this->_countSelect instanceof \Zend_Db_Select) {
            $this->setSelectCountSql(parent::getSelectCountSql());
        }
        return $this->_countSelect;
    }

    /**
     * Set select count sql
     * @param \Zend_Db_Select $countSelect
     * @return $this
     */
    public function setSelectCountSql(\Zend_Db_Select $countSelect)
    {
        $this->_countSelect = $countSelect;
        return $this;
    }

    /**
     * Add attribute to select result set.
     * Backward compatibility with EAV collection
     * @param string $attribute
     * @return $this
     */
    public function addAttributeToSelect($attribute)
    {
        $this->addFieldToSelect($this->_attributeToField($attribute));
        return $this;
    }

    /**
     * Check if $attribute is \Magento\Eav\Model\Entity\Attribute and convert to string field name
     * @param string|\Magento\Eav\Model\Entity\Attribute $attribute
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _attributeToField($attribute)
    {
        $field = false;
        if (is_string($attribute)) {
            $field = $attribute;
        } elseif ($attribute instanceof \Magento\Eav\Model\Entity\Attribute) {
            $field = $attribute->getAttributeCode();
        }
        if (!$field) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We cannot determine the field name.'));
        }
        return $field;
    }

    /**
     * Specify collection select filter by attribute value
     * Backward compatibility with EAV collection
     * @param string|\Magento\Eav\Model\Entity\Attribute $attribute
     * @param array|int|string|null $condition
     * @return $this
     */
    public function addAttributeToFilter($attribute, $condition = null)
    {
        $this->addFieldToFilter($this->_attributeToField($attribute), $condition);
        return $this;
    }

    /**
     * Specify collection select order by attribute value
     * Backward compatibility with EAV collection
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function addAttributeToSort($attribute, $dir = 'asc')
    {
        $this->addOrder($this->_attributeToField($attribute), $dir);
        return $this;
    }

    /**
     * Set collection page start and records to show
     * Backward compatibility with EAV collection
     * @param int $pageNum
     * @param int $pageSize
     * @return $this
     */
    public function setPage($pageNum, $pageSize)
    {
        $this->setCurPage($pageNum)->setPageSize($pageSize);
        return $this;
    }

    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Create all ids retrieving select with limitation
     * Backward compatibility with EAV collection
     * @param int $limit
     * @param int $offset
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Zend_Db_Select::ORDER);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(\Zend_Db_Select::COLUMNS);
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->limit($limit, $offset);
        return $idsSelect;
    }

    /**
     * Get search criteria.
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * Returns a collection item that corresponds to the fetched row
     * and moves the internal data pointer ahead
     * All returned rows marked as non changed to prevent unnecessary persistence operations
     * @return  \Magento\Framework\Object|bool
     */
    public function fetchItem()
    {
        if (null === $this->_fetchStmt) {
            $this->_renderOrders()->_renderLimit();

            $this->_fetchStmt = $this->getConnection()->query($this->getSelect());
        }
        $data = $this->_fetchStmt->fetch();
        if (!empty($data) && is_array($data)) {
            /** @var \Magento\Sales\Model\AbstractModel $item */
            $item = $this->getNewEmptyItem();
            if ($this->getIdFieldName()) {
                $item->setIdFieldName($this->getIdFieldName());
            }
            $item->setData($data);
            $this->entitySnapshot->registerSnapshot($item);
            return $item;
        }
        return false;
    }

    /**
     * Load data with filter in place
     * All returned rows marked as non changed to prevent unnecessary persistence operations
     * @param   bool $printQuery
     * @param   bool $logQuery
     * @return  $this
     */
    public function loadWithFilter($printQuery = false, $logQuery = false)
    {
        $this->_beforeLoad();
        $this->_renderFilters()->_renderOrders()->_renderLimit();
        $this->printLogQuery($printQuery, $logQuery);
        $data = $this->getData();
        $this->resetData();
        if (is_array($data)) {
            foreach ($data as $row) {
                $item = $this->getNewEmptyItem();
                if ($this->getIdFieldName()) {
                    $item->setIdFieldName($this->getIdFieldName());
                }
                $item->addData($row);
                $this->entitySnapshot->registerSnapshot($item);
                $this->addItem($item);
            }
        }
        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }
}
