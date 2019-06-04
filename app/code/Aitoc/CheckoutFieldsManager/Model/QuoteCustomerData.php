<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model;

use Aitoc\CheckoutFieldsManager\Api\Data\QuoteCustomerDataInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class QuoteCustomerData extends AbstractCustomerData implements QuoteCustomerDataInterface
{
    /**
     * Name of the module.
     */
    const MODULE_NAME = 'Aitoc_CheckoutFieldsManager';

    const TYPE = 'aitoc_checkoutfieldsmanager_quotecustomerdata';

    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'value_id';

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('Aitoc\CheckoutFieldsManager\Model\ResourceModel\QuoteCustomerData');
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteId()
    {
        return $this->getData(self::KEY_QUOTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteId($quoteId)
    {
        $this->setData(self::KEY_QUOTE_ID, $quoteId);

        return $this;
    }
}
