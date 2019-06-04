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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote;

/**
 * Adminhtml quotation quote view
 *
 * Class View
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Block group
     * @var string
     */
    protected $_blockGroup = 'Cart2Quote_Quotation';

    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Editable Status
     * @var array
     */
    protected $_editableStatus = [
        'proposal_sent',
        'proposal_expired',
        'ordered'
    ];

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get header text
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $_extQuoteId = $this->getQuote()->getExtQuoteId();
        if ($_extQuoteId) {
            $_extQuoteId = '[' . $_extQuoteId . '] ';
        } else {
            $_extQuoteId = '';
        }

        return __(
            'Quote # %1 %2 | %3',
            $this->getQuote()->getRealQuoteId(),
            $_extQuoteId,
            $this->formatDate(
                $this->_localeDate->date(new \DateTime($this->getQuote()->getQuotationCreatedAt())),
                \IntlDateFormatter::MEDIUM,
                true
            )
        );
    }

    /**
     * Retrieve quote model object
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * Hold URL getter
     * @return string
     */
    public function getHoldUrl()
    {
        return $this->getUrl('quotation/*/hold');
    }

    /**
     * URL getter
     * @param string $params
     * @param array $params2
     * @return string
     */
    public function getUrl($params = '', $params2 = [])
    {
        $params2['quote_id'] = $this->getQuoteId();

        return parent::getUrl($params, $params2);
    }

    /**
     * Retrieve Quote Identifier
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getQuote() ? $this->getQuote()->getId() : null;
    }

    /**
     * Comment URL getter
     * @return string
     */
    public function getCommentUrl()
    {
        return $this->getUrl('quotation/*/comment');
    }

    /**
     * Return back url for view grid
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getQuote() && $this->getQuote()->getBackUrl()) {
            return $this->getQuote()->getBackUrl();
        }

        return $this->getUrl('quotation/*/');
    }

    /**
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'quotation_id';
        $this->_controller = 'adminhtml_quote';
        $this->_mode = 'view';

        parent::_construct();

        $this->setId('quotation_quote_view');
        $quote = $this->getQuote();

        if (!$quote) {
            return;
        }

        $this->buttonList->remove('save');

        $this->addButton(
            'sections_popup_id',
            [
                'label' => __('Edit sections'),
                'class' => 'edit action-secondary',
                'onclick' => 'jQuery(this).popup("showDialog");',
                'data_attribute' => [
                    'mage-init' => [
                        'Cart2Quote_Quotation/quote/section/popup' => [
                            'elementId' => '#section-grid',
                            'response' => true
                        ]
                    ]
                ]
            ],
            1
        );

        if (in_array($quote->getStatus(), $this->_editableStatus)) {
            $this->addButton(
                'edit',
                [
                    'label' => __('Edit Quote'),
                    'class' => 'edit primary',
                    'onclick' => 'quote.edit("' . $this->getEditUrl() . '");'
                ],
                1
            );
        }

        $this->addButton(
            'save',
            [
                'label' => __('Save'),
                'class' => 'save primary',
                'onclick' => 'quote.submit();'
            ],
            1
        );
    }

    /**
     * Edit URL getter
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl('quotation/quote/edit');
    }

    /**
     * Check permission for passed action
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * @return \Magento\Framework\Phrase
     * @internal param \Cart2Quote\Quotation\Model\Quote $quote
     */
    protected function getEditMessage()
    {
        return __('Are you sure? This quote will be canceled and a new one will be created instead.');
    }
}
