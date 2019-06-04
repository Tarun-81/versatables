<?php

namespace Lof\RequestForQuote\Controller\Adminhtml\Quote;

abstract class Create extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * Retrieve quote object
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function _getQuote()
    {
        return $this->_getSession()->getRfqQuote();
    }

    /**
     * Retrieve order create model
     *
     * @return \Magento\Sales\Model\AdminOrder\Create
     */
    protected function _getOrderCreateModel()
    {
        return $this->_objectManager->get('Lof\RequestForQuote\Model\AdminOrder\Create');
    }

    protected function _processActionData($action = null)
    {
        $postData = $this->getRequest()->getPost();

        if (isset($postData['update_items'])) {
            $items = $postData['item'];
            foreach ($items as $key => $value) {
                if (isset($value['custom_price']) && $value['custom_price'] && !is_numeric($value['custom_price'])) {
                    return null;
                }
            }
        }

        parent::_processActionData($action);

        /**
         * Update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $items = $this->_processFiles($items);
            $this->_getOrderCreateModel()->updateRfqQuoteItems($items);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_RequestForQuote::quote_create');
    }
}