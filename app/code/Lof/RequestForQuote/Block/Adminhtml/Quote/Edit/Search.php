<?php

namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Edit;

class Search extends \Magento\Sales\Block\Adminhtml\Order\Create\Search {

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'label' => __('Add Selected Product(s) to Quote'),
            'onclick' => 'order.productGridAddSelected()',
            'class' => 'action-add action-secondary',
        ];
        return $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            $addButtonData
        )->toHtml();
    }
}