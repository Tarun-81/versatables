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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

use Cart2Quote\Quotation\Model\Quote;
use Magento\Framework\View\Element\Message;
use Magento\Framework\View\Element\Template;

/**
 * Quote view messages
 */
class Messages extends \Magento\Framework\View\Element\Messages
{
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Message\Factory $messageFactory
     * @param \Magento\Framework\Message\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param Message\InterpretationStrategyInterface $interpretationStrategy
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Message\InterpretationStrategyInterface $interpretationStrategy,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );
    }

    /**
     * Retrieve quote model instance
     * @return Quote
     */
    protected function _getQuote()
    {
        return $this->coreRegistry->registry('current_quote');
    }

    /**
     * Preparing global layout
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->messageManager->getMessages(true));
        return parent::_prepareLayout();
    }
}
