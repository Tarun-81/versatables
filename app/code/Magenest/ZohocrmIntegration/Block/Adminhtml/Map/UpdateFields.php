<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ZohocrmIntegration
 * @author   ThaoPV
 */
namespace  Magenest\ZohocrmIntegration\Block\Adminhtml\Map;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class UpdateFields
 *
 * @package Magenest\ZohocrmIntegration\Block\Adminhtml\Map
 */
class UpdateFields extends Template
{
    /**
     * Constructor
     *
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('zohocrm/field/retrieve', ['_current' => false]);
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUpdateAllFields()
    {
        return $this->getUrl('zohocrm/field/updateAllFields', ['_current' => false]);
    }
}
