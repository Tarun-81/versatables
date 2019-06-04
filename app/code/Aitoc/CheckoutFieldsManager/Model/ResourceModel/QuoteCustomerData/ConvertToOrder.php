<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\ResourceModel\QuoteCustomerData;

use Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface;
use Aitoc\CheckoutFieldsManager\Api\Data\QuoteCustomerDataInterface;
use Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData as OrderCustomerDataResource;
use Aitoc\CheckoutFieldsManager\Model\ResourceModel\QuoteCustomerData as QuoteCustomerDataResource;

class ConvertToOrder
{
    /** @var QuoteCustomerDataResource */
    private $quoteDataResource;

    /** @var OrderCustomerDataResource */
    private $orderDataResource;

    /**
     * @param QuoteCustomerDataResource $quoteDataResource
     * @param OrderCustomerDataResource $orderDataResource
     */
    public function __construct(
        QuoteCustomerDataResource $quoteDataResource,
        OrderCustomerDataResource $orderDataResource
    ) {
        $this->quoteDataResource = $quoteDataResource;
        $this->orderDataResource = $orderDataResource;
    }

    /**
     * Convert CFM attribute values of quote to order
     *
     * @param int $quoteId
     * @param int $orderId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function convert($quoteId, $orderId)
    {
        if (!$quoteId || !$orderId) {
            return;
        }
        $quoteTableName = $this->quoteDataResource->getMainTable();
        $orderTableName = $this->orderDataResource->getMainTable();
        $connection = $this->quoteDataResource->getConnection();

        /**
         * prepare select for multi insert from select
         */
        $quoteSelect = $connection->select()
            ->from(
                ['quote' => $quoteTableName],
                [
                    OrderCustomerDataInterface::KEY_ORDER_ID => new \Zend_Db_Expr($orderId),
                    QuoteCustomerDataInterface::KEY_ATTRIBUTE_ID,
                    QuoteCustomerDataInterface::KEY_VALUE
                ]
            )
            ->where('quote.' . QuoteCustomerDataInterface::KEY_QUOTE_ID . ' = ?', $quoteId);

        $insertQuery = $connection->insertFromSelect(
            $quoteSelect,
            $orderTableName,
            [
                OrderCustomerDataInterface::KEY_ORDER_ID,
                OrderCustomerDataInterface::KEY_ATTRIBUTE_ID,
                OrderCustomerDataInterface::KEY_VALUE
            ]
        );

        $connection->query($insertQuery);
    }
}
