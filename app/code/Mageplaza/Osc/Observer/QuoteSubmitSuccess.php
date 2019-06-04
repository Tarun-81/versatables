<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Osc\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class QuoteSubmitSuccess
 * @package Mageplaza\Osc\Observer
 */
class QuoteSubmitSuccess implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @type \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @type \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @type \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @type \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @type \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
    ) {
    
        $this->checkoutSession   = $checkoutSession;
        $this->accountManagement = $accountManagement;
        $this->_customerUrl      = $customerUrl;
        $this->messageManager    = $messageManager;
        $this->_customerSession  = $customerSession;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @type \Magento\Quote\Model\Quote $quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $oscData = $this->checkoutSession->getOscData();
        if (isset($oscData['register']) && $oscData['register']
            && isset($oscData['password']) && $oscData['password']
        ) {
            $customer           = $quote->getCustomer();

            /* Set customer Id for address */
            if($customer->getId()) {
                $quote->getBillingAddress()->setCustomerId($customer->getId());
                $quote->getBillingAddress()->setCustomerId($customer->getId());
            }

            /* Check customer to be login or not */
            $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
            if ($confirmationStatus === \Magento\Customer\Model\AccountManagement::ACCOUNT_CONFIRMATION_REQUIRED) {
                $url = $this->_customerUrl->getEmailConfirmationUrl($customer->getEmail());
                $this->messageManager->addSuccessMessage(
				// @codingStandardsIgnoreStart
					__(
						'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.',
						$url
					)
				// @codingStandardsIgnoreEnd
                );
            } else {
                $this->_customerSession->loginById($customer->getId());
                $this->_customerSession->regenerateId();
                if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                    $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                    $metadata->setPath('/');
                    $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                }
            }
        }

        if (isset($oscData['is_subscribed']) && $oscData['is_subscribed']) {
            if (!$this->_customerSession->isLoggedIn()) {
                $subscribedEmail = $quote->getBillingAddress()->getEmail();
            } else {
                $customer        = $this->_customerSession->getCustomer();
                $subscribedEmail = $customer->getEmail();
            }

            try {
                $this->subscriberFactory->create()
                    ->subscribe($subscribedEmail);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There is an error while subscribing for newsletter.'));
            }
        }

        $this->checkoutSession->unsOscData();
    }

    /**
     * Retrieve cookie manager
     *
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
        );
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
        );
    }
}
