<?php

namespace Magenest\ZohocrmIntegration\Block\Adminhtml\Map;

use Magento\Framework\View\Element\Template;
use Magenest\ZohocrmIntegration\Model\FieldFactory;
use Magenest\ZohocrmIntegration\Model\MapFactory;
use Magento\Backend\Model\Url;

class NewMapping extends Template
{
    protected $urlBackend;

    protected $_mapFactory;

    protected $_fieldFactory;

    protected $layoutProcessors;

    public function __construct(
        Template\Context $context,
        FieldFactory $fieldFactory,
        MapFactory $mapFactory,
        array $layoutProcessors = [],
        array $data = []
    ) {
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->layoutProcessors = $layoutProcessors;
        $this->_mapFactory        = $mapFactory;
        $this->_fieldFactory = $fieldFactory;
        parent::__construct($context, $data);
    }

    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        if (!$this->jsLayout) {
            $this->jsLayout = [
                'components' => [
                    'mapping' => [
                        'component' => 'Magenest_ZohocrmIntegration/js/view/mapping',
                        'displayArea' => 'mapping'
                    ]
                ]
            ];
        }
        return \Zend_Json::encode($this->jsLayout);
    }

    public function getConfig()
    {
        return [
            'Types' => $this->getTypes(),
            'SaveMappingUrl' => $this->getSaveMappingUrl(),
            'GetMappingFieldUrl' => $this->getGetMappingFieldUrl(),
        ];
    }

    public function getTypes()
    {
        $model = $this->_fieldFactory->create();
        $types = array_keys($model->getAllTable());
        return $types;
    }

    public function getSaveMappingUrl()
    {
        $url = $this->getUrl('zohocrm/map/savemapping');
        return $url;
    }

    public function getGetMappingFieldUrl()
    {
        $url = $this->getUrl('zohocrm/field/ajaxgetfields');
        return $url;
    }

}