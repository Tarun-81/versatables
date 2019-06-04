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

namespace Cart2Quote\Quotation\Plugin;

/**
 * Class Quote
 * @package Cart2Quote\Quotation\Model\Plugin\Quote
 */
class Quote
{

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Section\Provider
     */
    private $provider;
    /**
     * @var \Magento\Quote\Api\Data\CartExtensionFactory
     */
    private $quoteExtensionFactory;

    /**
     * Repository constructor.
     * @param \Cart2Quote\Quotation\Model\Quote\Section\Provider $provider
     * @param \Magento\Quote\Api\Data\CartExtensionFactory $quoteExtensionFactory
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\Section\Provider $provider,
        \Magento\Quote\Api\Data\CartExtensionFactory $quoteExtensionFactory
    ) {
        $this->provider = $provider;
        $this->quoteExtensionFactory = $quoteExtensionFactory;
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $subject
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function afterLoad(\Cart2Quote\Quotation\Model\Quote $subject, \Cart2Quote\Quotation\Model\Quote $quote)
    {
        $extensionAttributes = $quote->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->quoteExtensionFactory->create();
        }
        $sections = $this->provider->getSections($quote->getId());
        $extensionAttributes->setSections($sections);
        $quote->setExtensionAttributes($extensionAttributes);
        return $quote;
    }
}