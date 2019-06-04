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

namespace Cart2Quote\Quotation\Helper;

/**
 * Quotation quote helper
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class QuotationCart extends \Magento\Checkout\Helper\Cart
{
    /**
     * Path to controller to delete item from cart
     */
    const DELETE_URL_QUOTE = 'quotation/quote/delete';

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_checkoutCart;

    /**
     * @var \Cart2Quote\Quotation\Model\QuotationCart
     */
    protected $_quotationCart;

    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $_quotationSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Cart2Quote\Quotation\Model\QuotationCart $quotationCart
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Magento\Framework\App\Helper\Context $context,
        \Cart2Quote\Quotation\Model\QuotationCart $quotationCart
    ) {
        $this->_checkoutCart = $quotationCart;
        $this->_quotationSession = $quotationSession;
        parent::__construct($context, $quotationCart, $quotationSession);
        $this->_checkoutSession = $quotationSession;
    }

    /**
     * Retrieve url for add product to quote
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return  string
     */
    public function getAddUrl($product, $additional = [])
    {
        $continueUrl = $this->urlEncoder->encode($this->_urlBuilder->getCurrentUrl());
        $urlParamName = \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED;

        $routeParams = [
            $urlParamName => $continueUrl,
            'product' => $product->getEntityId(),
            '_secure' => $this->_getRequest()->isSecure()
        ];

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_scope'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_scope_to_url'] = true;
        }

        if ($this->_getRequest()->getRouteName() == 'checkout' && $this->_getRequest()->getControllerName() == 'cart'
        ) {
            $routeParams['in_cart'] = 1;
        }

        return $this->_getUrl('quotation/quote/add', $routeParams);
    }

    /**
     * Get post parameters for delete from quote
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return string
     */
    public function getDeletePostJson($item)
    {
        $url = $this->_getUrl(self::DELETE_URL_QUOTE);

        $data = ['id' => $item->getId()];
        if (!$this->_request->isAjax()) {
            $data[\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED] = $this->getCurrentBase64Url();
        }
        return json_encode(['action' => $url, 'data' => $data]);
    }

    /**
     * Retrieve quotation quote url
     * @return string
     */
    public function getCartUrl()
    {
        return $this->_getUrl('quotation/quote');
    }
}
