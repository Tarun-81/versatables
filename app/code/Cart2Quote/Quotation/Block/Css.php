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

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Button
 */
class Css extends \Magento\Framework\View\Element\Template
{
    /**
     * Xml pah to quoteadv sidebar extra css
     */
    const XML_PATH_QUOTATION_SIDEBAR_EXTRA_CSS = 'cart2quote_advanced/general/extra_global_css';

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Css constructor.
     * @param Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $data
    ) {
        $this->quotationHelper = $quotationHelper;
        parent::__construct($context, $data);
    }

    /**
     * Return if the theme is enabled
     */
    public function isThemeEnabled()
    {
        return $this->quotationHelper->isFrontendEnabled();
    }

    /**
     * Return the extra CSS set in the backend
     */
    public function getExtraCSS()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_QUOTATION_SIDEBAR_EXTRA_CSS,
            ScopeInterface::SCOPE_STORE
        );
    }
}
