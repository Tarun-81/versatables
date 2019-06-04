<?php
declare(strict_types = 1);
namespace Cryozonic\StripeSubscriptions\Plugin\Quote;

use Cryozonic\StripeSubscriptions\Model\Order\InitialFeeManagement;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Model\Quote\Address\ToOrder as QuoteAddressToOrder;
use Magento\Quote\Model\Quote\Address as QuoteAddress;

class InitialFeeToOrder
{
    /**
     * @var InitialFeeManagement
     */
    private $extensionManagement;

    public function __construct(InitialFeeManagement $extensionManagement)
    {
        $this->extensionManagement = $extensionManagement;
    }

    public function aroundConvert(
        QuoteAddressToOrder $subject,
        \Closure $proceed,
        QuoteAddress $quoteAddress,
        array $data = []
    ): OrderInterface {
        return $this->extensionManagement->setFromAddressData($proceed($quoteAddress, $data), $quoteAddress);
    }
}
