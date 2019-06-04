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

namespace Cart2Quote\Quotation\Api\Data;

/**
 * Quote status history search result interface.
 * An quote is a document that a web store issues to a customer.
 * Magento generates a quotation quote that lists the product items, billing and shipping addresses,
 * and shipping and payment methods. A corresponding external document, known as
 * a quote proposal, is emailed to the customer.
 * @api
 */
interface QuoteStatusHistorySearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Gets collection items.
     * @return \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     * @param \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
