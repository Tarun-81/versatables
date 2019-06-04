<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Api;

use Magento\Framework\Api\SearchCriteria;

interface OrderCustomerDataRepositoryInterface
{
    /**
     * Lists order status history comments that match specified search criteria.
     *
     * @param SearchCriteria $searchCriteria The search criteria.
     *
     * @return \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataSearchResultInterface Order custom fields search results interface.
     */
    public function getList(SearchCriteria $searchCriteria);
}
