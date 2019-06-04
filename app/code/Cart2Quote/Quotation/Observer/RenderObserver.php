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

namespace Cart2Quote\Quotation\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class RenderObserver
 *
 * @package Cart2Quote\Quotation\Observer
 */
class RenderObserver implements ObserverInterface
{
    /**
     * Core store config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Cart2Quote Module Enabled
     */
    protected $_enabledModule;

    /**
     * Cart2Quote Alternative Rendering Enabled
     */
    protected $_enabledAlternateRendering;

    /**
     * Module manager
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * HideConditions constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Module\Manager $moduleManager,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_coreRegistry = $coreRegistry;
        $this->_moduleManager = $moduleManager;
        $this->quotationHelper = $quotationHelper;
        $this->_enabledAlternateRendering = !(bool)$scopeConfig->getValue(
            'cart2quote_advanced/general/disable_alternate_rendering',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->_enabledModule = $moduleManager->isEnabled('Cart2Quote_Quotation');
    }

    /**
     * The function that gets executed when the event is observed
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_enabledModule || !$this->quotationHelper->isFrontendEnabled() || !$this->_enabledAlternateRendering) {
            return;
        }

        $categoryViewElements = [
            "category.products.list",
            "search_result_list"
        ];

        $elementName = $observer->getElementName();
        $transport = $observer->getTransport();
        $layout = $observer->getLayout();
        if ($transport && in_array($elementName, $categoryViewElements)) {
            $html = $transport->getOutput();
            $products = $this->_coreRegistry->registry('c2q_current_salable_product');
            $html = $this->addOurButton($html, $products, $elementName, $layout);
            $observer->getTransport()->setOutput($html);
        }
    }

    /**
     * Add validator to the html
     * This function could be avoided if we introduce template files
     *
     * @param $html
     * @param $products
     * @param $elementName
     * @param $layout
     * @return string
     */
    protected function addOurButton($html, $products, $elementName, $layout)
    {
        $productButtonsClass = 'product-item-actions';

        //find all indiviual products
        $pos = 0;
        while (($pos = strpos($html, $productButtonsClass, $pos)) !== false) {
            $maxNextPos = strpos($html, $productButtonsClass, $pos + 1);

            //find the product id
            $productId = $this->getProductId($html, $pos, $maxNextPos);

            //get replace button
            if ($productId != false && isset($products[$productId])) {
                $block = $layout->getBlock($elementName);
                $replaceButton = $block->getChildBlock('addtocart.addtoquote')
                    ->setListProduct($products[$productId])
                    ->setCacheLifeTime(null)
                    ->toHtml();

                //replace the button
                $html = $this->replaceButton($html, $pos, $maxNextPos, $replaceButton);
            }

            $pos++;
        }

        return $html;
    }

    /**
     * @param $html
     * @param $pos
     * @param $maxNextPos
     * @return bool|string
     */
    protected function getProductId($html, $pos, $maxNextPos)
    {
        $addToCartIdString = '<input type="hidden" name="product" value="';
        $usedAddToCartIdString = $addToCartIdString;
        $addToCartIdStringAlternate = '"product":"';
        $addToCartIdEndString = '"';

        $addToCartIdStart = strpos($html, $addToCartIdString, $pos);

        if ($addToCartIdStart === false || ($maxNextPos !== false && $addToCartIdStart > $maxNextPos)) {
            $addToCartIdStart = strpos($html, $addToCartIdStringAlternate, $pos);
            $usedAddToCartIdString = $addToCartIdStringAlternate;
            if ($addToCartIdStart === false) {
                //Is this an empty item? Continue for now.
                return false;
            }
        }

        if ($addToCartIdStart !== false) {
            $addToCartIdStart = $addToCartIdStart + strlen($usedAddToCartIdString);
            $addToCartIdEnd = strpos($html, $addToCartIdEndString, $addToCartIdStart);

            if ($addToCartIdEnd !== false) {
                $addToCartId = substr($html, $addToCartIdStart, ($addToCartIdEnd - $addToCartIdStart));
                return $addToCartId;
            }
        }

        return false;
    }

    /**
     * @param $html
     * @param $pos
     * @param $maxNextPos
     * @param $replaceButton
     * @return string
     */
    protected function replaceButton($html, $pos, $maxNextPos, $replaceButton)
    {
        $findPrimaryButtonDivString = 'actions-primary';
        $primaryButtonDivStart = strpos($html, $findPrimaryButtonDivString, $pos);

        if ($primaryButtonDivStart !== false && (($primaryButtonDivStart < $maxNextPos) || ($maxNextPos === false))) {
            $primaryButtonDivStart = strpos($html, '>', $primaryButtonDivStart) + strlen('>');
            $primaryButtonDivNext = strpos($html, '<div', $primaryButtonDivStart);
            $primaryButtonDivEnd = strpos($html, '</div>', $primaryButtonDivStart);

            if (($primaryButtonDivNext !== false) && ($primaryButtonDivEnd !== false)) {
                if ($primaryButtonDivNext < $primaryButtonDivEnd) {
                    //nested div
                    $nested = 2;
                    $searchStart = $primaryButtonDivNext;
                    while (($nested >= 1) && ($nested < 50)) { //above 50 is rare and probably an other issue so, exit.
                        $searchStart++;
                        $primaryButtonDivNext = strpos($html, '<div', $searchStart);
                        $primaryButtonDivEnd = strpos($html, '</div>', $searchStart);
                        if ($primaryButtonDivNext < $primaryButtonDivEnd || ($primaryButtonDivNext === false)) {
                            $nested++;
                            $searchStart = $primaryButtonDivNext;
                        } else {
                            $nested--;
                            $searchStart = $primaryButtonDivEnd;
                        }
                    }
                }
            }

            if (($primaryButtonDivStart !== false) && ($primaryButtonDivEnd !== false)) {
                $htmlWithReplacedButton = substr($html, 0, $primaryButtonDivStart);
                $htmlWithReplacedButton .= $replaceButton;
                $htmlWithReplacedButton .= substr($html, $primaryButtonDivEnd);

                //set new HTML
                $html = $htmlWithReplacedButton;
            }
        }

        return $html;
    }
}
