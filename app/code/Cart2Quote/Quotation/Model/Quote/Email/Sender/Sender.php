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

namespace Cart2Quote\Quotation\Model\Quote\Email\Sender;

/**
 * Class Sender
 * @package Cart2Quote\Quotation\Model\Quote\Email\Sender
 */
class Sender extends \Cart2Quote\Quotation\Model\Quote\Email\AbstractSender implements QuoteSenderInterface
{
    /**
     * Application Event Dispatcher
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;
    /**
     * Global configuration storage.
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $globalConfig;
    /**
     * @var string
     */
    protected $sendEmailIdentifier;
    /**
     * @var string
     */
    protected $emailSentIdentifier;

    /**
     * QuoteCanceledSender constructor.
     * @param \Magento\Sales\Model\Order\Email\Container\Template $templateContainer
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Container\AbstractQuoteIdentity $identityContainer
     * @param \Cart2Quote\Quotation\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
     * @param \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel
     * @param string $sendEmailIdentifier
     * @param string $emailSentIdentifier
     */
    public function __construct(
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Cart2Quote\Quotation\Model\Quote\Email\Container\AbstractQuoteIdentity $identityContainer,
        \Cart2Quote\Quotation\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel,
        $sendEmailIdentifier = '',
        $emailSentIdentifier = ''
    ) {
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $pdfModel
        );
        $this->eventManager = $eventManager;
        $this->globalConfig = $globalConfig;
        $this->sendEmailIdentifier = $sendEmailIdentifier;
        $this->emailSentIdentifier = $emailSentIdentifier;
    }

    /**
     * @return string
     */
    public function getSendEmailIdentifier()
    {
        return $this->sendEmailIdentifier;
    }

    /**
     * @return string
     */
    public function getEmailSentIdentifier()
    {
        return $this->emailSentIdentifier;
    }

    /**
     * Sends quote request email to the customer.
     * Email will be sent immediately in two cases:
     * - if asynchronous email sending is disabled in global settings
     * - if $forceSyncMode parameter is set to TRUE
     * Otherwise, email will be sent later during running of
     * corresponding cron job.
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param bool $forceSyncMode
     * @return bool
     */
    public function send(\Cart2Quote\Quotation\Model\Quote $quote, $forceSyncMode = false)
    {
        $quote->setData($this->sendEmailIdentifier, true);
        $quote->setData($this->emailSentIdentifier, null);

        if (!$this->globalConfig->getValue('sales_email/general/async_sending') || $forceSyncMode) {
            if ($this->checkAndSend($quote)) {
                $quote->setData($this->emailSentIdentifier, true);
                $quote->save();

                return true;
            }
        }

        $quote->save();

        return false;
    }

    /**
     * Prepare email template with variables
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return void
     */
    protected function prepareTemplate(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $transport = [
            'quote' => $quote,
            'billing' => $quote->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($quote),
            'store' => $quote->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($quote),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($quote),
            'attach_pdf' => $quote->getAttachPdf(),
            'attach_doc' => $quote->getAttachDoc(),
        ];

        $this->eventManager->dispatch(
            'email_quote_set_template_vars_before',
            ['sender' => $this, 'transport' => $transport]
        );

        $this->templateContainer->setTemplateVars($transport);

        parent::prepareTemplate($quote);
    }

    /**
     * Get payment info block as html
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string
     */
    protected function getPaymentHtml(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        return '';
    }
}