<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ZohocrmIntegration
 * @author   ThaoPV
 */
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Field;

use Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use Magenest\ZohocrmIntegration\Model\FieldFactory;

class Retrieve extends Action
{
    /**
     * @var \Magenest\ZohocrmIntegration\Model\FieldFactory
     */
    protected $_fieldFactory;

    /**
     * @param \Magento\Backend\App\Action\Context  $context
     * @param \Magenest\ZohocrmIntegration\Model\FieldFactory $fieldFactory
     */
    public function __construct(
        Context $context,
        FieldFactory $fieldFactory
    ) {
        parent::__construct($context);
        $this->_fieldFactory = $fieldFactory;
    }

    /**
     * Execute
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $type = $data['type'];
            $out  = [];
            $out['zohocrm_options'] = '';
            $out['magento_options']    = '';
            if ($type) {
                $model = $this->_fieldFactory->create();
                $model->loadByTable($type);
                $magentoFields = $model->getMagentoFields();

                $magentoOption = '';

                if ($magentoFields) {
                    foreach ($magentoFields as $value => $label) {
                        $magentoOption .= "<option value ='$value' >".$label."</option>";
                    }
                }

                $out['magento_options'] = $magentoOption;
                $zohoFields       = $model->getZohoFields();
                $zohoOption       = '';

                if ($zohoFields) {
                    foreach ($zohoFields as $value => $label) {
                        $zohoOption .= "<option value ='$value' >".$label."</option>";
                    }
                }

                $out['zohocrm_options'] = $zohoOption;
            }
            $this->getResponse()->setBody(json_encode($out));
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::mapping');
    }
}
