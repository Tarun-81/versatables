<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @copyright   Copyright (c) 2016 Mageplaza (http://mageplaza.com/)
 * @license     http://mageplaza.com/license-agreement.html
 */
namespace Mageplaza\Osc\Model\System\Config\Source;

/**
 * Class Layout
 * @package Mageplaza\Osc\Model\System\Config\Source
 */
class Layout
{
    const ONE_COLUMN = '1column';
    const TWO_COLUMNS = '2columns';
    const THREE_COLUMNS = '3columns';
    const THREE_COLUMNS_WITH_COLSPAN = '3columns-colspan';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('1 Column'),
                'value' => self::ONE_COLUMN
            ],
            [
                'label' => __('2 Columns'),
                'value' => self::TWO_COLUMNS
            ],
            [
                'label' => __('3 Columns'),
                'value' => self::THREE_COLUMNS
            ],
            [
                'label' => __('3 Columns With Colspan'),
                'value' => self::THREE_COLUMNS_WITH_COLSPAN
            ]
        ];

        return $options;
    }
}
