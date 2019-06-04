<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuote\Model;

class Quote extends \Magento\Framework\Model\AbstractModel
{
    CONST STATE_OPEN = 'open';
    CONST STATE_OUT_OF_STOCK = 'out_of_stock';
    CONST STATE_OUT_OF_STOCK_HOLDED = 'out_of_stock_holded';
    CONST STATE_HOLDED = 'holded';
    CONST STATE_WAITING_SUPPLIER = 'waiting_supplier';
    CONST STATE_CHANGE_REQUEST = 'change_request';
    CONST STATE_PENDING = 'pending';
    CONST STATE_ORDERED = 'ordered';
    CONST STATE_CANCELED = 'cancelled';
    CONST STATE_PROCESSING = 'processing';
    CONST STATE_EMAIL_SENT = 'email_sent';
    CONST STATE_REVIEWED = 'reviewed';
    CONST STATE_EXPIRED = 'expired';

    protected $_core_write_connection;
    protected $_core_resource;
    protected $_mage_quote = null;
    protected $_quoteCurrency = null;
    protected $_html_questions = null;

    protected $_storeManager;
    protected $_blockHelper;
    protected $_objectManager;
    protected $mageQuoteFactory;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lof\RequestForQuote\Model\ResourceModel\Quote $resource = null,
        \Lof\RequestForQuote\Model\ResourceModel\Quote\Collection $resourceCollection = null,
        \Magento\Framework\App\ResourceConnection $core_resource,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lof\RequestForQuote\Helper\Data $blockHelper,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Quote\Model\QuoteFactory $mageQuoteFactory,
        array $data = []
    )
    {
        $this->_storeManager = $storeManager;
        $this->_blockHelper = $blockHelper;
        $this->_core_resource = $core_resource;
        $this->_objectManager = $objectManager;
        $this->_currencyFactory = $currencyFactory;
        $this->mageQuoteFactory = $mageQuoteFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\RequestForQuote\Model\ResourceModel\Quote');
    }

    protected function getConnection()
    {
        if (!$this->_core_write_connection) {
            $this->_core_write_connection = $this->_core_resource->getConnection('core_write');
        }
        return $this->_core_write_connection;
    }

    public function getMageQuote($mage_quote_id = 0)
    {
        if (!$this->_mage_quote) {
            if ($quote_id = $this->getQuoteId()) {
                $mage_quote_id = $quote_id;
            }
            if ($mage_quote_id) {
                $this->_mage_quote = $this->mageQuoteFactory->create()->load($mage_quote_id);
            }
        }

        return $this->_mage_quote;
    }

    /**
     * Get Rule statues
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATE_OPEN => __('Open'),
            self::STATE_OUT_OF_STOCK => __('Out Of Stock'),
            self::STATE_OUT_OF_STOCK_HOLDED => __('On Holded - Out Of Stock'),
            self::STATE_HOLDED => __('On Holded'),
            self::STATE_WAITING_SUPPLIER => __('Waiting Supplier'),
            self::STATE_CHANGE_REQUEST => __('Open - Change Request'),
            self::STATE_EMAIL_SENT => __('Pending - Email Sent'),
            self::STATE_PENDING => __('Pending'),
            self::STATE_ORDERED => __('Ordered'),
            self::STATE_CANCELED => __('Cancelled'),
            self::STATE_REVIEWED => __('Reviewed'),
            self::STATE_EXPIRED => __('Expired')
        ];
    }

    public function getStatusLabel()
    {
        $status = $this->getData('status');
        $availableStatuses = $this->getAvailableStatuses();
        foreach ($availableStatuses as $k => $v) {
            if ($k == $status) {
                return $v;
            }
        }
    }

    public function loadByIncrementId($incrementId)
    {
        return $this->loadByAttribute('increment_id', $incrementId);
    }

    /**
     * Load order by custom attribute value. Attribute value should be unique
     *
     * @param string $attribute
     * @param string $value
     * @return $this
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }

    public function updateCustomerForQuote($quote_id, $mage_quote_id, $customer_info = [])
    {
        $connection = $this->getConnection();
        $table1 = $this->_core_resource->getTableName('lof_rfq_quote');
        $table2 = $this->_core_resource->getTableName('quote');

        $sql1 = 'UPDATE ' . $table1 . ' SET customer_id=' . (int)$customer_info['customer_id'] . '
            WHERE entity_id=' . (int)$quote_id;

        $result1 = $connection->query($sql1);

        $sql2 = 'UPDATE ' . $table2 . ' SET customer_id=' . (int)$customer_info['customer_id'] . ', customer_group_id=' . (int)$customer_info['customer_group_id'] . ', customer_is_guest=0 
            WHERE entity_id=' . (int)$mage_quote_id;

        $result2 = $connection->query($sql2);
    }

    /**
     * Get currency model instance. Will be used currency with which order placed
     *
     * @return Currency
     */
    public function getQuoteCurrency()
    {
        if ($this->_quoteCurrency === null) {
            $this->_quoteCurrency = $this->_currencyFactory->create();
            $currency_code = "";
            if ($this->getMageQuote()) {
                $currency_code = $this->getMageQuote()->getQuoteCurrencyCode();
                $this->_quoteCurrency->load($currency_code);
            } else {
                $this->_quoteCurrency = null;
            }

        }
        return $this->_quoteCurrency;
    }

    /**
     * Get formatted price value including order currency rate to order website currency
     *
     * @param   float $price
     * @param   bool $addBrackets
     * @return  string
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    /**
     * @param float $price
     * @param int $precision
     * @param bool $addBrackets
     * @return string
     */
    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        if ($this->getQuoteCurrency()) {
            return $this->getQuoteCurrency()->formatPrecision($price, $precision, [], true, $addBrackets);
        }

        return $this->_blockHelper->formatPriceWithCurency($price);
    }

    public function getQuoteQuestions()
    {
        if (!$this->_html_questions) {
            $questions = $this->getData("question");
            $html_questions = "";
            if ($questions) {
                $_quote_questions = unserialize($questions);
                $tmp = array();
                if ($_quote_questions) {
                    foreach ($_quote_questions as $question) {
                        $tmp[] = __("Q:") . $question['label'] . "<br/>" . __("A:") . $question['value'];
                    }
                    $html_questions = implode("<br/><hr/>", $tmp);
                    $this->_html_questions = $html_questions;
                }
            }
        }
        return $this->_html_questions;
    }
}