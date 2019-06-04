<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace  MageWorx\Downloads\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use MageWorx\Downloads\Model\SectionFactory;

class SectionText extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var SectionFactory
     */
    protected $sectionFactory;

    /**
     * SectionText constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SectionFactory $sectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SectionFactory $sectionFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->sectionFactory = $sectionFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');
        $sourceFieldName = 'section_id';

        $options = $this->sectionFactory->create()->getSectionList();

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$fieldName] = $options[$item[$sourceFieldName]];
        }

        return $dataSource;
    }
}