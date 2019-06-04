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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns;

use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class Name
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Grid
 */
class Name extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{

    /**
     * Catalog helper
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * Name constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Cart2Quote\Quotation\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Item $emptyQuoteItem,
        \Magento\Catalog\Helper\Data $catalogHelper,
        array $data = []
    ) {
        $this->catalogHelper = $catalogHelper;

        parent::__construct(
            $context,
            $stockRegistry,
            $stockConfiguration,
            $registry,
            $optionFactory,
            $quote,
            $emptyQuoteItem,
            $data
        );
    }

    /**
     * Add line breaks and truncate value
     *
     * @param string $value
     * @return array
     */
    public function getFormattedOption($value)
    {
        $remainder = '';
        $value = $this->truncateString($value, 55, '', $remainder);
        $result = ['value' => nl2br($value), 'remainder' => nl2br($remainder)];

        return $result;
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filterManager->truncate(
            $value,
            ['length' => $length, 'etc' => $etc, 'remainder' => $remainder, 'breakWords' => $breakWords]
        );
    }

    /**
     * @return array
     */
    public function getOrderOptions()
    {
        $result = [];

        if ($options = $this->getItem()->getProduct()->getTypeInstance(true)->getOrderOptions($this->getItem()->getProduct())) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }

        return $result;
    }

    /**
     * Return html button which calls configure window
     *
     * @param Item $item
     * @return string
     */
    public function getConfigureButtonHtml($item)
    {
        $product = $item->getProduct();

        $options = ['label' => __('Configure')];
        if ($product->canConfigure()) {
            $options['onclick'] = sprintf('quote.showQuoteItemConfiguration(%s)', $item->getId());
        } else {
            $options['class'] = ' disabled';
            $options['title'] = __('This product does not have any configurable options');
        }

        return $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData($options)->toHtml();
    }

    /**
     * Split SKU of an item by dashes and spaces
     * Words will not be broken, unless this length is greater than $length
     *
     * @param string $sku
     * @param int $length
     * @return string[]
     */
    public function splitSku($sku, $length = 30)
    {
        return $this->catalogHelper->splitSku($sku, $length);
    }
}
