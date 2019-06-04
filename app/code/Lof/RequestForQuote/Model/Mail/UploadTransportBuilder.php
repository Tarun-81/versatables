<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\RequestForQuote\Model\Mail;

use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;


class UploadTransportBuilder extends TransportBuilder {
    public function __construct(FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory) {

        parent::__construct($templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory);
    }

    public function addAttachment($file_content, $name, $file_type = "application/pdf") {
        if (!empty($file_content) && !empty($name)) {
            $this->message
            ->createAttachment(
                $file_content,
                $file_type,
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                basename($name)
                );
            }

        return $this;
    }

}