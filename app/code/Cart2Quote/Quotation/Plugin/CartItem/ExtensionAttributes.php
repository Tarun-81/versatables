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

namespace Cart2Quote\Quotation\Plugin\CartItem;

/**
 * Class ExtensionAttributes
 * @package Cart2Quote\Quotation\Plugin
 */
class ExtensionAttributes
{
    /**
     * @var \Magento\Quote\Api\Data\CartItemExtensionFactory
     */
    private $quoteItemExtensionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory
     */
    private $sectionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Item\Section\Provider
     */
    private $provider;

    /**
     * CartItemLoad constructor.
     * @param \Cart2Quote\Quotation\Model\Quote\Item\Section\Provider $provider
     * @param \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory $sectionFactory
     * @param \Magento\Quote\Api\Data\CartItemExtensionFactory $quoteItemExtensionFactory
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\Item\Section\Provider $provider,
        \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory $sectionFactory,
        \Magento\Quote\Api\Data\CartItemExtensionFactory $quoteItemExtensionFactory
    ) {
        $this->quoteItemExtensionFactory = $quoteItemExtensionFactory;
        $this->sectionFactory = $sectionFactory;
        $this->provider = $provider;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $entity
     * @param \Magento\Quote\Api\Data\CartItemExtensionInterface|null $extension
     * @return \Magento\Quote\Api\Data\CartItemExtension|\Magento\Quote\Api\Data\CartItemExtensionInterface
     */
    public function afterGetExtensionAttributes(
        \Magento\Quote\Api\Data\CartItemInterface $entity,
        \Magento\Quote\Api\Data\CartItemExtensionInterface $extension = null
    ) {
        if ($extension === null) {
            $extension = $this->quoteItemExtensionFactory->create();
            $extension->setSection($this->provider->getSection($entity->getItemId()));
            $entity->setExtensionAttributes($extension);
        }

        return $extension;
    }
}