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
 * Class SenderBuilder
 * @package Cart2Quote\Quotation\Model\Quote\Email
 */
class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    /**
     * @var \Magento\Sales\Model\Order\Email\Container\Template
     */
    protected $templateContainer;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Container\IdentityInterface
     */
    protected $identityContainer;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\UploadTransportBuilder
     */
    protected $uploadTransportBuilder;

    /**
     * Sender resolver
     * @var \Magento\Framework\Mail\Template\SenderResolverInterface
     */
    protected $_senderResolver;

    /**
     * SenderBuilder constructor.
     * @param \Magento\Sales\Model\Order\Email\Container\Template $templateContainer
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Container\IdentityInterface $identityContainer
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
     * @param \Cart2Quote\Quotation\Model\Quote\Email\UploadTransportBuilder $uploadTransportBuilder
     */
    public function __construct(
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Cart2Quote\Quotation\Model\Quote\Email\Container\IdentityInterface $identityContainer,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        UploadTransportBuilder $uploadTransportBuilder
    ) {
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $transportBuilder
        );
        $this->templateContainer = $templateContainer;
        $this->identityContainer = $identityContainer;
        $this->transportBuilder = $transportBuilder;
        $this->uploadTransportBuilder = $uploadTransportBuilder;
        $this->_senderResolver = $senderResolver;
    }

    /**
     * Prepare and send email message
     *
     * @param array|null $attachments
     */
    public function send(
        $attachments = null
    ) {
        if (is_array($attachments)) {
            foreach ($attachments as $attachmentName => $attachmentPath) {
                $this->transportBuilder = $this->uploadTransportBuilder;
                $this->transportBuilder->attachFile($attachmentPath, $attachmentName);
            }
        }
        $this->configureEmailTemplate();

        $this->transportBuilder->addTo(
            $this->identityContainer->getRecieverEmail(),
            $this->identityContainer->getRecieverName()
        );

        //Send copy to salesrep
        if ($this->identityContainer->isSendCopyToSalesRep() && $this->identityContainer->getCopyMethod() == 'bcc') {
            $emailIdentity = $this->_senderResolver->resolve($this->identityContainer->getEmailIdentity());
            $this->transportBuilder->addBcc($emailIdentity['email']);
        }

        $copyTo = $this->identityContainer->getEmailCopyTo();
        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'bcc') {
            foreach ($copyTo as $email) {
                $this->transportBuilder->addBcc($email);
            }
        }
        $transport = $this->transportBuilder->getTransport();
        $this->uploadTransportBuilder->resetUploadTransportBuilder();
        $transport->sendMessage();
    }

    /**
     * Prepare and send copy email message
     *
     * @return void
     */
    public function sendCopyTo()
    {
        //Send copy to salesrep
        if ($this->identityContainer->isSendCopyToSalesRep() && $this->identityContainer->getCopyMethod() == 'copy') {
            $this->configureEmailTemplate();
            $emailIdentity = $this->_senderResolver->resolve($this->identityContainer->getEmailIdentity());
            $this->transportBuilder->addTo($emailIdentity['email']);
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        }
        parent::sendCopyTo();
    }
}
