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
 * Interface StrategyInterface
 *
 * @package Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy
 */
interface StrategyInterface
{
    /**
     * Config path
     */
    const XML_CONFIG_PATH_AUTO_PROPOSAL_ENABLED = 'cart2quote_quotation/proposal/auto_proposal';
    /**
     * Config path
     */
    const XML_CONFIG_PATH_AUTO_PROPOSAL_STRATEGY = 'cart2quote_quotation/proposal/auto_proposal_strategy';
    /**
     * Config path
     */
    const XML_CONFIG_PATH_AUTO_PROPOSAL_DELAY = 'cart2quote_quotation/proposal/auto_proposal_delay';

    /**
     * Strategy identifier
     */
    const STRATEGY_IDENTIFIER = '';

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function propose(\Cart2Quote\Quotation\Model\Quote $quote = null);

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @return int
     */
    public function getDelayAmount();

    /**
     * @return $this
     */
    public function setProposalPrices();
}
