<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model\Attachment\Source;

use MageWorx\Downloads\Model\Section as SectionModel;
use MageWorx\Downloads\Model\Source;

class Section extends Source
{
    /**
     *
     * @param SectionModel $section
     */
    public function __construct(SectionModel $section)
    {
        $this->section = $section;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $array = $this->section->getSectionList();
        $optionArray = [];

        if (!empty($array)) {
            foreach ($array as $key => $value) {
                $optionArray[] = ['label' => $value, 'value' => $key];
            }
        }

        return $optionArray;
    }
}
