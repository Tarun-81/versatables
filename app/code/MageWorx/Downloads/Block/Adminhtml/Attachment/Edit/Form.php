<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Attachment\Edit;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;

class Form extends GenericForm
{
    protected $_template = 'MageWorx_Downloads::widget/form.phtml';

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        if (false !== strpos($_SERVER['REQUEST_URI'], '/create/')) {
            $this->setData('action', $this->getUrl('*/*/saveBrandNewAttachment'));
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $this->setChild(
            'form_after_switcher',
            $this->getLayout()->createBlock('MageWorx\Downloads\Block\Adminhtml\Attachment\Edit\Switcher')
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
