<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\Table;

class Checkbox extends Table
{
    /**
     * Retrieve all options array
     *
     * @param bool $withEmpty
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $options = parent::getAllOptions();
        $newOptions = [];
        foreach ($options as $item) {
            if ($item['value']) {
                $newOptions[] = $item;
            }
        }

        return $newOptions;
    }
}
