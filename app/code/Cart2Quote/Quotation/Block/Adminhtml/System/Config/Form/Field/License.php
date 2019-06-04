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

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class License
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class License extends \Magento\Config\Block\System\Config\Form\Field implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{

    /**
     * @var \Cart2Quote\Quotation\Helper\Data\LicenseInterface
     */
    private $licenseData;

    /**
     * License constructor.
     * @param \Cart2Quote\Quotation\Helper\Data\LicenseInterface $licenseData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data\LicenseInterface $licenseData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->licenseData = $licenseData;
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = __('<tr><th align="left" colspan="2">License</th></tr>');
        $html .= __('<tr><td>Current Domain:</td><td>%1</td></tr>', $this->licenseData->getDomain());
        $html .= __('<tr><td>Cart2Quote License:</td><td>%1</td></tr>', $this->getCart2QuoteLicenseHtml());
        $html .= __('<tr><td>Cart2Quote Status:</td><td>%1</td></tr>', $this->getCart2QuoteEditionHtml());
        $html .= __('<tr><td></td></tr>');
        $html .= $this->_renderValue($element);

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Get the Cart2Quote license
     *
     * @return string
     */
    public function getCart2QuoteLicenseHtml()
    {
        return __(ucfirst($this->licenseData->getLicenseType()));
    }

    /**
     * Get the Cart2Quote edition
     *
     * @return string
     */
    public function getCart2QuoteEditionHtml()
    {
        $color = '#000000';
        if ($this->licenseData->isActiveState()) {
            $color = '#009900';
        } elseif ($this->licenseData->isInactiveState()) {
            $color = '#b30000';
        } elseif ($this->licenseData->isPendingState() || $this->licenseData->isUnreachableState()) {
            $color = '#eb5202';
        }

        return __('<b style="color:%1;">%2</b>', $color, ucfirst($this->licenseData->getLicenseState()));
    }
}