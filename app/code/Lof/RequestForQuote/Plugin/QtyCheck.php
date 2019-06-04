<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\RequestForQuote\Plugin;

class QtyCheck
{
	protected $_helperData;

	public function __construct(
        \Lof\RequestForQuote\Helper\Data $_helperData
    ) {
        $this->_helperData = $_helperData;
    }
	public function aroundCheckQty(\Magento\CatalogInventory\Model\StockStateProvider $subject, $proceed, \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem, $qty) 
	{ 
		$disable_check_qty = $this->_helperData->getConfig("general/disable_check_qty", 0);
        $disable_check_qty = (int)$disable_check_qty;
        $disable_check_qty = ($disable_check_qty==1)?true:false;
        if($disable_check_qty) {
            return true; 
        }

        return $proceed($stockItem, $qty);
	}
}