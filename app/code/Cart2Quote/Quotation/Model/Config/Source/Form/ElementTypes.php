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

/**
 * Cart2Quote
 * Used in creating options for Form element types config value selection
 *
 */

namespace Cart2Quote\Quotation\Model\Config\Source\Form;

/**
 * Class ElementTypes
 * @package Cart2Quote\Quotation\Model\Config\Source\Form
 */
class ElementTypes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * Standard library element types
     *
     * @var string[]
     * @see \Magento\Framework\Data\Form\Element\Factory::$_standardTypes
     */
    protected $_standardTypes = [
//        'button',
//        'checkbox',
//        'checkboxes',
//        'column',
//        'date',
//        'editablemultiselect',
//        'editor',
//        'fieldset',
//        'file',
//        'gallery',
//        'hidden',
//        'image',
//        'imagefile',
//        'label',
//        'link',
//        'multiline',
//        'multiselect',
//        'note',
//        'obscure',
//        'password',
//        'radio',
//        'radios',
//        'reset',
//        'select',
//        'submit',
        'text',
        'textarea',
//        'time',
    ];

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = [];

            foreach ($this->_standardTypes as $standardType) {
                $this->_options[] = ['value' => $standardType, 'label' => __(ucfirst($standardType))];
            }
        }

        return $this->_options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_standardTypes;
    }
}
