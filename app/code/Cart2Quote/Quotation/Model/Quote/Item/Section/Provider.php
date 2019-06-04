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

namespace Cart2Quote\Quotation\Model\Quote\Item\Section;

/**
 * Class Provider
 * @package Cart2Quote\Quotation\Model\Quote\Section
 */
class Provider implements \Cart2Quote\Quotation\Api\Quote\Item\SectionProviderInterface
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section\Loader
     */
    private $loader;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory
     */
    private $sectionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section
     */
    private $sectionResourceModel;

    /**
     * Provider constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section\Loader $loader
     * @param \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory $sectionFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $sectionResourceModel
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section\Loader $loader,
        \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory $sectionFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $sectionResourceModel
    ) {
        $this->loader = $loader;
        $this->sectionFactory = $sectionFactory;
        $this->sectionResourceModel = $sectionResourceModel;
    }

    /**
     * @param int $itemId
     * @return \Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface
     */
    public function getSection($itemId)
    {
        $section = $this->sectionFactory->create();
        $section->setItemId($itemId);
        $sectionId = $this->loader->getIdByItemId($itemId);
        $this->sectionResourceModel->load($section, $sectionId);
        return $section;
    }
}