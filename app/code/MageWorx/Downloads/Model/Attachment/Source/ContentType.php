<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model\Attachment\Source;

use MageWorx\Downloads\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class ContentType extends Source
{
    const CONTENT_FILE = 1;
    const CONTENT_URL  = 2;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CONTENT_FILE,
                'label' => __('File')
            ],
            [
                'value' => self::CONTENT_URL,
                'label' => __('URL')
            ],
        ];
    }
}
