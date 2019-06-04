<?php
/**
 *
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2017] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 */

namespace Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field;

/**
 * Class Checkbox
 *
 * @package Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field
 */
class Checkbox extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $column = $this->getColumn();
        $columnName = $this->getColumnName();
        $inputName = $this->getInputName();
        $inputId = $this->getInputId();
        $size = $column['size'] ? sprintf('size="%s"', $column['size']) : '';
        $class = isset($column['class']) ? $column['class'] : 'input-text';
        $style = isset($column['style']) ? sprintf('style="%s"', $column['style']) : '';
        $onClick = $this->getOnclick() ? sprintf('onclick="%s"', $this->getOnclick()) : '';

        return sprintf(
            '<input type="checkbox" id="%s" name="%s" value="1" <%%- %s %%> %s class="%s" %s %s/>',
            $inputId,
            $inputName,
            $columnName,
            $size,
            $class,
            $style,
            $onClick
        );
    }
}
