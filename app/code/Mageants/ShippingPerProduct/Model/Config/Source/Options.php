<?php
/**
 * @category   Mageants ShippingPerProduct
 * @package    Mageants_ShippingPerProduct
 * @copyright  Copyright (c) 2016 Mageants
 * @author     Mageants Team <support@mageants.com>
 */

namespace Mageants\ShippingPerProduct\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;

/**
 * Custom Attribute Renderer
 */
class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var OptionFactory
     */
    private $optionFactory;

   /**
    * Get all options** @return array
    */
    public function getAllOptions()
    {
        $this->_options=[['label'=>'Per Product Quantity', 'value'=>'0'],
        ['label'=>'As Whole Product', 'value'=>'1'],];
        return $this->_options;
    }
}
