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

namespace Cart2Quote\AutoProposal\Observer;

/**
 * Class QuoteRequestObserver
 *
 * @package Cart2Quote\AutoProposal\Observer
 */
class QuoteRequestObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyProvider
     */
    private $strategyProvider;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * QuoteRequestObserver constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyProvider $strategyProvider
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyProvider $strategyProvider
    ) {
        $this->strategyProvider = $strategyProvider;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();

        try {
            if ($this->strategyProvider->getStrategy()->isEnabled()) {
                $this->strategyProvider->getStrategy()->propose($quote);
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
