<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassShow extends Action
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Context $context,
        Filter $filter
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('ids')) {
            $ids = $this->getRequest()->getParam('ids');
            $attributeCollectionCount = $this->collectionFactory->create()->addFieldToFilter('additional_table.attribute_id', ['in' => $ids])->count();
            $attributeCollection = $this->collectionFactory->create()->addFieldToFilter('additional_table.attribute_id', ['in' => $ids])->getItems();
            foreach ($attributeCollection as $attribute) {
                $attribute->setIsVisible(1);
                $attribute->save();
            }

            $this->messageManager->addSuccessMessage(__('A total of %1 element(s) have been show.', $attributeCollectionCount));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('aitoccheckoutfieldsmanager/*/index');

        return $resultRedirect;
    }
}
