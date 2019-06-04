<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\OrderCustomerData;

use Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterfacePersistor;
use Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataSearchResultInterfaceFactory;
use Aitoc\CheckoutFieldsManager\Api\OrderCustomerDataRepositoryInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria;

/**
 * Repository class for @see
 * \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface.
 */
class Repository implements OrderCustomerDataRepositoryInterface
{
    /**
     * orderCustomerDataInterfacePersistor.
     *
     * @var OrderCustomerDataInterfacePersistor
     */
    protected $orderCustomerDataInterfacePersistor = null;

    /**
     * Collection Factory.
     *
     * @var \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataSearchResultInterfaceFactory
     */
    protected $orderCustomerDataInterfaceSearchResultFactory = null;

    /**
     * List Custom Fields.
     *
     * @var array
     */
    protected $registry = [];

    /**
     * Extension attributes join processor.
     *
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor = null;

    /**
     * Repository constructor.
     *
     * @param OrderCustomerDataInterfacePersistor $orderCustomerDataInterfacePersistor
     * @param OrderCustomerDataSearchResultInterfaceFactory $orderCustomerDataInterfaceSearchResultFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        OrderCustomerDataInterfacePersistor $orderCustomerDataInterfacePersistor,
        OrderCustomerDataSearchResultInterfaceFactory $orderCustomerDataInterfaceSearchResultFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->orderCustomerDataInterfacePersistor = $orderCustomerDataInterfacePersistor;
        $this->orderCustomerDataInterfaceSearchResultFactory = $orderCustomerDataInterfaceSearchResultFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * Find entities by criteria.
     *
     * @param SearchCriteria $searchCriteria
     *
     * @return \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface[]
     */
    public function getList(SearchCriteria $searchCriteria)
    {
        $collection = $this->orderCustomerDataInterfaceSearchResultFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        return $collection;
    }
}
