<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Traits;

use Magento\Framework\App\ObjectManager;

trait CustomFields
{
    /**
     * @var array|null
     */
    protected $checkoutFieldsData = null;

    /**
     * @var int
     */
    protected $orderId;

    /**
     * Get customer checkout fields for order
     *
     * @param int $orderId
     *
     * @return self
     */
    protected function prepareCheckoutFieldsData($orderId)
    {
        if ($this->checkoutFieldsData === null) {
            /** @var \Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection $collection */
            $collection = ObjectManager::getInstance()
                ->create('Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection');
            $this->checkoutFieldsData = $collection->getAitocCheckoutfieldsByOrderId($orderId);
            if (!is_array($this->checkoutFieldsData)) {
                $this->checkoutFieldsData = [];
            }
        };

        return $this;
    }

    /**
     * Prepare attribute label and value without inputs, with HTML
     *
     * @param array $data
     *
     * @return array
     */
    protected static function getHtmlValuesSet($data = [])
    {
        $html = [];
        $valueRenderer = self::getRender();
        foreach ($data as $field) {
            $html[] = $valueRenderer->renderFieldValueHtml($field);
        }

        return $html;
    }

    /**
     * Set label for fields
     *
     * @param array $data
     *
     * @return array
     */
    protected static function setShowValue($data = [])
    {
        if (!empty($data)) {
            $valueRenderer = self::getRender();
            foreach ($data as $key => $field) {
                $data[$key]['value'] = $valueRenderer->getFormattedValue($field, true);
            }
        }

        return $data;
    }

    /**
     * Get customer checkout fields for order
     * Row contains HTML with label and value of checkout attribute
     * @return array
     */
    public function getCheckoutFieldsData()
    {
        $this->prepareCheckoutFieldsData($this->orderId);
        if (!empty($this->checkoutFieldsData)) {
            return self::getHtmlValuesSet($this->checkoutFieldsData);
        }

        return [];
    }

    /**
     * @return \Aitoc\CheckoutFieldsManager\Block\Element\ValueRenderer
     */
    private static function getRender()
    {
        return ObjectManager::getInstance()
            ->create('Aitoc\CheckoutFieldsManager\Block\Element\ValueRenderer');
    }
}
