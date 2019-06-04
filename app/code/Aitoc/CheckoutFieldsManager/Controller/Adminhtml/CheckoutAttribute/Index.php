<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

use Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

class Index extends CheckoutAttribute
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultPage = $this->createActionPage();
        /** @var \Aitoc\CheckoutFieldsManager\Block\Adminhtml\CheckoutAttribute $block */
        $block = $resultPage->getLayout()->createBlock('Aitoc\CheckoutFieldsManager\Block\Adminhtml\CheckoutAttribute');
        $resultPage->addContent($block);

        return $resultPage;
    }
}
