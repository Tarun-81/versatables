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

namespace Cart2Quote\Quotation\Block\Checkout\Cart\Item;

/**
 * Class Renderer
 * @package Cart2Quote\Quotation\Block\Checkout\Cart\Item
 */
class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $cart2QuoteHelper;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * Renderer constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $messageInterpretationStrategy
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $messageInterpretationStrategy,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        array $data = []
    ) {
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        $this->_quoteFactory = $quoteFactory;
        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageBuilder,
            $urlHelper,
            $messageManager,
            $priceCurrency,
            $moduleManager,
            $messageInterpretationStrategy,
            $data
        );
    }

    /**
     * Check disabled product remark field
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->cart2QuoteHelper->isProductRemarkDisabled();
    }

    /**
     * Check quote can accept
     * @return boolean
     */
    public function canAccept()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        $quote = $this->_quoteFactory->create()->load($quoteId);

        return $this->cart2QuoteHelper->canAccept($quote);
    }

    /**
     * get delete item url
     * @param $tierItemId
     * @return string
     */
    public function getDeleteUrl($tierItemId)
    {
        $quoteId = $this->getRequest()->getParam('quote_id');

        return $this->getUrl('quotation/quote/deleteTierItem', ['id' => $tierItemId, 'quote_id' => $quoteId]);
    }
}
