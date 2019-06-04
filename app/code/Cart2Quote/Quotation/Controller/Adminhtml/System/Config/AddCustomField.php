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

namespace Cart2Quote\Quotation\Controller\Adminhtml\System\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Adminhtml quotation add custom field controller
 */
class AddCustomField extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * AddCustomField constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Cart2Quote\Quotation\Helper\Data $dataHelper
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Cart2Quote\Quotation\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_resourceConfig = $resourceConfig;
        $this->_scopeConfig = $scopeConfig;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->_resultJsonFactory->create();

        //TODO retrieve selected scope
        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $scopeCode = 0;

        $customFields = $this->_dataHelper->getCustomFieldsConfig($scopeType, $scopeCode);
        $index = 0;
        foreach ($customFields as $key => $customField) {
            if ($index !== $key) {
                break;
            }
            $index++;
        }
        $type = $this->getRequest()->getParam('type', 'text');
        $this->addType($type, $index, $scopeType, $scopeCode);
        return $result->setData(['success' => true]);
    }

    protected function addType($type, $id, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        //TODO make OOP
        switch ($type) {
            case 'text':
                $this->_resourceConfig->saveConfig('cart2quote_quote_form_settings/quote_form_custom_field_' . $id . '/type',
                    'text', $scopeType, $scopeCode);
                $this->_resourceConfig->saveConfig('cart2quote_quote_form_settings/quote_form_custom_field_' . $id . '/label',
                    'text', $scopeType, $scopeCode);
                break;
            case 'textarea':
                $this->_resourceConfig->saveConfig('cart2quote_quote_form_settings/quote_form_custom_field_' . $id . '/type',
                    'textarea', $scopeType, $scopeCode);
                $this->_resourceConfig->saveConfig('cart2quote_quote_form_settings/quote_form_custom_field_' . $id . '/label',
                    'text', $scopeType, $scopeCode);
                break;
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;//$this->_authorization->isAllowed('Cart2Quote_Quotation::add_custom_field');
    }
}
