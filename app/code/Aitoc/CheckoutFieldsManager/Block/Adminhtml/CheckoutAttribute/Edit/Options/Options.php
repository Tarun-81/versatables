<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\CheckoutAttribute\Edit\Options;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\DataObject;
use Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options as BaseOptions;

class Options extends BaseOptions
{
    protected $_template = 'Aitoc_CheckoutFieldsManager::catalog/product/attribute/options.phtml';

    const OPTION_SELECT = 'select';
    const OPTION_MULTISELECT = 'multiselect';
    const OPTION_CHECKBOX = 'checkbox';
    const OPTION_RADIOBUTTON = 'radiobutton';

    protected $attributeTypes =
        [
            self::OPTION_SELECT,
            self::OPTION_MULTISELECT,
            self::OPTION_CHECKBOX,
            self::OPTION_RADIOBUTTON
        ];

    /**
     * @param AbstractAttribute $attribute
     * @param array|\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection
     *
     * @return array
     */
    protected function _prepareOptionValues(
        AbstractAttribute $attribute,
        $optionCollection
    ) {
        $type = $attribute->getFrontendInput();
        if (in_array($type, $this->attributeTypes)) {
            $defaultValues = explode(',', $attribute->getDefaultValue());
            $inputType = 'checkbox';
            if ($type === self::OPTION_SELECT || $type === self::OPTION_RADIOBUTTON) {
                $inputType = 'radio';
            }
        } else {
            $defaultValues = [];
            $inputType = '';
        }
        $values = [];
        $isSystemAttribute = is_array($optionCollection);
        foreach ($optionCollection as $option) {
            $bunch = $isSystemAttribute
                ? $this->_prepareSystemAttributeOptionValues(
                    $option,
                    $inputType,
                    $defaultValues
                )
                : $this->_prepareUserDefinedAttributeOptionValues(
                    $option,
                    $inputType,
                    $defaultValues
                );
            foreach ($bunch as $value) {
                $values[] = new DataObject($value);
            }
        }

        return $values;
    }
}
