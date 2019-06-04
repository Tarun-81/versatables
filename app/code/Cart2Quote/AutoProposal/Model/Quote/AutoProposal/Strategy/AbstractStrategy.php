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

namespace Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy;

/**
 * Class AbstractStrategy
 *
 * @package Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy
 */
abstract class AbstractStrategy implements \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote $quote
     */
    private $quote;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender
     */
    private $quoteProposalSender;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * AbstractAutoProposal constructor.
     *
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender $quoteProposalSender
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender $quoteProposalSender
    ) {
        $this->eventManager = $eventManager;
        $this->quoteProposalSender = $quoteProposalSender;
        $this->dateTime = $dateTime;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     *
     * @return $this
     */
    public function setQuote(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfigEnabled() && $this->getConfigStrategy() == static::STRATEGY_IDENTIFIER;
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function propose(\Cart2Quote\Quotation\Model\Quote $quote = null)
    {
        if (isset($quote)) {
            $this->setQuote($quote);
        }

        $quote = $this->getQuote();
        if (!isset($quote) || !$quote instanceof \Cart2Quote\Quotation\Model\Quote) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Missing parameter "quote" or the is not of type %2',
                    \Cart2Quote\Quotation\Model\Quote::class
                )
            );
        }

        $this->eventManager->dispatch(
            'autoproposal_before_set_proposal_prices', ['quote' => $this->getQuote(), 'strategy' => $this]
        );

        $this->setProposalPrices();

        $this->eventManager->dispatch(
            'autoproposal_after_set_proposal_prices', ['quote' => $this->getQuote(), 'strategy' => $this]
        );
        $this->send();

        return $this->getQuote()->save();
    }

    /**
     * Get autoproposal email sending delay amount in minutes
     *
     * @return int
     */
    public function getDelayAmount()
    {
        return $this->getConfigDelay();
    }

    /**
     * Prepare the quote for auto proposal
     */
    //@codingStandardsIgnoreLine
    /**
     * @return $this
     */
    abstract public function setProposalPrices();

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function send()
    {
        $this->setSentData();
        $this->eventManager->dispatch(
            'autoproposal_before_auto_proposal_send', ['quote' => $this->getQuote(), 'strategy' => $this]
        );

        if ($this->getDelayAmount() <= 0) {
            $this->quoteProposalSender->send($this->quote);
        } else {
            $this->quote->setData("send_proposal_email", true);
            $this->quote->setData("proposal_email_sent", null);
        }

        $this->eventManager->dispatch(
            'autoproposal_after_auto_proposal_sent', ['quote' => $this->getQuote(), 'strategy' => $this]
        );

        return $this;
    }


    /**
     * @return $this
     */
    protected function setSentData()
    {
        $this->quote->setProposalSent($this->dateTime->gmtTimestamp());
        $this->quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_PENDING);
        $this->quote->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_AUTO_PROPOSAL_SENT);

        return $this;
    }

    /**
     * @return string
     */
    private function getConfigStrategy()
    {
        return $this->getScopeConfig()->getValue(
            static::XML_CONFIG_PATH_AUTO_PROPOSAL_STRATEGY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    private function getConfigEnabled()
    {
        return $this->getScopeConfig()->getValue(
            static::XML_CONFIG_PATH_AUTO_PROPOSAL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    private function getConfigDelay()
    {
        return $this->getScopeConfig()->getValue(
            static::XML_CONFIG_PATH_AUTO_PROPOSAL_DELAY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
