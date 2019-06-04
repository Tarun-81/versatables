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

namespace Cart2Quote\Quotation\Api;

/**
 * Interface QuoteRepositoryInterface
 * @package Cart2Quote\Quotation\Api
 */
interface QuoteRepositoryInterface
{
    /**
     * Enables an administrative user to return information for a specified cart.
     *
     * @param int $quoteId
     * @return \Cart2Quote\Quotation\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($quoteId);

    /**
     * Save quote
     *
     * @param \Cart2Quote\Quotation\Api\Data\QuoteInterface $quote
     * @return void
     */
    public function saveQuote(\Cart2Quote\Quotation\Api\Data\QuoteInterface $quote);

    /**
     * Delete quote
     *
     * @param int $quoteId
     * @param int[] $sharedStoreIds
     * @return void
     */
    public function deleteQuote($quoteId, array $sharedStoreIds);
}
