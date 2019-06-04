<?php
declare(strict_types = 1);
namespace Cryozonic\StripeSubscriptions\Plugin\Order;

use Cryozonic\StripeSubscriptions\Model\Order\InitialFeeManagement;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class SaveInitialFee
{
    /**
     * @var InitialFeeManagement
     */
    private $initialFeeManagement;

    public function __construct(InitialFeeManagement $initialFeeManagement)
    {
        $this->initialFeeManagement = $initialFeeManagement;
    }

    public function beforeSave(OrderRepositoryInterface $subject, Order $order): array
    {
        return [$this->initialFeeManagement->setDataFrom($order)];
    }
}
