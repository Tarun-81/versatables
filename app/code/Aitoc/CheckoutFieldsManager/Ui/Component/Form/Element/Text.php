<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Ui\Component\Form\Element;

use Magento\Ui\Component\Form\Element\AbstractElement;

class Text extends AbstractElement
{
    const NAME = 'label';

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }
}
