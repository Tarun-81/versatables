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

namespace Cart2Quote\Quotation\Model\Quote;

/**
 * Class CompositeConfigProvider
 * @package Cart2Quote\Quotation\Model\Quote
 */
class CompositeConfigProvider extends \Magento\Checkout\Model\CompositeConfigProvider
{
    /**
     * Replace the default config provider to get from the quote session instead of the checkout session
     *
     * @param \Cart2Quote\Quotation\Model\Quote\ConfigProvider $quotationSessionConfigProvider
     * @param \Cart2Quote\Quotation\Model\Quote\QuotationConfigProvider $quotationConfigProvider
     * @param array $configProviders
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\ConfigProvider $quotationSessionConfigProvider,
        \Cart2Quote\Quotation\Model\Quote\QuotationConfigProvider $quotationConfigProvider,
        array $configProviders
    ) {
        $configProviders['checkout_default_config_provider'] = $quotationSessionConfigProvider;
        $configProviders['quotation_config_provider'] = $quotationConfigProvider;
        $configProviders = array_intersect_key($configProviders, $this->getAllowedConfigProviders());

        parent::__construct($configProviders);
    }

    /**
     * Get the allowed config providers
     * Other config providers are ignored.
     *
     * @return array
     */
    protected function getAllowedConfigProviders()
    {
        return array_fill_keys([
            'checkout_default_config_provider',
            'quotation_config_provider',
            'checkout_default_config_provider',
            'persistent_checkout_config_provider',
            'checkout_captcha_config_provider',
            'tax_config_provider',
            'weee_config_provider',
            'checkout_captcha_config_provider'
        ], "");
    }
}
