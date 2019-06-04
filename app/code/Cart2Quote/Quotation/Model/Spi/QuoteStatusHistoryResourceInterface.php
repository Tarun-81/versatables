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

namespace Cart2Quote\Quotation\Model\Spi;

/**
 * Interface ResourceInterface
 */
interface QuoteStatusHistoryResourceInterface
{
    /**
     * Save object data
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function save(\Magento\Framework\Model\AbstractModel $object);

    /**
     * Load an object
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param string|null $field field to load by (defaults to model id)
     * @return mixed
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null);

    /**
     * Delete the object
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return mixed
     */
    public function delete(\Magento\Framework\Model\AbstractModel $object);
}
