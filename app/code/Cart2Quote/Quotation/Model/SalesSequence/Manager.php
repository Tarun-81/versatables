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

namespace Cart2Quote\Quotation\Model\SalesSequence;

/**
 * Class Manager
 */
class Manager extends \Magento\SalesSequence\Model\Manager
{
    /**
     * @var \Magento\SalesSequence\Model\ResourceModel\Meta
     */
    protected $resourceSequenceMeta;

    /**
     * @var \Magento\SalesSequence\Model\SequenceFactory
     */
    protected $sequenceFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $entityConfig;

    /**
     * Manager constructor.
     * @param \Magento\SalesSequence\Model\ResourceModel\Meta $resourceSequenceMeta
     * @param \Magento\SalesSequence\Model\SequenceFactory $sequenceFactory
     * @param \Magento\Eav\Model\Entity\Type $entityConfig
     */
    public function __construct(
        \Magento\SalesSequence\Model\ResourceModel\Meta $resourceSequenceMeta,
        \Magento\SalesSequence\Model\SequenceFactory $sequenceFactory,
        \Magento\Eav\Model\Entity\Type $entityConfig
    ) {
        parent::__construct(
            $resourceSequenceMeta,
            $sequenceFactory
        );
        $this->resourceSequenceMeta = $resourceSequenceMeta;
        $this->sequenceFactory = $sequenceFactory;
        $this->entityConfig = $entityConfig;
    }

    /**
     * Returns sequence for given entityType and store
     *
     * @param string $entityType
     * @param int $storeId
     * @return \Magento\Framework\DB\Sequence\SequenceInterface
     */
    public function getSequence($entityType, $storeId)
    {
        return $this->sequenceFactory->create(
            [
                'meta' => $this->resourceSequenceMeta->loadByEntityTypeAndStore(
                    $entityType,
                    $storeId
                ),
                'entityType' => $this->entityConfig->loadByCode($entityType)
            ]
        );
    }
}
