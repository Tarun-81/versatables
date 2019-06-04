<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model\Attachment\Source;

use MageWorx\Downloads\Model\Source;

class FileSize extends Source
{
    const FILE_SIZE_PRECISION_AUTO = 1;
    const FILE_SIZE_PRECISION_KILO = 2;
    const FILE_SIZE_PRECISION_MEGA = 3;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::FILE_SIZE_PRECISION_AUTO,
                'label' => __('Auto')
            ],
            [
                'value' => self::FILE_SIZE_PRECISION_KILO,
                'label' => __('Kilobytes')
            ],
            [
                'value' => self::FILE_SIZE_PRECISION_MEGA,
                'label' => __('Megabytes')
            ],
        ];
    }
}
