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

namespace Cart2Quote\Quotation\Observer\Quote;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CreateSequence
 */
class Run implements ObserverInterface
{
    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Run constructor.
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $quotationHelper
    ) {
        $this->quotationHelper = $quotationHelper;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $controller = $observer->getControllerAction();

        if (!$this->quotationHelper->isFrontendEnabled()) {
            $frontname = $observer->getRequest()->getFrontName();

            //make sure that we are not in the backend
            if ($frontname == "quotation") {
                $controller->getResponse()->setRedirect(
                    $controller->getUrl('')
                );
            }
        }

        return $this;
    }
}
