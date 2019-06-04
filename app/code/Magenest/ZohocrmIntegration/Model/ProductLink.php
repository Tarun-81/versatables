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
namespace Magenest\ZohocrmIntegration\Model;

use Magento\Framework\Model\AbstractModel;


class ProductLink extends AbstractModel
{
    protected $_eventPrefix = 'link_entity';

    public function _construct()
    {
        $this->_init('magenest_zohocrm_link_entity');
    }
}