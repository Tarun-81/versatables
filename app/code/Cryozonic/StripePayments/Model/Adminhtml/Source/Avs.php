<?php

namespace Cryozonic\StripePayments\Model\Adminhtml\Source;

class Avs
{
    public function toOptionArray()
    {
        return [
            [
                'value' => false,
                'label' => __('Disabled')
            ],
            [
                'value' => true,
                'label' => __('Enabled')
            ],
        ];
    }
}
