<?php

namespace Lof\RequestForQuote\Model\AdminOrder;

class Create extends \Magento\Sales\Model\AdminOrder\Create
{
    /**
     * Retrieve quote object model
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->getSession()->getRfqQuote();
        }

        return $this->_quote;
    }
    public function updateRfqQuoteItems($items)
    {
        if (!is_array($items)) {
            return $this;
        }
        try {
            foreach ($items as $itemId => $info) {
                if (!empty($info['configured'])) {
                    $item = $this->getQuote()->updateItem($itemId, $this->objectFactory->create($info));
                    $info['qty'] = (double)$item->getQty();
                } else {
                    $item = $this->getQuote()->getItemById($itemId);
                    if (!$item) {
                        continue;
                    }
                    $info['qty'] = (double)$info['qty'];
                }
                $this->quoteItemUpdater->update($item, $info);
                $item->save();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }

        return $this;
    }

    protected function _validate()
    {
        $customerId = $this->getSession()->getCustomerId();
        if (is_null($customerId)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please select a customer'));
        }

        if (!$this->getSession()->getStore()->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please select a store'));
        }
        $items = $this->getQuote()->getAllItems();

        if (count($items) == 0) {
            $this->_errors[] = __('Please specify order items.');
        }

        foreach ($items as $item) {
            $messages = $item->getMessage(false);
            if ($item->getHasError() && is_array($messages) && !empty($messages)) {
                $this->_errors = array_merge($this->_errors, $messages);
            }
        }

        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                $this->messageManager->addError($error);
            }
            throw new \Magento\Framework\Exception\LocalizedException(__('Validation is failed.'));
        }

        return $this;
    }
}