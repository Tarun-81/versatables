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

namespace Cart2Quote\Quotation\Block\Quote;

use Magento\Quote\Model\Quote;

/**
 * Quotation quote abstract block
 */
class AbstractQuote extends \Magento\Framework\View\Element\Template
{
    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    /**
     * @var Quote|null
     */
    protected $_quote = null;

    /**
     * @var array
     */
    protected $_totals;

    /**
     * @var array
     */
    protected $_itemRenders = [];

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $_quotationSession;

    /**
     * @var bool
     */
    protected $visibilityEnabled;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $data = []
    ) {
        $this->quotationHelper = $quotationHelper;
        $this->_customerSession = $customerSession;
        $this->_quotationSession = $quotationSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Get all quote items
     * @return array
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Get active quote
     * @return Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->_quotationSession->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Get item row html
     * @param   \Magento\Quote\Model\Quote\Item $item
     * @return  string
     */
    public function getItemHtml(\Magento\Quote\Model\Quote\Item $item)
    {
        $renderer = $this->getItemRenderer($item->getProductType())->setItem($item);
        return $renderer->toHtml();
    }

    /**
     * Retrieve item renderer block
     * @param string|null $type
     * @return \Magento\Framework\View\Element\Template
     * @throws \RuntimeException
     */
    public function getItemRenderer($type = null)
    {
        if ($type === null) {
            $type = self::DEFAULT_TYPE;
        }
        $rendererList = $this->_getRendererList();
        if (!$rendererList) {
            throw new \RuntimeException('Renderer list for block "' . $this->getNameInLayout() . '" is not defined');
        }
        $overriddenTemplates = $this->getOverriddenTemplates() ?: [];
        $template = isset($overriddenTemplates[$type]) ? $overriddenTemplates[$type] : $this->getRendererTemplate();
        return $rendererList->getRenderer($type, self::DEFAULT_TYPE, $template);
    }

    /**
     * Retrieve renderer list
     * @return \Magento\Framework\View\Element\RendererList
     */
    protected function _getRendererList()
    {
        return $this->getRendererListName() ? $this->getLayout()->getBlock(
            $this->getRendererListName()
        ) : $this->getChildBlock(
            'renderer.list'
        );
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        return $this->getTotalsCache();
    }

    /**
     * @return array
     */
    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $this->_totals = $this->getQuote()->getTotals();
        }
        return $this->_totals;
    }

    /**
     * Check if Cart2Quote visibility is enabled
     * @return bool
     */
    public function getIsQuotationEnabled()
    {
        if (isset($this->visibilityEnabled)) {
            return $this->visibilityEnabled;
        }

        if ($this->quotationHelper->isFrontendEnabled()) {
            $this->visibilityEnabled = true;
            return true;
        }

        $this->visibilityEnabled = false;
        return false;
    }
}
