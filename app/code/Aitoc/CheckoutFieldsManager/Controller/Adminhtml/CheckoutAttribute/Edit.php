<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

use Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

class Edit extends CheckoutAttribute
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        /** @var $model \Aitoc\CheckoutFieldsManager\Model\Entity\Attribute */
        $model = $this->checkoutEavFactory
            ->create()
            ->setEntityTypeId($this->entityTypeId);
        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addError(__('This attribute no longer exists.'));
                return $this->_redirect('aitoccheckoutfieldsmanager/*/');
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->entityTypeId) {
                $this->messageManager->addError(__('This attribute cannot be edited.'));
                return $this->_redirect('aitoccheckoutfieldsmanager/*/new');
            }
        }

        // set entered data if was error when we do save
        $data = $this->_session->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('attribute');
        if (!empty($attributeData) && $id === null) {
            $model->addData($attributeData);
        }

        $this->coreRegistry->register('entity_attribute', $model);

        $item = $id ? __('Edit Checkout Attribute') : __('New Checkout Attribute');

        $resultPage = $this->createActionPage($item);
        $resultPage->getConfig()->getTitle()->prepend($id ? $model->getName() : __('New Checkout Attribute'));

        return $resultPage;
    }
}
