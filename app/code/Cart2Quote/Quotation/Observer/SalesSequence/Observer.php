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

namespace Cart2Quote\Quotation\Observer\SalesSequence;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CreateSequence
 */
class Observer implements ObserverInterface
{
    /**
     * @var \Magento\SalesSequence\Model\Builder
     */
    protected $sequenceBuilder;

    /**
     * @var \Magento\SalesSequence\Model\EntityPool
     */
    protected $entityPool;

    /**
     * @var \Cart2Quote\Quotation\Model\SalesSequence\Config
     */
    protected $sequenceConfig;

    /**
     * @param \Magento\SalesSequence\Model\Builder $sequenceBuilder
     * @param \Cart2Quote\Quotation\Model\SalesSequence\EntityPool $entityPool
     * @param \Cart2Quote\Quotation\Model\SalesSequence\Config $sequenceConfig
     */
    public function __construct(
        \Magento\SalesSequence\Model\Builder $sequenceBuilder,
        \Cart2Quote\Quotation\Model\SalesSequence\EntityPool $entityPool,
        \Cart2Quote\Quotation\Model\SalesSequence\Config $sequenceConfig
    ) {
        $this->sequenceBuilder = $sequenceBuilder;
        $this->entityPool = $entityPool;
        $this->sequenceConfig = $sequenceConfig;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $observer->getData('store')->getId();
        foreach ($this->entityPool->getEntities() as $entityType) {
            $this->sequenceBuilder->setPrefix($this->sequenceConfig->get('prefix'))
                ->setSuffix($this->sequenceConfig->get('suffix'))
                ->setStartValue($this->sequenceConfig->get('startValue'))
                ->setStoreId($storeId)
                ->setStep($this->sequenceConfig->get('step'))
                ->setWarningValue($this->sequenceConfig->get('warningValue'))
                ->setMaxValue($this->sequenceConfig->get('maxValue'))
                ->setEntityType($entityType)
                ->create();
        }
        return $this;
    }
}
