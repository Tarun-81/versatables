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

namespace Cart2Quote\Quotation\Model;

/**
 * Quotation emails sending observer.
 * Performs handling of cron jobs related to sending emails to customers
 * after creation/modification of Order, Invoice, Shipment or Creditmemo.
 */
class EmailSenderHandler
{
    /**
     * Email sender model.
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\Sender
     */
    protected $emailSender;

    /**
     * Entity resource model.
     * @var \Magento\Sales\Model\ResourceModel\EntityAbstract
     */
    protected $entityResource;

    /**
     * Entity collection model.
     * @var \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
     */
    protected $entityCollection;

    /**
     * Global configuration storage.
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $globalConfig;

    /**
     * EmailSenderHandler constructor.
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\Sender $emailSender
     * @param ResourceModel\Quote $entityResource
     * @param ResourceModel\Quote\Collection|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $entityCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\Sender $emailSender,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote $entityResource,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $entityCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
    ) {
        $this->emailSender = $emailSender;
        $this->entityResource = $entityResource;
        $this->entityCollection = $entityCollection;
        $this->globalConfig = $globalConfig;
    }

    /**
     * Handles asynchronous email sending
     * @return void
     */
    public function sendEmails()
    {
        if ($this->globalConfig->getValue('sales_email/general/async_sending')) {
            $this->entityCollection->addFieldToFilter($this->emailSender->getSendEmailIdentifier(), ['eq' => 1]);
            $this->entityCollection->addFieldToFilter($this->emailSender->getEmailSentIdentifier(), ['null' => true]);

            /** @var \Magento\Sales\Model\AbstractModel $item */
            foreach ($this->entityCollection->getItems() as $item) {
                $this->emailSender->send($item, true);
            }
        }
    }
}
