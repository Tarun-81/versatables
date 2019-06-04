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
 * Class TierPricing
 *
 * @package Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy
 */
class TierPricing extends AbstractStrategy
{
    /**
     * Strategy identifier
     */
    const STRATEGY_IDENTIFIER = 'tier_pricing';

    /**
     * @return $this
     */
    public function setProposalPrices()
    {
        //Don't have to set prices here as it takes the prices from Product tier pricing settings
        return $this;
    }
}
