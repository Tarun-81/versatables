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

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Support;

/**
 * Class DocumentationInfo
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Support
 */
class DocumentationInfo extends \Magento\Config\Block\System\Config\Form\Field implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    const CART2QUOTE_M2_QUICKSTART_MANUAL_URL = 'http://www.cart2quote.com/media/manuals/Cart2Quote_Quick_Start_M2.pdf';
    const CART2QUOTE_M2_INSTALL_MANUAL_URL = 'http://www.cart2quote.com/documentation/magento-2-cart2quote-installation-manual/';
    const CART2QUOTE_M2_USER_MANUAL_URL = 'http://www.cart2quote.com/documentation/magento-2-cart2quote-user-manual/';

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<ul>';
        $html .= __(
            '<li><a href="%1">Cart2Quote - Quick Start Manual</a></li>',
            self::CART2QUOTE_M2_QUICKSTART_MANUAL_URL
        );
        $html .= __(
            '<li><a href="%1">Cart2Quote - Installation Manual</a></li>',
            self::CART2QUOTE_M2_INSTALL_MANUAL_URL
        );
        $html .= __('<li><a href="%1">Cart2Quote - User Manual</a></li>', self::CART2QUOTE_M2_USER_MANUAL_URL);
        $html .= '</ul>';
        $html .= $this->_renderValue($element);

        return $this->_decorateRowHtml($element, $html);
    }
}
