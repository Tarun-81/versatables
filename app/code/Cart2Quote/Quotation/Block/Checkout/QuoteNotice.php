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

namespace Cart2Quote\Quotation\Block\Checkout;

use Magento\Framework\View\Element\Template;

/**
 * Class QuoteNotice
 * @package Cart2Quote\Quotation\Block\Checkout
 */
class QuoteNotice extends \Magento\Framework\View\Element\Template
{

    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quotationSession;

    /**
     * QuoteNotice constructor.
     * @param Template\Context $context
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        array $data = []
    ) {
        $this->quotationSession = $quotationSession;
        parent::__construct($context, $data);
    }

    /**
     * Get Quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->quotationSession->getQuote();
    }
}
