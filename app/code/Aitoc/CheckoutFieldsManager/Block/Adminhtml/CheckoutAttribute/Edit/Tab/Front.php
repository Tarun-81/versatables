<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\Checkoutattribute\Edit\Tab;

use Aitoc\CheckoutFieldsManager\Model\Entity\Attribute;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Front extends Generic
{
    /**
     * @var Yesno
     */
    protected $yesNo;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        array $data = []
    ) {
        $this->yesNo = $yesNo;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var Attribute $attributeObject */
        $attributeObject = $this->_coreRegistry->registry('entity_attribute');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset(
            'front_fieldset',
            ['legend' => __('Storefront Properties')]
        );

        $fieldset->addField(
            'is_visible',
            'select',
            [
                'name' => 'is_visible',
                'label' => __('Visible on Checkout Page'),
                'title' => __('Visible on Checkout Page'),
                'value' => 1,
                'values' => $this->yesNo->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'display_area',
            'select',
            [
                'name' => 'display_area',
                'label' => __('Display Area'),
                'title' => __('Display Area'),
                'values' => $this->getDisplayArea(),
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Order'),
                'title' => __('Order'),
                'value' => 0
            ]
        );

        $this->setForm($form);
        if ($attributeObject->getId()) {
            $form->setValues($attributeObject->getData());
        }

        return parent::_prepareForm();
    }

    /**
     * @return array
     */
    protected function getDisplayArea()
    {
        $displayArea = ['' => ' '];

        $configPathArray = $this->_scopeConfig->getValue('checkoutfieldsmanager/checkout_field_path');
        if (is_array($configPathArray)) {
            /** full path contained in config.xml */
            foreach ($configPathArray as $key => $path) {
                /** generate Label from config key */
                $displayArea[$key] = __(ucwords(str_replace('_', ' ', $key)));
            }
        }

        return $displayArea;
    }
}
