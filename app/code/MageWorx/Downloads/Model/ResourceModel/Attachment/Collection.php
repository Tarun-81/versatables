<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model\ResourceModel\Attachment;

use MageWorx\Downloads\Model\ResourceModel\Attachment as AttachmentResource;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    const SORT_BY_ALPHABETICAL = 1;
    const SORT_BY_UPLOAD_DATE  = 2;
    const SORT_BY_SIZE         = 3;
    const SORT_BY_DOWNLOADS    = 4;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\Downloads\Helper\Data
     */
    protected $helperData;


    /**
     * @var string
     */
    protected $_idFieldName = 'attachment_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    protected $joinProductFlag = false;


    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\Downloads\Helper\Data $helperData,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->helperData   = $helperData;
        $this->storeManager = $storeManager;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('MageWorx\Downloads\Model\Attachment', 'MageWorx\Downloads\Model\ResourceModel\Attachment');
        $this->_map['fields']['attachment_id'] = 'main_table.attachment_id';
        $this->_map['fields']['section_id']    = 'main_table.section_id';
        $this->_map['fields']['name']          = 'main_table.name';
        $this->_map['fields']['is_active']     = 'main_table.is_active';
        $this->_map['fields']['store']         = 'store_table.store_id';
    }

    /**
     *
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['section_table' => $this->getTable('mageworx_downloads_section')],
            'main_table.section_id = section_table.section_id',
            ['attachment_section_id' => 'section_id', 'section_name' => 'name', 'is_section_active' => 'is_active']
        );

        return $this;
    }

    protected function joinProduct()
    {
        if (!$this->joinProductFlag) {
            $this->getSelect()
                ->joinLeft(
                    ['product_relation_table' => $this->getTable(AttachmentResource::PRODUCT_RELATION_TABLE)],
                    'main_table.attachment_id = product_relation_table.attachment_id',
                    []
                );
            $this->joinProductFlag = true;
        }

        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function performAfterLoad($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);

        if (!count($items)) {
            return;
        }

        $connection = $this->getConnection();
        $select = $connection->select()->from(['attachment_entity_store' => $this->getTable($tableName)])
            ->where('attachment_entity_store.' . $columnName . ' IN (?)', $items);
        $result = $connection->fetchPairs($select);

        if ($result) {
            foreach ($this as $item) {
                $entityId = $item->getData($columnName);
                if (!isset($result[$entityId])) {
                    continue;
                }
                if ($result[$entityId] == 0) {
                    $stores = $this->storeManager->getStores(false, true);
                    $storeId = current($stores)->getId();
                    $storeCode = key($stores);
                } else {
                    $storeId = $result[$item->getData($columnName)];
                    $storeCode = $this->storeManager->getStore($storeId)->getCode();
                }
                $item->setData('_first_store_id', $storeId);
                $item->setData('store_code', $storeCode);
                $item->setData('store_id', [$result[$entityId]]);
            }
        }
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     *
     * @param int $customerGroupId
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection
     */
    public function addCustomerGroupFilter($customerGroupId)
    {
        if (!$this->getFlag('customer_group_filter_added')) {
            $this->getSelect()
            ->joinLeft(
                ['customer_group_relation_table' => $this->getTable(AttachmentResource::CUSTOMER_GROUP_RELATION_TABLE)],
                'main_table.attachment_id = customer_group_relation_table.attachment_id',
                []
            )
            ->where('customer_group_relation_table.customer_group_id = ?', $customerGroupId);
        }
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad(AttachmentResource::STORE_RELATION_TABLE, 'attachment_id');
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable(AttachmentResource::STORE_RELATION_TABLE, 'attachment_id');
    }

    /**
     * Perform adding filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => $store], 'public');

        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function joinStoreRelationTable($tableName, $columnName)
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable($tableName)],
                'main_table.' . $columnName . ' = store_table.' . $columnName,
                []
            )->group(
                'main_table.' . $columnName
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Add type filter
     *
     * @return this
     */
    public function addEnabledFilter()
    {
        return $this->getSelect()->where('main_table.is_active = 1');
    }

    /**
     * Add product filter
     * @param int $productId
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection
     */
    public function addProductFilter($productId)
    {
        if (!$this->joinProductFlag) {
            $this->joinProduct();
        }
        $this->getSelect()->reset(\Zend_Db_Select::GROUP);
        $this->getSelect()->where('product_relation_table.product_id = ?', $productId);

        return $this;
    }

    public function addExcludeAttachmentFilter(array $attachmentIds = [])
    {
        $this->addFieldToFilter('attachment_id', ['nin' => $attachmentIds]);
        return $this;
    }

    /**
     *
     * @param int $sort
     * @return \MageWorx\Downloads\Model\ResourceModel\Attachment\Collection
     */
    public function addSortOrder($sort = null)
    {
        if (!$sort) {
            $sort = $this->helperData->getSortOrder();
        }
        switch ($sort) {
            case self::SORT_BY_ALPHABETICAL:
                $order = 'name asc';
                break;
            case self::SORT_BY_UPLOAD_DATE:
                $order = 'date_added desc';
                break;
            case self::SORT_BY_SIZE:
                $order = 'size desc';
                break;
            case self::SORT_BY_DOWNLOADS:
                $order = 'downloads desc';
                break;
            default:
                $order = 'id asc';
        }
        $this->getSelect()->order($order);
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Zend_Db_Select::ORDER);
        $countSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);

        $totalSelect = clone $countSelect;
        $totalSelect->reset();
        $totalSelect->from(array('t1' => $countSelect), array('COUNT(*)'));

        return $totalSelect;
    }
}
