<?php

namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Field;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magenest\ZohocrmIntegration\Model\FieldFactory;
use Magenest\ZohocrmIntegration\Model\MapFactory;
use Symfony\Component\Config\Definition\Exception\Exception;

class AjaxGetFields extends Action
{
    protected $_fieldFactory;

    protected $_jsonFactory;

    protected $_mapFactory;

    public function __construct(
        JsonFactory $jsonFactory,
        FieldFactory $fieldFactory,
        MapFactory $mapFactory,
        Context $context
    ) {
        $this->_mapFactory = $mapFactory;
        $this->_jsonFactory = $jsonFactory;
        $this->_fieldFactory = $fieldFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $_model = $this->_fieldFactory->create();
        $mapCollection = $this->_mapFactory->create()->getCollection();
        $out = [];

        if ($this->getRequest()->isAjax()) {
            $data = $this->getRequest()->getParam('type');
            $mappedFields = $mapCollection->addFieldToFilter('type', $data)->getData();

            $_model->loadByTable($data, true);
            $zohoFields = $_model->getZohoFields($data);
            $magentoFields = $_model->getMagentoFields($data);
            $out['magento_fields'] = $magentoFields;
            $out['zoho_fields'] = $zohoFields;
            $out['mapped'] = $mappedFields;
            $this->getResponse()->setBody(json_encode($out));

        } else {
            return false;
        }
    }
}