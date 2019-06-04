<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderCustomerDataSearchResultInterface extends SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
