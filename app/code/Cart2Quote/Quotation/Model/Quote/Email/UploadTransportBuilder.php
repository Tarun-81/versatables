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

namespace Cart2Quote\Quotation\Model\Quote\Email;

/**
 * Class UploadTransportBuilder
 * @package Cart2Quote\Quotation\Model\Quote\Email
 */
class UploadTransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * Function to attach a file to an outgoing email
     *
     * @param $file
     * @param $name
     * @return $this
     */
    public function attachFile($file, $name)
    {
        if (!empty($file) && file_exists($file)) {
            $this->message
                ->createAttachment(
                    file_get_contents($file),
                    \Zend_Mime::TYPE_OCTETSTREAM,
                    \Zend_Mime::DISPOSITION_ATTACHMENT,
                    \Zend_Mime::ENCODING_BASE64,
                    basename($name)
                );
        }

        return $this;
    }

    /**
     * Reset UploadTransportBuilder object state
     *
     */
    public function resetUploadTransportBuilder()
    {
        $this->reset();
    }
}
