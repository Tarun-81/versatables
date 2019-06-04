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

namespace Cart2Quote\Quotation\Helper\Data;

/**
 * Class License
 * @package Cart2Quote\Quotation\Helper\Data
 */
final class License implements LicenseInterface
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * License constructor.
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(\Magento\Backend\Model\UrlInterface $backendUrl)
    {
        $this->backendUrl = $backendUrl;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        // @codingStandardsIgnoreLine
        return parse_url($this->backendUrl->getBaseUrl(), PHP_URL_HOST);
    }

    /**
     * @return string
     */
    public function getLicenseType()
    {
        return 'opensource';
    }

    /**
     * @return string
     */
    public function getLicenseState()
    {
        return 'active';
    }

    /**
     * @return bool
     */
    public function isActiveState()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isInactiveState()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isPendingState()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isUnreachableState()
    {
        return false;
    }
}