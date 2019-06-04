<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Setup;

use Cart2Quote\Quotation\Model\SalesSequence\Config as SequenceConfig;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\SalesSequence\Model\Builder;

/**
 * Class InstallData
 * @package Cart2Quote\Quotation\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * Sales setup factory
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var Builder
     */
    protected $sequenceBuilder;

    /**
     * @var SequenceConfig
     */
    protected $sequenceConfig;

    /**
     * Category setup factory
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * Init
     * @param SalesSetupFactory $salesSetupFactory
     * @param Builder $sequenceBuilder
     * @param SequenceConfig $sequenceConfig
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        Builder $sequenceBuilder,
        SequenceConfig $sequenceConfig,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->sequenceBuilder = $sequenceBuilder;
        $this->sequenceConfig = $sequenceConfig;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create();

        /**
         * Install eav entity types to the eav/entity_type table
         */
        $salesSetup->installEntities();

        $defaultStoreIds = [0, 1];
        foreach ($defaultStoreIds as $storeId) {
            foreach ($salesSetup->getDefaultEntities() as $entityType => $entity) {
                $this->sequenceBuilder->setPrefix($this->sequenceConfig->get('prefix'))
                    ->setSuffix($this->sequenceConfig->get('suffix'))
                    ->setStartValue($this->sequenceConfig->get('startValue'))
                    ->setStoreId($storeId)
                    ->setStep($this->sequenceConfig->get('step'))
                    ->setWarningValue($this->sequenceConfig->get('warningValue'))
                    ->setMaxValue($this->sequenceConfig->get('maxValue'))
                    ->setEntityType($entityType)
                    ->create();
            }
        }

        /** @var \Magento\Catalog\Setup\CategorySetup $catalogSetup */
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $groupName = 'Product Details';
        $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $catalogSetup->getAttributeSetId($entityTypeId, 'Default');

        /* Add hide price attribute */
        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cart2quote_quotable',
            [
                'group' => $groupName,
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                'frontend' => '',
                'label' => 'Quotable',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'input_renderer' => 'Cart2Quote\Quotation\Block\Adminhtml\Form\QuotableConfig',
                'visible_on_front' => false,
            ]
        );

        $attribute = $catalogSetup->getAttribute($entityTypeId, 'cart2quote_quotable');
        if ($attribute) {
            $catalogSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $groupName,
                $attribute['attribute_id'],
                22
            );
        }

        if (!$catalogSetup->getAttributesNumberInGroup($entityTypeId, $attributeSetId, 'Quotable')) {
            $catalogSetup->removeAttributeGroup($entityTypeId, $attributeSetId, 'Quotable');
        }

        /**
         * Install statuses and states
         */

        /**
         * Install quote statuses from config
         */
        $data = [];

        $statuses = [
            'open' => __('Open'),
            'new' => __('New'),
            'processing' => __('In Process'),
            'change_request' => __('Change Request'),
            'quote_available' => __('Quote Available'),
            'holded' => __('On Hold'),
            'waiting_supplier' => __('Waiting for supplier'),
            'canceled' => __('Canceled'),
            'out_of_stock' => __('Out of Stock'),
            'proposal_expired' => __('Proposal Expired'),
            'pending' => __('Pending'),
            'proposal_sent' => __('Proposal sent'),
            'ordered' => __('Ordered'),
            'accepted' => __('Accepted'),
            'closed' => __('Closed'),
        ];
        foreach ($statuses as $code => $info) {
            $data[] = ['status' => $code, 'label' => $info];
        }

        $query = $setup->getConnection()->query('SELECT * FROM ' . $setup->getTable('quotation_quote_status'));
        if ($query->rowCount() == 0) {
            $setup->getConnection()->insertOnDuplicate(
                $setup->getTable('quotation_quote_status'),
                $data,
                ['status', 'label']
            );
        }

        /**
         * Install quote states from config
         */
        $data = [];
        $states = [
            'open' => [
                'label' => __('Open'),
                'statuses' => [
                    'open' => ['default' => '0'],
                    'new' => ['default' => '1'],
                    'change_request' => ['default' => '0'],
                    'processing' => ['default' => '0'],
                ],
                'visible_on_front' => true,
            ],
            'holded' => [
                'label' => __('On Hold'),
                'statuses' => [
                    'holded' => ['default' => '1'],
                    'waiting_supplier' => ['default' => '0'],
                ],
                'visible_on_front' => true,
            ],
            'canceled' => [
                'label' => __('Canceled'),
                'statuses' => [
                    'canceled' => ['default' => '1'],
                    'out_of_stock' => ['default' => '0'],
                    'proposal_expired' => ['default' => '0'],
                ],
                'visible_on_front' => true,
            ],
            'pending' => [
                'label' => __('Pending'),
                'statuses' => [
                    'pending' => ['default' => '1'],
                    'proposal_sent' => ['default' => '0'],
                    'quote_available' => ['default' => '0'],
                ],
                'visible_on_front' => true,
            ],
            'completed' => [
                'label' => __('Completed'),
                'statuses' => [
                    'ordered' => ['default' => '0'],
                    'accepted' => ['default' => '1'],
                    'closed' => ['default' => '0'],
                ],
                'visible_on_front' => true,
            ],
        ];

        foreach ($states as $code => $info) {
            if (isset($info['statuses'])) {
                foreach ($info['statuses'] as $status => $statusInfo) {
                    $isDefault = 0;
                    if (is_array($statusInfo) && isset($statusInfo['default'])) {
                        $isDefault = $statusInfo['default'];
                    }

                    $data[] = [
                        'status' => $status,
                        'state' => $code,
                        'is_default' => $isDefault,
                    ];
                }
            }
        }

        $setup->getConnection()->insertOnDuplicate(
            $setup->getTable('quotation_quote_status_state'),
            $data,
            ['status', 'state', 'is_default']
        );

        /** Update visibility for states */
        foreach ($states as $index => $state) {
            $setup->getConnection()->update(
                $setup->getTable('quotation_quote_status_state'),
                ['visible_on_front' => $state['visible_on_front']],
                ['state = ?' => $index]
            );
        }
    }
}
