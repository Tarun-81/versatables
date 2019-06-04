<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Plugin\Sales\Controller\Adminhtml\Order\Create;

use Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection;
use Magento\Framework\Registry;
use Magento\Sales\Controller\Adminhtml\Order\Create\Save as MagentoSalesSave;

/**
 * Plugin for @see MagentoSalesSave
 */
class Save
{
    const URL_REDIRECT_CREATE = 'aitoccheckoutfieldsmanager/customerdatacheckoutattribute/create';
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param Registry $registry
     * @param Collection $collection
     */
    public function __construct(
        Registry $registry,
        Collection $collection
    ) {
        $this->registry = $registry;
        $this->collection = $collection;
    }

    /**
     * After create order from admin, redirect to "Additional Fields"
     * for add Custom Fields to Order
     *
     * @param MagentoSalesSave $object
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function afterExecute(MagentoSalesSave $object, $resultRedirect)
    {
        if ($this->registry->registry('current_order')) {
            $orderId = $this->registry->registry('current_order')->getId();
            $attributesArray = $this->collection->getAitocCheckoutfieldsByOrderId((int)$orderId, true);
            if (!empty($attributesArray)) {
                $resultRedirect->setPath(self::URL_REDIRECT_CREATE, ['orderid' => $orderId]);
            }
        }

        return $resultRedirect;
    }
}
