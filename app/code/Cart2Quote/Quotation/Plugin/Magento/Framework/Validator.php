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

namespace Cart2Quote\Quotation\Plugin\Magento\Framework;

/**
 * Class Validator
 * @package Cart2Quote\Quotation\Plugin\Magento\Customer\Model
 */
class Validator extends \Magento\Framework\Validator
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Address constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     *
     */
    public function afterIsValid($subject, $value)
    {
        if (strpos($this->request->getHeader('Referer'), 'quotation') !== false) {
            $messages = $subject->getMessages();

            if (isset($messages['city']) ||
                isset($messages['postcode']) ||
                isset($messages['street']) ||
                isset($messages['telephone'])
            ) {
                $value = true;
            }
        }

        return $value;
    }
}
