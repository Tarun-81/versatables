<?php
declare(strict_types = 1);
namespace Cryozonic\StripeSubscriptions\Plugin\Order;

use Cryozonic\StripeSubscriptions\Model\Order\InitialFeeManagement;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

class LoadInitialFeeOnCollection
{
    /**
     * @var InitialFeeManagement
     */
    private $extensionManagement;

    public function __construct(InitialFeeManagement $initialFeeManagement)
    {
        $this->initialFeeManagement = $initialFeeManagement;
    }

    public function afterGetItems(OrderCollection $subject, array $orders): array
    {
        return array_map(function (Order $order) {
            return $this->initialFeeManagement->setFromData($order);
        }, $orders);
    }
}
