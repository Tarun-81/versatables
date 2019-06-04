<?php
/**
 * Created by PhpStorm.
 * User: canhnd
 * Date: 09/02/2017
 * Time: 10:37
 */
namespace Magenest\ZohocrmIntegration\Model\Config\Source;

/**
 * Class SyncMode
 * @package Magenest\ZohocrmIntegration\Model\Config\Source
 */
class SyncMode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = [
        1 => 'Sync Manually',
        2 => 'Sync Every',
        3 => 'Sync Daily',
    ];

    /**
     * Return options array
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_options;
        return $options;
    }
}
