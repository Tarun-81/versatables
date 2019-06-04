<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model;

class Section extends \Magento\Framework\Model\AbstractModel
{

    const STATUS_ENABLED     = 1;
    const STATUS_DISABLED    = 0;

    const DEFAULT_ID         = 1;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\Downloads\Model\ResourceModel\Section');
    }

    /**
     *
     * @return array
     */
    public function getDefaultValues()
    {
        parent::beforeDelete();
        return ['is_active' => self::STATUS_DISABLED];
    }

    /**
     *
     * @return \MageWorx\Downloads\Model\Section
     */
    public function delete()
    {
        if ($this->getId() == self::DEFAULT_ID) {
            return $this;
        }
        $this->getResource()->delete($this);
    }

    /**
     * Retrieve section list
     *
     * @return array
     */
    public function getSectionList()
    {
        $data = [];
        $sections = $this->getResource()->getEnabledSections();
        if ($sections) {
            foreach ($sections as $value) {
                $data[$value['section_id']] = (string)$value['name'];
            }
        }

        return $data;
    }
}
