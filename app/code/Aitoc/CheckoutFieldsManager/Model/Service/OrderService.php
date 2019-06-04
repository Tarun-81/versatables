<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Service;

use Aitoc\CheckoutFieldsManager\Api\OrderCustomerDataRepositoryInterface;
use Aitoc\CheckoutFieldsManager\Api\OrderCustomFieldsInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class OrderService.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderService implements OrderCustomFieldsInterface
{
    /**
     * @var OrderCustomerDataRepositoryInterface
     */
    protected $repository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * OrderService constructor.
     *
     * @param OrderCustomerDataRepositoryInterface $repository
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        OrderCustomerDataRepositoryInterface $repository,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        ManagerInterface $eventManager
    ) {
        $this->repository = $repository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->eventManager = $eventManager;
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function getList($id)
    {
        $this->criteriaBuilder->addFilters(
            [$this->filterBuilder->setField('order_id')->setValue($id)->setConditionType('eq')->create()]
        );
        $searchCriteria = $this->criteriaBuilder->create();
        $result = $this->repository->getList($searchCriteria);

        return $result;
    }
}
