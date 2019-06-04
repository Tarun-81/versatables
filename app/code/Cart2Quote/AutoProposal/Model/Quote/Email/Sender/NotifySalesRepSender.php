<?php
/**
 *
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2017] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 */

namespace Cart2Quote\AutoProposal\Model\Quote\Email\Sender;

/**
 * Class NotifySalesRepSender
 *
 * @package Cart2Quote\AutoProposal\Model\Quote\Email\Sender
 */
class NotifySalesRepSender extends \Cart2Quote\Quotation\Model\Quote\Email\Sender\Sender
{
    /**
     * @var \Cart2Quote\AutoProposal\Helper\Range
     */
    private $rangeHelper;

    /**
     * NotifySalesRepSender constructor.
     *
     * @param \Cart2Quote\AutoProposal\Helper\Range $rangeHelper
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
        \Cart2Quote\AutoProposal\Helper\Range $rangeHelper,
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Cart2Quote\Quotation\Model\Quote\Email\Container\AbstractQuoteIdentity $identityContainer,
        \Cart2Quote\Quotation\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel,
        $sendEmailIdentifier = \Cart2Quote\AutoProposal\Api\Data\AutoProposalInterface::SEND_NOTIFY_SALESREP_EMAIL,
        $emailSentIdentifier = \Cart2Quote\AutoProposal\Api\Data\AutoProposalInterface::NOTIFY_SALESREP_EMAIL_SENT
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
        $this->rangeHelper = $rangeHelper;
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     */
    protected function prepareTemplate(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $transport = [
            'salesrep' => new \Magento\Framework\DataObject(
                ['name' => $this->identityContainer->getRecieverName()]
            ),
            'quote' => $quote,
            'store' => $quote->getStore(),
            'auto_proposal_range' => $this->rangeHelper->getCurrentRange($quote)
        ];

        $this->eventManager->dispatch(
            'email_quote_set_template_vars_before',
            ['sender' => $this, 'transport' => $transport]
        );

        $this->templateContainer->setTemplateVars($transport);

        \Cart2Quote\Quotation\Model\Quote\Email\AbstractSender::prepareTemplate($quote);
    }
}
