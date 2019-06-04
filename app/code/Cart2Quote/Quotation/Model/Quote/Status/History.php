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

namespace Cart2Quote\Quotation\Model\Quote\Status;

/**
 * Quote status history comments
 * @method \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History _getResource()
 * @method \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History getResource()
 */
class History extends \Magento\Sales\Model\AbstractModel implements \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface
{
    const CUSTOMER_NOTIFICATION_NOT_APPLICABLE = 2;

    /**
     * Quote instance
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $_quote;

    /**
     * @var string
     */
    protected $_eventPrefix = 'quotation_quote_status_history';

    /**
     * @var string
     */
    protected $_eventObject = 'status_history';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_storeManager = $storeManager;
    }

    /**
     * Notification flag
     * @param  mixed $flag OPTIONAL (notification is not applicable by default)
     * @return $this
     */
    public function setIsCustomerNotified($flag = null)
    {
        if ($flag === null) {
            $flag = self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
        }

        return $this->setData('is_customer_notified', $flag);
    }

    /**
     * Customer Notification Applicable check method
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable()
    {
        return $this->getIsCustomerNotified() == self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }

    /**
     * Returns is_customer_notified
     * @return int
     */
    public function getIsCustomerNotified()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::IS_CUSTOMER_NOTIFIED);
    }

    /**
     * Retrieve status label
     * @return string|null
     */
    public function getStatusLabel()
    {
        if ($this->getQuote()) {
            return $this->getQuote()->getConfig()->getStatusLabel($this->getStatus());
        }

        return null;
    }

    /**
     * Retrieve quote instance
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Set quote object and grab some metadata from it
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return $this
     */
    public function setQuote(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $this->_quote = $quote;
        $this->setStoreId($quote->getStoreId());

        return $this;
    }

    /**
     * Returns status
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::STATUS);
    }

    /**
     * Get store object
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->getQuote()) {
            return $this->getQuote()->getStore();
        }

        return $this->_storeManager->getStore();
    }

    /**
     * Set quote again if required
     * @return $this
     */
    public function beforeSave()
    {
        parent::beforeSave();

        if (!$this->getParentId() && $this->getQuote()) {
            $this->setParentId($this->getQuote()->getId());
        }

        return $this;
    }

    /**
     * Returns parent_id
     * @return int
     */
    public function getParentId()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::PARENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($id)
    {
        return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::PARENT_ID, $id);
    }

    /**
     * Returns comment
     * @return string
     */
    public function getComment()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::COMMENT);
    }

    /**
     * Returns created_at
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::CREATED_AT, $createdAt);
    }

    /**
     * Returns entity_id
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::ENTITY_ID);
    }

    /**
     * Returns entity_name
     * @return string
     */
    public function getEntityName()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::ENTITY_NAME);
    }

    /**
     * Returns is_visible_on_front
     * @return int
     */
    public function getIsVisibleOnFront()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::IS_VISIBLE_ON_FRONT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVisibleOnFront($isVisibleOnFront)
    {
        return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::IS_VISIBLE_ON_FRONT,
            $isVisibleOnFront);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::COMMENT, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityName($entityName)
    {
        return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::ENTITY_NAME, $entityName);
    }

    /**
     * {@inheritdoc}
     * @return \Magento\Sales\Api\Data\OrderStatusHistoryExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     * @param \Magento\Sales\Api\Data\OrderStatusHistoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magento\Sales\Api\Data\OrderStatusHistoryExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Initialize resourcemodel
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History');
    }
}
