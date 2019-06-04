<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\RequestForQuote\Model\Config\Source;

use Magento\Customer\Api\GroupManagementInterface;

/**
 * Customer group attribute source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class QuoteStatus implements \Magento\Framework\Option\ArrayInterface
{

    protected $_customerGroup;

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

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,        
        array $data = []
        ) {
        $this->_customerGroup = $customerGroup;
    } 
    
    public function toOptionArray() {
        return [
            ['value' => self::STATE_OPEN, 'label' => __('Open')], 
            ['value' => self::STATE_OUT_OF_STOCK, 'label' => __('Out Of Stock')],
            ['value' => self::STATE_OUT_OF_STOCK_HOLDED, 'label' => __('On Holded - Out Of Stock')], 
            ['value' => self::STATE_HOLDED, 'label' => __('On Holded')],
            ['value' => self::STATE_WAITING_SUPPLIER, 'label' => __('Waiting Supplier')], 
            ['value' => self::STATE_CHANGE_REQUEST, 'label' => __('Open - Change Request')],
            ['value' => self::STATE_EMAIL_SENT, 'label' => __('Pending - Email Sent')], 
            ['value' => self::STATE_PENDING, 'label' => __('Pending')],
            ['value' => self::STATE_ORDERED, 'label' => __('Ordered')], 
            ['value' => self::STATE_CANCELED, 'label' => __('Cancelled')],
            ['value' => self::STATE_REVIEWED, 'label' => __('Reviewed')], 
            ['value' => self::STATE_EXPIRED, 'label' => __('Expired')] 
        ]; 
    }
}
