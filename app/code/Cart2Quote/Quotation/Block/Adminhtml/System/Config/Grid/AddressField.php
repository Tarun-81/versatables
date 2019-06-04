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

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Grid;

/**
 * Class AddressField
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Grid
 */
class AddressField extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Cart2Quote_Quotation::system/config/grid/address.phtml';

    /**
     * Get grid configuration
     *
     * @return string
     */
    public function getAddressGridConfig()
    {
        return json_encode([
            'field' => [
                'name' => $this->getElement()->getName(),
                'label' => $this->getElement()->getLabel(),
                'htmlId' => $this->getElement()->getHtmlId(),
                'fieldValue' => $this->getElement()->getValue()
            ]
        ]);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);

        return $this->toHtml();
    }

}
