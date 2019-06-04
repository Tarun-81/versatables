<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Section\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use MageWorx\Downloads\Model\Section\Source\IsActive as IsActiveOptions;

class Main extends GenericForm implements TabInterface
{
    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var IsActiveOptions
     */
    protected $isActiveOptions;

    /**
     *
     * @param IsActiveOptions $isActiveOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        IsActiveOptions $isActiveOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->isActiveOptions       = $isActiveOptions;
        $this->registry              = $registry;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\Downloads\Model\Section $section */
        $section = $this->getSection();
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('section_');
        $form->setFieldNameSuffix('section');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => $this->getLegendText(),
                'class'  => 'fieldset-wide'
            ]
        );

        if ($section->getId()) {
            $fieldset->addField(
                'section_id',
                'hidden',
                ['name' => 'section_id']
            );
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'      => 'name',
                'label'     => __('Name'),
                'title'     => __('Name'),
                'required'  => true,
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name'      => 'description',
                'label'     => __('Description'),
                'title'     => __('Description'),
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name'      => 'is_active',
                'label'     => __('Is Active'),
                'title'     => __('Is Active'),
                'required'  => true,
                'options'   => $this->isActiveOptions->toArray()
            ]
        );

        $sectionData = $this->_session->getData('mageworx_downloads_section_data', true);

        if ($sectionData) {
            $section->addData($sectionData);
        } else {
            if (!$section->getId()) {
                $section->addData($section->getDefaultValues());
            }
        }

        $form->addValues($section->getData());

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Section Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     *
     * @return \MageWorx\Downloads\Model\Section
     */
    protected function getSection()
    {
        return $this->registry->registry('mageworx_downloads_section');
    }
}
