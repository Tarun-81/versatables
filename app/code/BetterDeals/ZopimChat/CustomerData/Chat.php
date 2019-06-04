<?php

namespace BetterDeals\ZopimChat\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class Chat implements SectionSourceInterface
{
    /**
     * Customer session
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Order Collection
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $orderCollection;

    /**
     * Helper
     * @var \BetterDeals\ZopimChat\Helper\Chat
     */
    protected $helper;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\ResourceModel\Order\Collection $orderCollection,
        \BetterDeals\ZopimChat\Helper\Chat $helper
    ) {
        $this->customerSession = $customerSession;
        $this->orderCollection = $orderCollection;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        if(!$this->helper->useCustomerData()) {
            return [];
        }

        $data = [
            'is_logged_in' => $this->customerSession->isLoggedIn(),
        ];

        if($this->customerSession->isLoggedIn()) {

            $customer = $this->getCustomer();

            if($this->helper->useCustomerName()) {
                $data['name'] = $customer->getName();
            }

            if($this->helper->useCustomerEmail()) {
                $data['email'] = $customer->getEmail();
            }

            if($this->helper->useCustomerTelephone()) {
                $address = $customer->getDefaultBillingAddress();

                if($address && $address->getTelephone()) {
                    $data['telephone'] = $address->getTelephone();
                }
            }

            if($this->helper->useCustomerOrders()) {
                $this->orderCollection->addFieldToFilter('customer_id', $customer->getId())
                    ->getSelect()
                    ->reset(\Zend_Db_Select::COLUMNS)
                    ->columns('increment_id');

                $data['orders'] = [];

                foreach($this->orderCollection as $order) {
                    $data['orders'][] = $order->getData('increment_id');
                }
            }
        }

        return $data;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }
}
