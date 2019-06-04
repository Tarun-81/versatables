<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Entity\Source;

class Boolean extends \Magento\Eav\Model\Entity\Attribute\Source\Boolean
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $options = parent::getAllOptions();
            array_unshift($options, ['label' => __('Please Select'), 'value' => '']);
            $this->_options = $options;
        }

        return $this->_options;
    }
}
