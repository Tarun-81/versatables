<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

use Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

class Delete extends CheckoutAttribute
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $model = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute');
            $model->load($id);
            try {
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the product attribute.'));
                return $resultRedirect->setPath('aitoccheckoutfieldsmanager/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath(
                    'aitoccheckoutfieldsmanager/*/edit',
                    ['attribute_id' => $this->getRequest()->getParam('attribute_id')]
                );
            }
        }
        $this->messageManager->addError(__('We can\'t find an attribute to delete.'));
        return $resultRedirect->setPath('aitoccheckoutfieldsmanager/*/');
    }
}
