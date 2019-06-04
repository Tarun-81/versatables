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

namespace Cart2Quote\Quotation\Model;

/**
 * Class QuoteRepository
 * @package Cart2Quote\Quotation\Model
 */
class QuoteRepository extends \Magento\Quote\Model\QuoteRepository
    implements \Cart2Quote\Quotation\Api\QuoteRepositoryInterface
{
    /**
     * @param \Cart2Quote\Quotation\Api\Data\QuoteInterface $quote
     */
    public function saveQuote(\Cart2Quote\Quotation\Api\Data\QuoteInterface $quote)
    {
        /**
         * @var \Cart2Quote\Quotation\Model\Quote $quote
         * @var \Magento\Quote\Model\QuoteFactory $quoteFactory
         */
        $quoteFactory = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Quote\Model\QuoteFactory');
        if (!$quote->getId()) {
            $quote->create($quoteFactory->create($quote->getData()));
        } else {
            $quote->save();
        }
    }

    /**
     * @param int $quoteId
     * @param int[] $sharedStoreIds
     */
    public function deleteQuote($quoteId, array $sharedStoreIds)
    {
        $this->delete($this->get($quoteId, $sharedStoreIds));
    }

    /**
     * {@inheritDoc}
     */
    protected function getQuoteCollection()
    {
        /** @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory */
        $collectionFactory = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory::class);

        return $collectionFactory->create();
    }
}
