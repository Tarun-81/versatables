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
 * Flat quotation quote status history resourcemodel
 */
class History extends \Cart2Quote\Quotation\Model\ResourceModel\EntityAbstract
    implements \Cart2Quote\Quotation\Model\Spi\QuoteStatusHistoryResourceInterface
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Status\History\Validator
     */
    protected $validator;
    /**
     * Event prefix
     * @var string
     */
    protected $_eventPrefix = 'quotation_quote_status_history_resource';

    /**
     * History constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Attribute $attribute
     * @param \Magento\SalesSequence\Model\Manager $sequenceManager
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite $entityRelationComposite
     * @param \Cart2Quote\Quotation\Model\Quote\Status\History\Validator $validator
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Sales\Model\ResourceModel\Attribute $attribute,
        \Magento\SalesSequence\Model\Manager $sequenceManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite $entityRelationComposite,
        \Cart2Quote\Quotation\Model\Quote\Status\History\Validator $validator,
        $resourcePrefix = null
    ) {
        $this->validator = $validator;
        parent::__construct(
            $context,
            $attribute,
            $sequenceManager,
            $entitySnapshot,
            $entityRelationComposite,
            $resourcePrefix
        );
    }

    /**
     * Perform actions before object save
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_beforeSave($object);
        $warnings = $this->validator->validate($object);
        if (!empty($warnings)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Cannot save comment:\n%1", implode("\n", $warnings))
            );
        }
        return $this;
    }

    /**
     * Model initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init('quotation_quote_status_history', 'entity_id');
    }
}
