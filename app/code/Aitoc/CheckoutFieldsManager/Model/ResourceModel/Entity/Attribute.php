<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\ResourceModel\Entity;

use Aitoc\CheckoutFieldsManager\Helper\Data as Helper;
use Magento\Framework\Model\AbstractModel;

class Attribute extends \Magento\Eav\Model\ResourceModel\Entity\Attribute
{

    /**
     * @inheritdoc
     */
    protected function _afterSave(AbstractModel $object)
    {
        $pageId = $object->getId();
        $oldStores = $this->lookupStoreIds($pageId);
        $newStores = (array)$object->getStores();

        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId() ?: $oldStores;
        }

        $table = $this->getTable('aitoc_checkout_eav_attribute_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['attribute_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['attribute_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * Get store ids to which specified attribute is assigned
     *
     * @param int $pageId
     *
     * @return array
     */
    public function lookupStoreIds($pageId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('aitoc_checkout_eav_attribute_store'),
            'store_id'
        )->where(
            'attribute_id = ?',
            (int)$pageId
        );

        return $connection->fetchCol($select);
    }

    /**
     * Perform operations after object load: Set selected store_ids for attribute
     *
     * @inheritdoc
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', empty($stores) ? [0] : $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute|AbstractModel $object
     * @param int|string $optionId
     * @param int $intOptionId
     * @param array $defaultValue
     */
    protected function _updateDefaultValue($object, $optionId, $intOptionId, &$defaultValue)
    {
        $array_types_single = ['select', 'radiobutton'];
        $array_types_multi = ['multiselect','checkbox'];
        if (in_array($optionId, $object->getDefault())) {
            $frontendInput = $object->getFrontendInput();
            if (in_array($frontendInput, $array_types_multi)) {
                $defaultValue[] = $intOptionId;
            } elseif (in_array($frontendInput, $array_types_single)) {
                $defaultValue = [$intOptionId];
            }
        }
    }

    /**
     * @param \Aitoc\CheckoutFieldsManager\Model\Entity\Attribute | AbstractModel $object
     *
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$object->getSourceModel()) {
            if (in_array($object->getFrontendInput(), ['select','multiselect'])) {
                $object->setSourceModel('Aitoc\CheckoutFieldsManager\Model\Entity\Source\Table');
            }
            if (in_array($object->getFrontendInput(), ['checkbox','radiobutton'])) {
                $object->setSourceModel('Aitoc\CheckoutFieldsManager\Model\Entity\Attribute\Source\Checkbox');
            }
            if ($object->getBackendType() == 'int' && $object->getFrontendInput() == 'boolean') {
                $object->setSourceModel('Aitoc\CheckoutFieldsManager\Model\Entity\Source\Boolean');
            }
        }

        $step = Helper::getCheckoutStepByDisplayArea($object->getDisplayArea());
        $object->setCheckoutStep($step);

        return parent::_beforeSave($object);
    }
}
