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

namespace Cart2Quote\Quotation\Model\Quote\Address;

/**
 * Class Renderer used for formatting the quote address
 */
class Renderer extends \Magento\Sales\Model\Order\Address\Renderer
{
    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $addressConfig;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrderAddress
     */
    protected $_quoteAddressToOrderAddress;

    /**
     * Renderer constructor.
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress
     */
    public function __construct(
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress
    ) {
        $this->addressConfig = $addressConfig;
        $this->eventManager = $eventManager;
        $this->_quoteAddressToOrderAddress = $quoteAddressToOrderAddress;
    }

    /**
     * Format quote address like magento formats the order addresses
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param $type
     * @return null|string
     */
    public function formatQuoteAddress(\Magento\Quote\Model\Quote\Address $address, $type)
    {
        $address = $this->_quoteAddressToOrderAddress->convert($address);

        return $this->format($address, $type);
    }
}
