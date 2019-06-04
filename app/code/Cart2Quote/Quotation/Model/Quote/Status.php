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

namespace Cart2Quote\Quotation\Model\Quote;

/**
 * Class Status
 * @method string getStatus()
 * @method string getLabel()
 */
class Status extends \Magento\Sales\Model\Order\Status
{
    const STATE_OPEN = 'open';
    const STATE_HOLDED = 'holded';
    const STATE_CANCELED = 'canceled';
    const STATE_PENDING = 'pending';
    const STATE_COMPLETED = 'completed';
    const STATUS_OPEN = 'open';
    const STATUS_NEW = 'new';
    const STATUS_CHANGE_REQUEST = 'change_request';
    const STATUS_QUOTE_AVAILABLE = 'quote_available';
    const STATUS_HOLDED = 'holded';
    const STATUS_WAITING_SUPPLIER = 'waiting_supplier';
    const STATUS_CANCELED = 'canceled';
    const STATUS_OUT_OF_STOCK = 'out_of_stock';
    const STATUS_PROPOSAL_EXPIRED = 'proposal_expired';
    const STATUS_PENDING = 'pending';
    const STATUS_PROPOSAL_SENT = 'proposal_sent';
    const STATUS_AUTO_PROPOSAL_SENT = 'auto_proposal_sent';
    const STATUS_ORDERED = 'ordered';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_CLOSED = 'closed';
    const STATUS_PROCESSING = 'processing';
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
            $storeManager,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_storeManager = $storeManager;
    }

    /**
     * Unassigns quote status from particular state
     * @param string $state
     * @return $this
     * @throws \Exception
     */
    public function unassignState($state)
    {
        $this->validateBeforeUnassign($state);
        $this->getResource()->unassignState($this->getStatus(), $state);
        $this->_eventManager->dispatch(
            'quotation_quote_status_unassign',
            [
                'status' => $this->getStatus(),
                'state' => $state
            ]
        );

        return $this;
    }

    /**
     * @param string $state
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateBeforeUnassign($state)
    {
        if ($this->getResource()->checkIsStateLast($state)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The last status can\'t be unassigned from its current state.'));
        }
        if ($this->getResource()->checkIsStatusUsed($this->getStatus())) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Status can\'t be unassigned, because it is used by existing quote(s).'));
        }
    }

    /**
     * Get the flag to decide whether or not the button on the customer dashboard quote detail view should be enabled
     *
     * @return string
     */
    public function getFrontendButtonHtmlFlag()
    {
        $flag = 'disabled';
        if ($this->canAccept()) {
            $flag = '';
        }

        return $flag;
    }

    /**
     * Function to check whether the quote can be accepted based on its state and status
     *
     * @param null $state
     * @param null $status
     * @return bool
     */
    public function canAccept($state = null, $status = null)
    {
        if ($state == null) {
            $state = $this->getState();
        }
        if ($status == null) {
            $status = $this->getStatus();
        }

        return
            in_array($state, [
                self::STATE_PENDING,
                self::STATE_COMPLETED
            ]) &&
            in_array($status, [
                self::STATUS_PENDING,
                self::STATUS_QUOTE_AVAILABLE,
                self::STATUS_PROPOSAL_SENT,
                self::STATUS_AUTO_PROPOSAL_SENT,
                self::STATUS_ACCEPTED,
            ]);
    }

    /**
     * Function to check whether the quote can show prices based on its state and status
     *
     * @param null $state
     * @param null $status
     * @return bool
     */
    public function showPrices($state = null, $status = null)
    {
        if ($state == null) {
            $state = $this->getState();
        }
        if ($status == null) {
            $status = $this->getStatus();
        }

        return
            in_array($state, [
                self::STATE_PENDING,
                self::STATE_COMPLETED
            ]) &&
            in_array($status, [
                self::STATUS_PENDING,
                self::STATUS_QUOTE_AVAILABLE,
                self::STATUS_PROPOSAL_SENT,
                self::STATUS_AUTO_PROPOSAL_SENT,
                self::STATUS_ACCEPTED,
                self::STATUS_ORDERED,
            ]);
    }

    /**
     * Returns true if this->getStatus() is STATUS_ACCEPTED
     *
     * @return bool
     */
    public function statusIsAccepted()
    {
        return $this->getStatus() == self::STATUS_ACCEPTED;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Quotation\Model\ResourceModel\Quote\Status');
    }
}
