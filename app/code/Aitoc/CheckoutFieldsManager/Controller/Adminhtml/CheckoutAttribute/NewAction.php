<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

use Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

class NewAction extends CheckoutAttribute
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
