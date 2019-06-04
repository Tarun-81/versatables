<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Plugin\Sales\Block\Adminhtml\Order\View;

use Aitoc\CheckoutFieldsManager\Block\Adminhtml\Sales\Order\Invoice\AitocCheckoutFields;
use Magento\Sales\Block\Adminhtml\Order\View\Info;

class InfoPlugin
{
    /**
     * Show edit url for administrators
     */
    const SHOW_EDIT_URL = 1;

    /**
     * @param Info $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(Info $subject, $result)
    {
        /** @var AitocCheckoutFields $block */
        $block = $subject->getLayout()->createBlock(
            AitocCheckoutFields::class,
            'adminhtml_sales_order_view_aitoc_info'
        );

        $block->setEditUrl(self::SHOW_EDIT_URL);

        if ((!$block->hasOrder()) && ($order = $subject->getOrder())) {
            $block->setOrder($order);
        }

        return $result . $block->toHtml();
    }
}
