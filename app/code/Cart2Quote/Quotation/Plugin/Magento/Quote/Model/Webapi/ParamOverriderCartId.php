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

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Model\Webapi;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;

/**
 * Class Quote
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Model\ResourceModel
 */
class ParamOverriderCartId extends \Magento\Quote\Model\Webapi\ParamOverriderCartId
{
    /**
     * User context
     *
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * Cart Management
     *
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    private $quoteSession;

    /**
     * ParamOverriderCartId constructor.
     *
     * @param UserContextInterface $userContext
     * @param CartManagementInterface $cartManagement
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     */
    public function __construct(
        UserContextInterface $userContext,
        CartManagementInterface $cartManagement,
        \Magento\Framework\App\RequestInterface $request,
        \Cart2Quote\Quotation\Model\Session $quoteSession
    ) {
        $this->quoteSession = $quoteSession;
        $this->request = $request;
        $this->userContext = $userContext;
        $this->cartManagement = $cartManagement;
        parent::__construct($userContext, $cartManagement);
    }

    /**
     * If the origin URL has quotation then the mine from the REST API
     * will be the quotation cart id instead of the Magento cart id
     *
     * @param $subject
     * @return int|null
     */
    public function aroundGetOverriddenValue($subject)
    {
        try {
            if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER) {

                $referer = $this->request->getHeader('Referer');
                if (strpos($referer, 'quotation') !== false) {
                    return $this->quoteSession->getQuoteId();
                } else {
                    $customerId = $this->userContext->getUserId();

                    /** @var \Magento\Quote\Api\Data\CartInterface */
                    $cart = $this->cartManagement->getCartForCustomer($customerId);
                    if ($cart) {
                        return $cart->getId();
                    }
                }
            }
        } catch (NoSuchEntityException $e) {
            /* do nothing and just return null */
        }
        return null;
    }
}
