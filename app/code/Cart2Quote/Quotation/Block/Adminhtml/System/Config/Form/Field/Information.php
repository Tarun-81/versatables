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

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class Information
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class Information extends \Magento\Config\Block\System\Config\Form\Field implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{

    /**
     * Information constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param string $template
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        $template = 'Cart2Quote_Quotation::system/config/form/field/information.phtml'
    ) {
        parent::__construct($context);
        $this->_template = $template;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * @return string
     */
    public function getCart2QuoteSupportPageUrl()
    {
        return sprintf('%s/%s', $this->getCart2QuoteUrl(), 'magento-quotation-module-support.html');
    }

    /**
     * @return string
     */
    public function getCart2QuoteUrl()
    {
        return 'https://www.cart2quote.com';
    }

    /**
     * @param $href
     * @param $value
     * @param string $target
     * @return string
     */
    public function getLink($href, $value, $target = '_blank')
    {
        return sprintf('<a href="%s" target="%s">%s</a>', $href, $target, $value);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
