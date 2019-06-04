<?php
namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Edit;

class Proposal extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('subtotal_proposal_form');
    }
 
    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Apply Subtotal Proposal');
    }

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-promo-quote';
    }
}
