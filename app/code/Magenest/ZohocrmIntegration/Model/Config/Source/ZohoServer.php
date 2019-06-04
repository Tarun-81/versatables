<?php
/**
 * Created by PhpStorm.
 * User: canhnd
 * Date: 13/02/2017
 * Time: 10:49
 */
namespace Magenest\ZohocrmIntegration\Model\Config\Source;

/**
 * Class Edition
 * @package Magenest\ZohocrmIntegration\Model\Config\Source
 */
class ZohoServer implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = [
        '.com' => 'COM',
        '.eu' => 'EU'

    ];

    /**
     * Return options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_options;
        return $options;
    }
}
