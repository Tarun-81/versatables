<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model\ResourceModel\Section;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'section_id';

    public function _construct()
    {
        $this->_init('MageWorx\Downloads\Model\Section', 'MageWorx\Downloads\Model\ResourceModel\Section');
    }

    /**
     *
     * @return \MageWorx\Downloads\Model\ResourceModel\Section\Collection
     */
    public function addSortOrder()
    {
        $this->getSelect()->order($this->getIdFieldName());
        return $this;
    }
}
