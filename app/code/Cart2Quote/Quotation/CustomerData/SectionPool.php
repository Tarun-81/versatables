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

namespace Cart2Quote\Quotation\CustomerData;

/**
 * Class SectionPool
 * @package Cart2Quote\Quotation\CustomerData
 */
class SectionPool extends \Magento\Customer\CustomerData\SectionPool
{
    /**
     *  Class name of Cart2Quote quote section
     */
    const QUOTATION_SECTION_CLASS = 'Cart2Quote\Quotation\CustomerData\Quote';

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * SectionPool constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Customer\CustomerData\Section\Identifier $identifier
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param array $sectionSourceMap
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\CustomerData\Section\Identifier $identifier,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $sectionSourceMap
    ) {
        $this->quotationHelper = $quotationHelper;
        parent::__construct($objectManager, $identifier, $sectionSourceMap);
    }

    /**
     * Get section source by name
     *
     * @param string $name
     * @return SectionSourceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function get($name)
    {
        if ($name != self::QUOTATION_SECTION_CLASS || $this->quotationHelper->isFrontendEnabled()) {
            return parent::get($name);
        }
    }
}
