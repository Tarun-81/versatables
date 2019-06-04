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
 * Class Suggest
 * @package Mageplaza\Osc\Model\System\Config\Source\Address
 */
class AddressSuggest
{
    /**
     * @return array
     */
    public function getTriggerOption()
    {
        return [
            ''       => __('No'),
            'google' => __('Google'),
//			'pca'    => __('Capture+ by PCA Predict'),
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getTriggerOption() as $code => $label) {
            $options[] = [
                'value' => $code,
                'label' => $label
            ];
        }

        return $options;
    }
}
