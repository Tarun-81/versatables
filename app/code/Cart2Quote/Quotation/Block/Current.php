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

namespace Cart2Quote\Quotation\Block;

/**
 * Class Current
 */

class Current extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var bool
     */
    protected $_visibilityEnabled;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Current constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $data
    ) {
        $this->quotationHelper = $quotationHelper;
        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getIsQuotationEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * Check if Cart2Quote visibility is enabled
     * @return bool
     */
    public function getIsQuotationEnabled()
    {
        if (isset($this->_visibilityEnabled)) {
            return $this->_visibilityEnabled;
        }

        if ($this->quotationHelper->isFrontendEnabled()) {
            $this->_visibilityEnabled = true;
            return true;
        }

        $this->_visibilityEnabled = false;
        return false;
    }
}
