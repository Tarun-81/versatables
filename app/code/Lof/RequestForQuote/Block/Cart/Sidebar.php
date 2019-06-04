<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\RequestForQuote\Block\Cart;

use Magento\Store\Model\ScopeInterface;
/**
 * Cart sidebar block
 *
 * @api
 */
class Sidebar extends \Magento\Checkout\Block\Cart\Sidebar
{
	
	protected function _toHtml() {
		$disable_checkout = $this->getStoreConfig("requestforquote/general/disable_checkout", 0);
		if((bool)$disable_checkout) {
		    return "";
		}
		return parent::_toHtml();
	}
	public function getStoreConfig($path, $default = "") {
		$config_value = $this->_scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
        return $config_value?$config_value:$default;
	}
	
}