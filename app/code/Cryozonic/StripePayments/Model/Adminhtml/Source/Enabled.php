<?php

namespace Cryozonic\StripePayments\Model\Adminhtml\Source;

class Enabled
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => false,
                'label' => __('Disabled')
            ),
            array(
                'value' => true,
                'label' => __('Enabled')
            )
        );
    }
}
