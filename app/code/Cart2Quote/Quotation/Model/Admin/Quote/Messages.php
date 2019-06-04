<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Model\Admin\Quote;

/**
 * Class Messages
 * @package Cart2Quote\Quotation\Model\Admin\Quote
 */
class Messages implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    private $adminSessionInfoCollection;

    /**
     * Messages constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $adminSessionInfoCollection
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $adminSessionInfoCollection,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->authSession = $authSession;
        $this->backendUrl = $backendUrl;
        $this->adminSessionInfoCollection = $adminSessionInfoCollection;
    }

    /**
     * Determine the unique message identity in order to enhance message behavior
     *
     * @return string
     */
    public function getIdentity()
    {
        return md5('CART2QUOTE_NOTIFICATION' . $this->authSession->getUser()->getLogdate());
    }

    /**
     * Determine whether to show the message or not
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->getNewRequestCount() > 0 || $this->getNewRequestSinceLoginCount() > 0;
    }

    /**
     * Get quote request count with the state "open"
     *
     * @return int
     */
    public function getNewRequestCount()
    {
        $quoteCollection = $this->getNewQuoteCollection();

        return $quoteCollection->getSize();
    }

    /**
     * Get quote request count with the state "open" and creation date after last login
     *
     * @return int
     */
    public function getNewRequestSinceLoginCount()
    {
        $quoteCollection = $this->getNewQuoteCollection();
        $loginDate = $this->authSession->getUser()->getLogdate();
        $adminLogin = $this->getPreviousLogin();
        $lastLoginDate = $adminLogin->getQuotationCreatedAt();
        if (empty($lastLoginDate)) {
            $lastLoginDate = $loginDate;
        }

        $quoteCollection->addFieldToFilter(
            \Magento\Quote\Model\Quote::KEY_UPDATED_AT,
            ['gt' => $lastLoginDate]
        );
        $quoteCollection->addFieldToFilter(
            \Magento\Quote\Model\Quote::KEY_UPDATED_AT,
            ['lt' => $loginDate]
        );

        return $quoteCollection->getSize();
    }

    /**
     * Generate the text to be shown in the message
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getText()
    {
        $message = __('Cart2Quote notice:');
        $url = $this->backendUrl->getUrl('quotation/quote');
        $newRequestCount = $this->getNewRequestCount();
        if ($newRequestCount == 1) {
            $message .= '<br/>';
            $message .= __('You have <a href="%1">1 unanswered quote</a> request.', $url);
        } else {
            if ($newRequestCount > 1) {
                $message .= '<br/>';
                $message .= __('You have <a href="%1">%2 unanswered quote</a> requests.', $url, $newRequestCount);
            }
        }
        $newRequestSinceLogin = $this->getNewRequestSinceLoginCount();
        if ($newRequestSinceLogin == 1) {
            $message .= '<br/>';
            $message .= __('There is <a href="%1">1 new quote request</a> since your last login.', $url);
        } else {
            if ($newRequestSinceLogin > 1) {
                $message .= '<br/>';
                $message .= __(
                    'There are <a href="%1">%2 new quote requests</a> since your last login.',
                    $url,
                    $newRequestSinceLogin
                );
            }
        }

        return $message;
    }

    /**
     * Get the severity value of the message
     *
     * @return int
     */
    public function getSeverity()
    {
        return \Magento\Framework\Notification\MessageInterface::SEVERITY_CRITICAL;
    }

    /**
     * Get previous admin login model
     *
     * @return \Magento\Security\Model\AdminSessionInfo
     */
    public function getPreviousLogin()
    {
        $statuses = [
            \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT,
            \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT_BY_LOGIN,
            \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT_MANUALLY
        ];
        $adminCollection = $this->adminSessionInfoCollection;
        $adminCollection->addFieldToFilter('user_id', $this->authSession->getUser()->getId());
        $adminCollection->addFieldToFilter('status', ['in' => $statuses]);

        $this->adminSessionInfoCollection->setOrder('created_at', 'DESC');

        return $adminCollection->getFirstItem();
    }

    /**
     * Get new quotes collection
     *
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
     */
    public function getNewQuoteCollection()
    {
        /**
         * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $collection
         */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteInterface::STATE,
            \Cart2Quote\Quotation\Model\Quote\Status::STATE_OPEN
        );
        $collection->addFieldToFilter('is_quote', ['eq' => 1]);

        return $collection;
    }
}
