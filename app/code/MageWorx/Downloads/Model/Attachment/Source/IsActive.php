<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model\Attachment\Source;

use MageWorx\Downloads\Model\Attachment;
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
                'value' => Attachment::STATUS_ENABLED,
                'label' => __('Yes')
            ],[
                'value' => Attachment::STATUS_DISABLED,
                'label' => __('No')
            ],
        ];
    }
}
