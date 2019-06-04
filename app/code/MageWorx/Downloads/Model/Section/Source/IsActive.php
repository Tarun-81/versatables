<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model\Section\Source;

use MageWorx\Downloads\Model\Section;
use MageWorx\Downloads\Model\Source;

class IsActive extends Source
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Section::STATUS_ENABLED,
                'label' => __('Yes')
            ],[
                'value' => Section::STATUS_DISABLED,
                'label' => __('No')
            ],
        ];
    }
}
