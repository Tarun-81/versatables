<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\CheckoutAttribute\Edit\Tab;

use Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain;
use Magento\Framework\DataObject;

class Main extends AbstractMain
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        /** @var $form \Magento\Framework\Data\Form */
        $form = $this->getForm();
        /** @var $fieldset \Magento\Framework\Data\Form\Element\Fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $fieldsToRemove = ['attribute_code', 'is_unique', 'frontend_class'];
        /** Note: If you adding additional types, don't forget to add this types to validator in Save Controller */
        $additionalTypes = [
            ['value' => 'checkbox', 'label' => __('Checkbox')],
            ['value' => 'radiobutton', 'label' => __('Radio Button')],
            ['value' => 'label', 'label' => __('Static text (Basic HTML)')]
        ];

        foreach ($fieldset->getElements() as $element) {
            /** @var \Magento\Framework\Data\Form\AbstractForm $element */
            if (substr($element->getId(), 0, strlen('default_value')) == 'default_value') {
                $fieldsToRemove[] = $element->getId();
            }
        }
        foreach ($fieldsToRemove as $id) {
            $fieldset->removeField($id);
        }

        $frontendInputElm = $form->getElement('frontend_input');
        $response = new DataObject();
        $response->setTypes([]);
        $this->_eventManager->dispatch('aitoc_checkout_attribute_types', ['response' => $response]);
        $hiddenFields = [];
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $hiddenFields[$type['value']] = $type['hide_fields'];
            }
        }
        $this->_coreRegistry->register('attribute_type_hidden_fields', $hiddenFields);

        $this->_eventManager->dispatch('aitoc_checkout_attribute_form_build_main_tab', ['form' => $form]);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues)
            ->setLabel(__('Input Type'))
            ->setTitle(__('Input Type'));

        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Properties');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}
