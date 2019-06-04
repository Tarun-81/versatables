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
 * Class Sequence represents sequence in logic
 */
class Sequence extends \Magento\SalesSequence\Model\Sequence
{
    /**
     * @var string
     */
    private $lastIncrementId;

    /**
     * @var \Magento\SalesSequence\Model\Meta
     */
    private $meta;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    private $entityType;

    /**
     * Sequence constructor.
     * @param \Magento\SalesSequence\Model\Meta $meta
     * @param \Magento\Eav\Model\Entity\Type $entityType
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param string $pattern
     */
    public function __construct(
        \Magento\SalesSequence\Model\Meta $meta,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Framework\App\ResourceConnection $resource,
        $pattern = parent::DEFAULT_PATTERN
    ) {
        $pattern = str_replace("9", $entityType->getIncrementPadLength(), $pattern);
        parent::__construct($meta, $resource, $pattern);
        $this->meta = $meta;
        $this->connection = $resource->getConnection('quotation');
        $this->pattern = $pattern;
        $this->entityType = $entityType;
    }

    /**
     * Calculate current value depends on start value
     *
     * @return string
     */
    private function calculateCurrentValue()
    {
        return ($this->lastIncrementId - $this->meta->getActiveProfile()->getStartValue())
            * $this->meta->getActiveProfile()->getStep()
            + $this->meta->getActiveProfile()->getStartValue();
    }
}
