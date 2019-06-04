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
 * Abstract quotation entity provides to its children knowledge about eventPrefix and eventObject
 */
abstract class EntityAbstract extends \Magento\Sales\Model\ResourceModel\EntityAbstract
{
    /**
     * Event prefix
     * @var string
     */
    protected $_eventPrefix = 'quotation_quote_resource';

    /**
     * Event object
     * @var string
     */
    protected $_eventObject = 'resource';

    /**
     * Use additional is object new check for this resourcemodel
     * @var bool
     */
    protected $_useIsObjectNew = true;

    /**
     * @var \Magento\Eav\Model\Entity\TypeFactory
     */
    protected $_eavEntityTypeFactory;

    /**
     * @var \Magento\SalesSequence\Model\Manager
     */
    protected $sequenceManager;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot
     */
    protected $entitySnapshot;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite
     */
    protected $entityRelationComposite;

    /**
     * EntityAbstract constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Attribute $attribute
     * @param \Magento\SalesSequence\Model\Manager $sequenceManager
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite $entityRelationComposite
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Sales\Model\ResourceModel\Attribute $attribute,
        \Magento\SalesSequence\Model\Manager $sequenceManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite $entityRelationComposite,
        $resourcePrefix = null
    ) {
        $this->attribute = $attribute;
        $this->entitySnapshot = $entitySnapshot;
        $this->entityRelationComposite = $entityRelationComposite;
        parent::__construct(
            $context,
            $entitySnapshot,
            $entityRelationComposite,
            $attribute,
            $sequenceManager,
            $resourcePrefix
        );
    }

    /**
     * Save entity
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->isDeleted()) {
            return $this->delete($object);
        }
        if (!$this->entitySnapshot->isModified($object)) {
            $this->entityRelationComposite->processRelations($object);
            return $this;
        }
        $this->beginTransaction();

        try {
            $object->validateBeforeSave();
            $object->beforeSave();
            if ($object->isSaveAllowed()) {
                $this->_serializeFields($object);
                $this->_beforeSave($object);
                $this->_checkUnique($object);
                $this->objectRelationProcessor->validateDataIntegrity($this->getMainTable(), $object->getData());
                if ($object->getId() !== null && (!$this->_useIsObjectNew || !$object->isObjectNew())) {
                    $condition = $this->getConnection()->quoteInto($this->getIdFieldName() . '=?', $object->getId());
                    $data = $this->_prepareDataForSave($object);
                    unset($data[$this->getIdFieldName()]);
                    $this->getConnection()->update($this->getMainTable(), $data, $condition);
                } else {
                    $bind = $this->_prepareDataForSave($object);
                    unset($bind[$this->getIdFieldName()]);
                    $this->getConnection()->insert($this->getMainTable(), $bind);
                    $object->setId($this->getConnection()->lastInsertId($this->getMainTable()));
                    if ($this->_useIsObjectNew) {
                        $object->isObjectNew(false);
                    }
                }
                $this->unserializeFields($object);
                $this->_afterSave($object);
                $this->entitySnapshot->registerSnapshot($object);
                $object->afterSave();
                $this->entityRelationComposite->processRelations($object);
            }
            $this->addCommitCallback([$object, 'afterCommitCallback'])->commit();
            $object->setHasDataChanges(false);
        } catch (\Exception $e) {
            $this->rollBack();
            $object->setHasDataChanges(true);
            throw $e;
        }
        return $this;
    }

    /**
     * Perform actions after object delete
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterDelete($object);
        return $this;
    }

    /**
     * Perform actions after object load, mark loaded data as data without changes
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\Object $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->entitySnapshot->registerSnapshot($object);
        return $this;
    }
}
