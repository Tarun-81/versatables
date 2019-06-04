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
 * Class QuoteProposalSender
 * @package Cart2Quote\Quotation\Model\Quote\Email\Sender
 */
class QuoteProposalSender extends Sender
{
    /**
     * Path to attach_proposal_pdf in system.xml
     */
    const ATTACH_PROPOSAL_PDF = 'cart2quote_email/quote_proposal/attach_proposal_pdf';

    /**
     * Path to attach_proposal_doc in system.xml
     */
    const ATTACH_PROPOSAL_ATTACHMENT = 'cart2quote_email/quote_proposal/attach_proposal_doc';

    /**
     * Path to attach_proposal_name in system.xml
     */
    const ATTACH_PROPOSAL_NAME = 'cart2quote_email/quote_proposal/attach_proposal_name';

    /**
     * Folder structure for uploading email attachment
     */
    const QUOTATION_EMAIL_FOLDER = '/quotation/email/';

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * QuoteProposalSender constructor.
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
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
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Cart2Quote\Quotation\Model\Quote\Email\Container\AbstractQuoteIdentity $identityContainer,
        \Cart2Quote\Quotation\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel,
        $sendEmailIdentifier = \Cart2Quote\Quotation\Api\Data\QuoteInterface::SEND_PROPOSAL_EMAIL,
        $emailSentIdentifier = \Cart2Quote\Quotation\Api\Data\QuoteInterface::PROPOSAL_EMAIL_SENT
    ) {
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $eventManager,
            $globalConfig,
            $pdfModel,
            $sendEmailIdentifier,
            $emailSentIdentifier
        );
        $this->directoryList = $directoryList;
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param array|null $attachments
     * @return bool
     */
    protected function checkAndSend(
        \Cart2Quote\Quotation\Model\Quote $quote,
        $attachments = null

    ) {
        if ($this->getAttachPdf($quote)) {
            $filePath = $this->_pdfModel->createQuotePdf([$quote]);
            if (isset($filePath)) {
                $attachmentName = $quote->getIncrementId() . '.pdf';
                $attachments[$attachmentName] = $this->createFilePath($filePath);
                $quote->setAttachPdf(true);
            }
        }

        if ($file = $this->getAttachDocument($quote)) {
            $filePath = self::QUOTATION_EMAIL_FOLDER . $file;
            $attachmentName = $this->getAttachDocumentName($quote);
            $attachments[$attachmentName] = $this->createFilePath($filePath);
            $quote->setAttachDoc(true);
        }

        return parent::checkAndSend($quote, $attachments);
    }

    /**
     * Create complete file path
     *
     * @param $filePath
     * @return string
     */
    protected function createFilePath($filePath)
    {
        return $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            . DIRECTORY_SEPARATOR
            . $filePath;
    }

    /**
     * Get attach pdf configuration setting
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    protected function getAttachPdf($quote)
    {
        return $this->globalConfig->getValue(
            self::ATTACH_PROPOSAL_PDF,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $quote->getStoreId()
        );
    }

    /**
     * Get attached document
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string|null
     */
    protected function getAttachDocument($quote)
    {
        return $this->globalConfig->getValue(
            self::ATTACH_PROPOSAL_ATTACHMENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $quote->getStoreId()
        );
    }

    /**
     * Get attachment name
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string
     */
    protected function getAttachDocumentName($quote)
    {
        $name = $this->globalConfig->getValue(
            self::ATTACH_PROPOSAL_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $quote->getStoreId()
        );

        if (isset($name)) {
            return $name;
        }

        $document = $this->getAttachDocument($quote);
        $fileName = substr($document, strrpos($document, DIRECTORY_SEPARATOR) + 1);

        return $fileName;
    }
}
