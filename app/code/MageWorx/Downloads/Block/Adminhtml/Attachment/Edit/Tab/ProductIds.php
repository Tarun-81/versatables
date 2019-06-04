<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Attachment\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

class ProductIds extends GenericForm implements TabInterface
{
    /**
     * @var  \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->coreRegistry          = $registry;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\Downloads\Model\Attachment $attachment */
        $attachment = $this->getAttachment();

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('attachment_');
        $form->setFieldNameSuffix('attachment');

        $fieldset = $form->addFieldset(
            'productids_fieldset',
            [
                'legend' => $this->getLegendText(),
                'class'  => 'fieldset-wide'
            ]
        );

        $fieldset->addField(
            'productids',
            'textarea',
            [
                'name'      => 'productids',
                'label'     => __('Product IDs'),
                'title'     => __('Product IDs'),
                'note'      => __('To enter a new product ID, either start a new line'
                    . ' or separate it from the previous one with a coma.')
            ]
        );

        $attachmentData = $this->_session->getData('mageworx_downloads_attachment_data', true);
        if ($attachmentData) {
            $attachment->addData($attachmentData);
        } else {
            if (!$attachment->getId()) {
                $attachment->addData($attachment->getDefaultValues());
            }
        }

        $form->addValues($attachment->getData());
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
        return __('Product IDs');
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
        return true;
    }

    /**
     *
     * @return /MageWorx\Downloads\Model\Attachment
     */
    public function getAttachment()
    {
        return $this->coreRegistry->registry('mageworx_downloads_attachment');
    }
}
