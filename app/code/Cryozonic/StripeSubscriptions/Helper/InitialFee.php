<?php

namespace Cryozonic\StripeSubscriptions\Helper;

use Cryozonic\StripePayments\Helper\Logger;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class InitialFee
{
    public $serializer = null;

    public function __construct(
        \Cryozonic\StripePayments\Helper\Generic $paymentsHelper
    ) {
        $this->paymentsHelper = $paymentsHelper;

        // A Magento 2.2 backwards incompatible class exists which is necessary for Magento 2.2
        if (class_exists('Magento\Framework\Serialize\Serializer\Json'))
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->serializer = $objectManager->get('Magento\Framework\Serialize\SerializerInterface');
        }
    }

    public function serialize($data)
    {

        if (!empty($this->serializer))
            return $this->serializer->serialize($data);

        return serialize($data);
    }

    public function getTotalInitialFeeForCreditmemo($creditmemo, $orderRate = true)
    {
        $items = $creditmemo->getAllItems();

        if ($orderRate)
            $rate = $creditmemo->getBaseToOrderRate();
        else
            $rate = 1;

        return $this->getInitialFeeForItems($items, $rate);
    }

    public function getTotalInitialFeeForInvoice($invoice, $invoiceRate = true)
    {
        $items = $invoice->getAllItems();

        if ($invoiceRate)
            $rate = $invoice->getBaseToOrderRate();
        else
            $rate = 1;

        return $this->getInitialFeeForItems($items, $rate);
    }

    public function getTotalInitialFeeForQuote($quote, $quoteRate = true)
    {
        $items = $quote->getAllItems();

        if ($quoteRate)
            $rate = $quote->getBaseToQuoteRate();
        else
            $rate = 1;

        return $this->getInitialFeeForItems($items, $rate);
    }

    public function getInitialFeeForItems($items, $rate)
    {
        $total = 0;

        foreach ($items as $item)
        {
            // We skip the children of configurable products which are duplicates
            if ($item->getParentItem())
                continue;

            // We include the configurable products children
            if ($item->getQtyOptions())
            {
                foreach ($item->getQtyOptions() as $id => $option)
                {
                    $total += $this->getInitialFeeForProductId($id, $rate, $item->getQty());
                }
            }
            else
                $total += $this->getInitialFeeForProductId($item->getProductId(), $rate, $item->getQty());
        }

        return $total;
    }

    public function getInitialFeeForProductId($productId, $rate, $qty)
    {
        $product = $this->paymentsHelper->loadProductById($productId);

        if (!is_numeric($product->getCryozonicSubInitialFee()))
            return 0;

        return $product->getCryozonicSubInitialFee() * $rate * $qty;
    }

    public function getAdditionalOptionsForChildrenOf($item)
    {
        $additionalOptions = array();

        foreach ($item->getQtyOptions() as $productId => $option)
        {
            $additionalOptions = array_merge($additionalOptions, $this->getAdditionalOptionsForProductId($productId, $item->getQty()));
        }

        return $additionalOptions;
    }

    public function getAdditionalOptionsForProductId($productId, $qty)
    {
        $profile = $this->getSubscriptionProfileForProductId($productId);
        if (!$profile)
            return array();

        $additionalOptions = array(
            array(
                'label' => 'Repeats Every',
                'value' => $profile['repeat_every']
            )
        );

        $quote = $this->paymentsHelper->getSessionQuote();

        if ($profile['initial_fee'])
        {
            $additionalOptions[] = array(
                'label' => 'Initial Fee',
                'value' => $this->paymentsHelper->formatPrice($profile['initial_fee'] * $qty)
            );
        }

        if ($profile['trial_days'])
        {
            $additionalOptions[] = array(
                'label' => 'Trial Period',
                'value' => $profile['trial_days'] . " days"
            );
        }

        return $additionalOptions;
    }

    public function getSubscriptionProfileForProductId($productId)
    {
        $product = $this->paymentsHelper->loadProductById($productId);

        if (!$product->getCryozonicSubEnabled())
            return null;

        $profile['initial_fee'] = $product->getCryozonicSubInitialFee();

        $intervalCount = $product->getCryozonicSubIntervalCount();
        $interval = ucfirst($product->getCryozonicSubInterval());
        $plural = ($intervalCount > 1 ? 's' : '');

        $profile['repeat_every'] = "$intervalCount $interval$plural";

        $trialDays = $product->getCryozonicSubTrial();
        if ($trialDays && is_numeric($trialDays) && $trialDays > 0)
            $profile['trial_days'] = round($trialDays);
        else
            $profile['trial_days'] = false;

        return $profile;
    }
}
