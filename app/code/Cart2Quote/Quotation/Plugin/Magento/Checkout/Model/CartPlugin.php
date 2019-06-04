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

namespace Cart2Quote\Quotation\Plugin\Magento\Checkout\Model;

/**
 * Class CartPlugin
 * @package Cart2Quote\Quotation\Plugin\Magento\Checkout\Model
 */
class CartPlugin
{
    /**
     * Data helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;

    /**
     * CartPlugin constructor.
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     */
    public function __construct( \Cart2Quote\Quotation\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Check if allowed function AddProduct
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param int|\Magento\Catalog\Model\Product $productInfo
     * @param \Magento\Framework\DataObject|int|array  $requestInfo
     * @return array
     * @throws \Exception
     */
    public function beforeAddProduct
    (
        \Magento\Checkout\Model\Cart $subject,
        $productInfo,
        $requestInfo = null
    ) {
        $this->allowedMethod();

        return [$productInfo, $requestInfo];
    }

    /**
     * Check if allowed function AddProductsByIds
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function beforeAddProductsByIds(\Magento\Checkout\Model\Cart $subject, $productIds)
    {
        $this->allowedMethod();

        return [$productIds];
    }

    /**
     * Check if allowed function UpdateItems
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function beforeUpdateItems(\Magento\Checkout\Model\Cart $subject, $data)
    {
        $this->allowedMethod();

        return [$data];
    }

    /**
     * Check if allowed function UpdateItem
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param int|array|\Magento\Framework\DataObject $requestInfo
     * @param null|array|\Magento\Framework\DataObject $updatingParams
     * @return array
     * @throws \Exception
     */
    public function beforeUpdateItem
    (
        \Magento\Checkout\Model\Cart $subject,
        $requestInfo = null,
        $updatingParams = null
    ) {
        $this->allowedMethod();

        return [$requestInfo, $updatingParams];
    }

    /**
     * Check if allowed function
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param int $itemId
     * @return int
     * @throws \Exception
     */
    public function beforeRemoveItem(\Magento\Checkout\Model\Cart $subject, $itemId)
    {
        $this->allowedMethod();

        return [$itemId];
    }

    /**
     * Blocks method if active confirm mode is set true to session
     *
     * @throws \Exception
     */
    public function allowedMethod()
    {
        $confirmationMode = $this->helper->getActiveConfirmMode();

        if ($confirmationMode) {
            throw new \Exception('Action is blocked in quote confirmation mode.');
        }
    }
}
