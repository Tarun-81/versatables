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

use Magenest\ZohocrmIntegration\Model\FieldFactory;
use Magento\Backend\App\Action;

/**
 * Class UpdateAllFields
 *
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Field
 */
class UpdateAllFields extends Action
{
    /**
     * @var \Magenest\ZohocrmIntegration\Model\FieldFactory
     */
    protected $_fieldFactory;


    /**
     * UpdateAllFields constructor.
     * @param Action\Context $context
     * @param FieldFactory $fieldFactory
     */
    public function __construct(
        Action\Context $context,
        FieldFactory $fieldFactory
    ) {
        $this->_fieldFactory = $fieldFactory;
        parent::__construct($context);
    }

    /**
     * Execute
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $model = $this->_fieldFactory->create();
            $table = $model->getAllTable();
            foreach ($table as $s_table => $m_table) {
                $model = $this->_fieldFactory->create();
                $model->loadByTable($s_table, true);
            }
        }

        return;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::mapping');
    }
}
