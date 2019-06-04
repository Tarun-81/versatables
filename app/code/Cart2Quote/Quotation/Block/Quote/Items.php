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

namespace Cart2Quote\Quotation\Block\Quote;

/**
 * Class Items
 * @package Cart2Quote\Quotation\Block\Quote
 */
class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Quotation Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Tier item collection
     *
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    protected $tierItemCollection;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\SectionFactory
     */
    private $sectionFactory;

    /**
     * Items constructor.
     * @param \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemCollection
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemCollection,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->quotationHelper = $quotationHelper;
        $this->tierItemCollection = $tierItemCollection;
        parent::__construct($context, $data);
        $this->sectionFactory = $sectionFactory;
    }

    /**
     * Check disabled product remark field
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->quotationHelper->isProductRemarkDisabled();
    }

    /**
     * Check if an optional product exists on the quote
     *
     * @return int
     */
    public function hasOptionalProducts()
    {
        if (!$this->tierItemCollection->isLoaded()) {
            $tableName = $this->tierItemCollection->getTable('quote_item');
            $this->tierItemCollection
                ->addFieldToFilter('make_optional', true)
                ->join(
                    $tableName,
                    sprintf(
                        '`%s`.item_id = `main_table`.item_id AND `%s`.quote_id = %s',
                        $tableName,
                        $tableName,
                        $this->getQuote()->getId()
                    )
                );
        }

        return (bool)$this->tierItemCollection->count();
    }

    /**
     * Retrieve current quote model instance
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->getQuote()->getSections(true);
    }

    /**
     * Get config setting for hide prices dashboard
     *
     * @param $quote
     * @return bool
     */
    public function isHidePrices($quote)
    {
        return $this->quotationHelper->isHidePrices($quote);
    }
}
