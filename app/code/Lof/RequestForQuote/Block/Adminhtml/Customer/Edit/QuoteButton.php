<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\RequestForQuote\Block\Adminhtml\Customer\Edit;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;

class QuoteButton extends GenericButton implements ButtonProviderInterface {

	/**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->authorization = $context->getAuthorization();
        parent::__construct($context, $registry);
    }
    
	public function getButtonData() {

		$customerId = $this->getCustomerId();
		$data = [];
        if ($customerId && $this->authorization->isAllowed('Lof_RequestForQuote::quote_create')) {
            $data = [
	            'label' => __('Create Quote'),
	            'on_click' => sprintf("location.href = '%s';", $this->getCreateQuoteUrl()),
	            'class' => 'add',
	            'sort_order' => 40,
	        ];
        }
	    return $data;
	}
	/**
     * Retrieve the Url for creating an quote.
     *
     * @return string
     */
    public function getCreateQuoteUrl()
    {
        return $this->getUrl('quotation/quote_create/start', ['customer_id' => $this->getCustomerId()]);
    }
}