<?php
declare(strict_types = 1);
namespace Cryozonic\StripeSubscriptions\Plugin\Order;

use Cryozonic\StripeSubscriptions\Model\Order\InitialFeeManagement;
use Magento\Sales\Model\Order;

class LoadInitialFee
{
    /**
     * @var InitialFeeManagement
     */
    private $extensionManagement;

    public function __construct(InitialFeeManagement $initialFeeManagement)
    {
        $this->initialFeeManagement = $initialFeeManagement;
    }

    public function afterLoad(Order $subject, Order $returnedOrder): Order
    {
        return $this->initialFeeManagement->setFromData($returnedOrder);
    }
}
