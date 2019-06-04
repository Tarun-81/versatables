<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Entity;

class Attribute extends \Magento\Eav\Model\Entity\Attribute
{
    /**
     * Name of the module
     */
    const MODULE_NAME = 'Aitoc_CheckoutFieldsManager';

    const TYPE = 'aitoc_checkout';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\CheckoutFieldsManager\Model\ResourceModel\Entity\Attribute');
    }

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setCheckoutStep($data)
    {
        return $this->setData('checkout_step', $data);
    }

    /**
     * Get Checkout Step, contained in additional EAV table (aitoc_checkout_eav_attribute)
     *
     * @return string|null
     */
    public function getCheckoutStep()
    {
        return $this->_getData('checkout_step');
    }

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setDisplayArea($data)
    {
        return $this->setData('display_area', $data);
    }

    /**
     * Get Display Area, contained in additional EAV table (aitoc_checkout_eav_attribute)
     *
     * @return mixed
     */
    public function getDisplayArea()
    {
        return $this->_getData('display_area');
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getDefaultValueByInput($type)
    {
        $field = '';
        switch ($type) {
            case 'checkbox':
                $field = null;
                break;
            case 'radiobutton':
                break;
            case 'label':
                $field = 'default_value_textarea';
                break;
            default:
                $field = parent::getDefaultValueByInput($type);
                break;
        }

        return $field;
    }

    /**
     * @param string|int $value
     * @param Attribute|null $attribute
     *
     * @return string|int
     */
    public function processValue($value, $attribute = null)
    {
        if (!is_string($value) && !is_numeric($value)) {
            $value = '';
        }
        //TODO: Make required fields validation.

        return $value;
    }
}
