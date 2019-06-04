<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CustomerDataCheckoutAttribute;

use Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CustomerDataCheckoutAttribute;

class Edit extends CustomerDataCheckoutAttribute
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_CheckoutFieldsManager::actions_edit';

    /**
     * Edit order address form
     *
     * @inheritdoc
     */
    public function execute()
    {
        return $this->getResult();
    }
}
