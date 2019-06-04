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

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote\View;

/**
 * Class ConfigureQuoteItems
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote\View
 */
class ConfigureQuoteItems extends \Magento\Backend\App\Action
{
    /**
     * Quote Item
     *
     * @var \Magento\Quote\Model\Quote\Item
     */
    protected $item;

    /**
     * Quote Option
     *
     * @var \Magento\Quote\Model\Quote\Item\Option
     */
    protected $option;

    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $session;

    /**
     * Product Composite
     *
     * @var \Magento\Catalog\Helper\Product\Composite
     */
    protected $composite;

    /**
     * Data Object Factory
     *
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * ConfigureQuoteItems constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param \Magento\Quote\Model\Quote\Item\Option $option
     * @param \Cart2Quote\Quotation\Model\Session $session
     * @param \Magento\Catalog\Helper\Product\Composite $composite
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Quote\Model\Quote\Item $item,
        \Magento\Quote\Model\Quote\Item\Option $option,
        \Cart2Quote\Quotation\Model\Session $session,
        \Magento\Catalog\Helper\Product\Composite $composite,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        $this->item = $item;
        $this->option = $option;
        $this->session = $session;
        $this->composite = $composite;
        $this->dataObjectFactory = $dataObjectFactory;

        parent::__construct($context);
    }

    /**
     * Ajax handler to response configuration fieldset of composite product in quote items
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $configureResult = $this->dataObjectFactory->create();
        try {
            $quoteItemId = $this->getQuoteItemId();
            $quoteItem = $this->getQuoteItem($quoteItemId);

            $configureResult->setOk(true);
            $optionCollection = $this->option->getCollection()->addItemFilter([$quoteItemId]);
            $quoteItem->setOptions($optionCollection->getOptionsByItem($quoteItem));

            $configureResult
                ->setBuyRequest($quoteItem->getBuyRequest())
                ->setCurrentStoreId($quoteItem->getStoreId())
                ->setProductId($quoteItem->getProductId())
                ->setCurrentCustomerId($this->session->getCustomerId());

        } catch (\Exception $e) {
            $configureResult
                ->setError(true)
                ->setMessage($e->getMessage());
        }

        return $this->composite->renderConfigureResult($configureResult);
    }

    /**
     * Get the quote item id
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getQuoteItemId()
    {
        $quoteItemId = (int)$this->getRequest()->getParam('id');
        if (!$quoteItemId) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Quote item id is not received.'));
        }

        return $quoteItemId;
    }

    /**
     * Get the quote item
     *
     * @param int $quoteItemId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getQuoteItem($quoteItemId)
    {
        $quoteItem = $this->item->load($quoteItemId);
        if (!$quoteItem->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Quote item is not loaded.'));
        }

        return $quoteItem;
    }
}

