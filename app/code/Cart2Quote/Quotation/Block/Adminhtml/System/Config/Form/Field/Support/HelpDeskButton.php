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
 * Class HelpDeskButton
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Support
 */
class HelpDeskButton extends \Magento\Config\Block\System\Config\Form\Field implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        //the text on the button itself
        $buttonText = '';

        // the full verb to add in the instructions
        $fullActionText = '';

        //the result of filling out the form (bug report or support ticket)
        $resultText = '';

        switch ($element->getLabel()) {
            case 'Create Ticket':
                $buttonText = __('Create Ticket');
                $fullActionText = __('Create A Ticket');
                $resultText = __('Support Ticket');
                break;
            case 'Report Bug':
                $buttonText = __('Report Bug');
                $fullActionText = __('Report A Bug');
                $resultText = __('Bug Report');
                break;
        }

        $element->setValue($buttonText);
        $element->setClass('action-default scalable ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only');
        $element->addCustomAttribute(
            "onClick",
            "window.open('https://cart2quote.zendesk.com/hc/en-us/requests/new', '_blank')"
        );

        $html = __(
            '<p>Clicking the button below will open a new page where you can %1 Please fill out the form completely.
            In order to handle your %2 properly we need as much information as you can provide.</p>',
            $fullActionText,
            $resultText
        );
        $html .= $this->_renderValue($element);

        return $this->_decorateRowHtml($element, $html);
    }
}
