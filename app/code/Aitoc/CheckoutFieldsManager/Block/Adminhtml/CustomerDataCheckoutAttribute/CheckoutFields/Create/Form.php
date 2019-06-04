<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\CustomerDataCheckoutAttribute\CheckoutFields\Create;

use Aitoc\CheckoutFieldsManager\Block\Adminhtml\GeneralForm;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Form extends GeneralForm
{
    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        array $data
    ) {
        parent::__construct($context, $registry, $formFactory, $yesNo, $data);
        $this->setLegend('Add Checkout Fields');
    }
}
