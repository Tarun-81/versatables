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

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Items;

/**
 * Class Quote
 * @package Cart2Quote\Quotation\Model\Sales\Quote\Pdf\Items
 */
class QuoteItem extends AbstractItems
{
    /**
     * Interface to get information about products
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepositoryInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $cart2QuoteHelper;

    /**
     * QuoteItem constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        parent::__construct(
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        $quote = $this->getQuote();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $this->_setFontRegular();
        $drawItems = [];
        $line = [];


        /* in case Product name is longer than 80 chars - it is written in a few lines */
        $feed = 35;
        $name = $item->getName();
        $nameArray['font'] = 'regular';

        $utils = $pdf->getStringUtils();

        $nameArray['text'] = $pdf->getStringUtils()->split($name, 35, true, true);
        $nameArray['feed'] = $feed;
        $nameArray['addToTop'] = -5;
        $nameArray['isProductLine'] = true;

        $line[] = $nameArray;
        $nameLineCount = count($nameArray['text']);


        $enabledShortDescription = $this->_scopeConfig->getValue(
            'cart2quote_pdf/quote/pdf_enable_short_description',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $splitDescription = [];
        if ($enabledShortDescription) {
            /* set short description beneath name*/
            $product = $this->productRepositoryInterface->getById($item->getProductId());

            $shortDescription = $product->getShortDescription();
            if ($nameLineCount > 1) {
                $shortDescription = ' ';
            }
            if ($shortDescription != '' && $shortDescription != null) {
                $shortDescription = preg_replace('/<(p>|\/p>)/i', "", $shortDescription);
            }
            $splitDescription = $pdf->getStringUtils()->split($shortDescription, 35, true, true);
            $line[] = [
                'text' => $splitDescription,
                'feed' => $feed + 5,
                'addToLeft' => 5,
                'addToTop' => 5,
                'font' => 'italic',
                'isProductLine' => true
            ];
        }

        //draw comment
        $comment = '';
        if (!$this->cart2QuoteHelper->isProductRemarkDisabled() && $item->getDescription()) {
            $comment = $item->getDescription();
            $top = (count($splitDescription) + $nameLineCount) * 10;
            $line[] = [
                'text' => $pdf->getStringUtils()->split(__('Remark'), 35, true, true),
                'feed' => $feed + 5,
                'addToLeft' => 5,
                'addToTop' => $top,
                'font' => 'bold',
                'isProductLine' => true
            ];
            $line[] = [
                'text' => $pdf->getStringUtils()->split($comment, 35, true, true),
                'feed' => $feed + 5,
                'addToLeft' => 5,
                'addToTop' => $top + 10,
                'font' => 'italic',
                'isProductLine' => true
            ];
        }

        // draw SKUs
        $text = [];
        foreach ($pdf->getStringUtils()->split($item->getSku(), 17) as $part) {
            $text[] = $part;
        }
        $line[] = ['text' => $text, 'feed' => 190, 'isProductLine' => true, 'addToTop' => -5];

        // draw prices
        $fontType = 'regular';
        $qty = $item->getQty();
        if ($item->getParentItem()) {
            $fontType = 'regular';
            $tax = null;
            $row_total = null;
            $qty = null;
        } else {
            $tax = $quote->formatPriceTxt($item->getTaxAmount());
            $row_total = $quote->formatPriceTxt($item->getRowTotal());
        }

        if ($item->getCurrentTierItem()->getCustomPrice() != null) {
            $price = $quote->formatPriceTxt($item->getCurrentTierItem()->getCustomPrice());
            $line[] = [
                'text' => $price,
                'feed' => 360,
                'font' => $fontType,
                'align' => 'right',
                'isProductLine' => true,
                'addToTop' => -5
            ];
            $line[] = [
                'text' => $qty,
                'feed' => 420,
                'font' => $fontType,
                'isProductLine' => true,
                'addToTop' => -5
            ];
            $line[] = [
                'text' => $tax,
                'feed' => 490,
                'font' => $fontType,
                'align' => 'right',
                'isProductLine' => true,
                'addToTop' => -5
            ];
            $line[] = [
                'font' => $fontType,
                'text' => $row_total,
                'feed' => 560,
                'align' => 'right',
                'isProductLine' => true,
                'addToTop' => -5
            ];
            if ($item->getTierItems()) {
                $lineTier = $this->setTierItemsPdf($quote, $item);
                $line = array_merge($line, $lineTier);
            }
        }
        $drawItems[]['lines'][] = $line;

        /**
         * Custom Options
         * Get Selected Custom Options from a Product
         */
        $options = array();
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $item->getProduct()->getOptionById($optionId)) {

                    $quoteItemOption = $item->getOptionByCode('option_' . $option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setQuoteItemOption($quoteItemOption);

                    $options[] = array(
                        'label' => $option->getTitle(),
                        'value' => $group->getFormattedOptionValue($quoteItemOption->getValue()),
                        'print_value' => $group->getPrintableOptionValue($quoteItemOption->getValue()),
                        'option_id' => $option->getId(),
                        'option_type' => $option->getType(),
                        'custom_view' => $group->isCustomizedView()
                    );
                }
            }
        }
        if ($addOptions = $item->getOptionByCode('additional_options')) {
            $options = array_merge($options, unserialize($addOptions->getValue()));
        }

        /**
         * Draw Options
         */
        if ($options) {
            foreach ($options as $option) {
                $customOption = sprintf('%s: %s', $option['label'], $option['value']);
                $lines[][] = [
                    'font' => 'italic',
                    'text' => [$customOption],
                    'feed' => 40
                ];
            }
            $drawItems[] = ['lines' => $lines, 'height' => 15];
        }

        /**
         * Print Bundle Options
         * Grabs the children and puts them below the Parent.
         * Kind of like, in real life.
         */

        if ($item->getProductType() == 'bundle') {
            foreach ($item->getChildren() as $child) {
                $childName = $child->getName();
                $childQty = $child->getQty();
                $childLine = sprintf("%s x %s", $childName, $childQty);
                $lines[][] = [
                    'font' => 'italic',
                    'text' => [$childLine],
                    'feed' => 40
                ];
            }
            $drawItems[] = ['lines' => $lines, 'height' => 15];
        }


        $page = $pdf->drawLineBlocks($page, $drawItems, ['table_header' => true]);
        $this->setPage($page);
    }

    /**
     * Set the tier quantity to PDF
     *
     * @param $quote
     * @param $item
     * @return array
     */
    public function setTierItemsPdf($quote, $item)
    {
        $tierItems = $item->getTierItems();
        $addToTop = 0;
        $line = [];
        foreach ($tierItems->getItems() as $tierItem) {
            if ($tierItem->getQty() != $item->getQty()) {
                $addToTop += 15;
                $price = $quote->formatPriceTxt($tierItem->getCustomPrice());
                $line[] = [
                    'text' => $price,
                    'feed' => 390,
                    'font' => 'regular',
                    'align' => 'right',
                    'isProductLine' => true,
                    'addToTop' => $addToTop
                ];
                $line[] = [
                    'text' => $tierItem->getQty() * 1,
                    'feed' => 400,
                    'font' => 'regular',
                    'isProductLine' => true,
                    'addToTop' => $addToTop
                ];
                $tax = $quote->formatPriceTxt($tierItem->getTaxAmount());
                $line[] = [
                    'text' => $tax,
                    'feed' => 490,
                    'font' => 'regular',
                    'align' => 'right',
                    'isProductLine' => true,
                    'height' => 15,
                    'addToTop' => $addToTop
                ];
                $row_total = $quote->formatPriceTxt($tierItem->getRowTotal());
                $line[] = [
                    'text' => $row_total,
                    'feed' => 560,
                    'font' => 'regular',
                    'align' => 'right',
                    'isProductLine' => true,
                    'addToTop' => $addToTop
                ];
            }
        }

        return $line;
    }
}
