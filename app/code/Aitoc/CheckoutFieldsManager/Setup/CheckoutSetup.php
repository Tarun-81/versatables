<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Setup;

use Magento\Eav\Setup\EavSetup;

class CheckoutSetup extends EavSetup
{
    /**
     * Retrieve default entities: aitoc_checkout
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = [
            'aitoc_checkout' => [
                'entity_model' => 'Aitoc\CheckoutFieldsManager\Model\ResourceModel\Order',
                'attribute_model' => 'Aitoc\CheckoutFieldsManager\Model\Attribute',
                'table' => 'sales_order',
                'table_prefix' => null,
                'id_field' => null,
                'increment_model' => 'Magento\Eav\Model\Entity\Increment\NumericValue',
                'increment_per_store' => 1,
                'additional_attribute_table' => 'aitoc_checkout_eav_attribute',
                'entity_attribute_collection' => 'Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\Collection'
            ]
        ];

        return $entities;
    }
}
