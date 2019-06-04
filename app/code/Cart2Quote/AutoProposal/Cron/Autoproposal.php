<?php
/**
 * Cart2Quote
 */

namespace Cart2Quote\AutoProposal\Cron;

/**
 * Class Autoproposal
 * @package Cart2Quote\AutoProposal\Cron
 */
class Autoproposal
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    private $date;

    /**
     * @var \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyProvider
     */
    private $strategyProvider;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender
     */
    private $quoteProposalSender;

    /**
     * Global configuration storage.
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $globalConfig;

    /**
     * Autoproposal constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyProvider $strategyProvider
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender $quoteProposalSender
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyProvider $strategyProvider,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender $quoteProposalSender,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->date = $date;
        $this->strategyProvider = $strategyProvider;
        $this->quoteProposalSender = $quoteProposalSender;
        $this->globalConfig = $globalConfig;
    }

    /**
     * Send autoproposal email to customer with delay
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $availableStatus = [
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_AUTO_PROPOSAL_SENT
        ];

        $delayAmount = $this->strategyProvider->getStrategy()->getDelayAmount();
        if ($delayAmount > 0 && !$this->globalConfig->getValue('sales_email/general/async_sending')) {
            $timeStamp = $this->date->date("Y-m-d H:i:s", strtotime("-$delayAmount minutes"));

            $quotes = $this->collectionFactory->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('is_quote', ['eq' => 1])
                ->addFieldToFilter('status', ['in' => $availableStatus])
                ->addFieldToFilter(\Cart2Quote\Quotation\Api\Data\QuoteInterface::SEND_PROPOSAL_EMAIL, ['eq' => 1])
                ->addFieldToFilter(\Cart2Quote\Quotation\Api\Data\QuoteInterface::PROPOSAL_EMAIL_SENT, ['null' => true])
                ->addFieldToFilter('created_at', ['lteq' => $timeStamp]);

            foreach ($quotes as $quote) {
                $this->quoteProposalSender->send($quote);
            }
        }
    }
}
