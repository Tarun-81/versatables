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
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\SalesSequence\Model\Builder;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Upgrade Data script
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Cart2Quote\Quotation\Model\SalesSequence\EntityPool
     */
    protected $entityPool;

    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @var Builder
     */
    protected $sequenceBuilder;

    /**
     * @var SequenceConfig
     */
    protected $sequenceConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Sales setup factory
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    private $configResourceModel;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quotationCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * UpgradeData constructor.
     *
     * @param \Cart2Quote\Quotation\Model\SalesSequence\EntityPool $entityPool
     * @param CategorySetupFactory $categorySetupFactory
     * @param Builder $sequenceBuilder
     * @param SequenceConfig $sequenceConfig
     * @param StoreManagerInterface $storeManager
     * @param SalesSetupFactory $salesSetupFactory
     * @param \Magento\Config\Model\ResourceModel\Config $configResourceModel
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\SalesSequence\EntityPool $entityPool,
        CategorySetupFactory $categorySetupFactory,
        Builder $sequenceBuilder,
        SequenceConfig $sequenceConfig,
        StoreManagerInterface $storeManager,
        SalesSetupFactory $salesSetupFactory,
        \Magento\Config\Model\ResourceModel\Config $configResourceModel,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $state
    ) {
        $this->entityPool = $entityPool;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->sequenceBuilder = $sequenceBuilder;
        $this->sequenceConfig = $sequenceConfig;
        $this->storeManager = $storeManager;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->configResourceModel = $configResourceModel;
        $this->quotationCollectionFactory = $collectionFactory;
        $this->logger = $logger;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $catalogSetup->updateAttribute($entityTypeId, 'cart2quote_quotable', 'is_used_in_grid', true);
            $catalogSetup->updateAttribute($entityTypeId, 'cart2quote_quotable', 'used_in_product_listing', true);
        }

        if (version_compare($context->getVersion(), '1.0.11') < 0) {
            $defaultStoreIds = [0, 1];
            $storeIds = array_keys($this->storeManager->getStores(true));
            foreach ($storeIds as $storeId) {
                if (in_array($storeId, $defaultStoreIds)) {
                    //already done in installData
                    continue;
                }
                foreach ($this->entityPool->getEntities() as $entityType) {
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
        }

        if (version_compare($context->getVersion(), '1.0.19', '<')) {
            /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
            $salesSetup = $this->salesSetupFactory->create();

            $salesSetup->updateEntityType(
                'quote',
                'entity_model',
                'Cart2Quote\Quotation\Model\ResourceModel\Quote'
            );
        }

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            $dataStatuses = [];
            $dataStateStatusRelation = [];

            $changedStatuses = [
                'open' => 'Open',
                'new' => 'Open, New',
                'processing' => 'Open, In Process',
                'change_request' => 'Open, Change Request',
                'holded' => 'On Hold',
                'waiting_supplier' => 'On Hold, Waiting for supplier',
                'canceled' => 'Canceled',
                'out_of_stock' => 'Canceled, Out of Stock',
                'pending' => 'Pending',
                'proposal_sent' => 'Pending, Proposal sent',
                'ordered' => 'Completed, Ordered',
                'accepted' => 'Completed, Accepted',
                'closed' => 'Closed',
                'quote_available' => 'Pending, Quote Available',
                'proposal_expired' => 'Pending, Proposal Expired'
            ];

            $newStatuses = [
                'proposal_sent_completed' => 'Completed, Proposal sent',
                'out_of_stock_holded' => 'On Hold, Out of Stock'
            ];

            $newStatusSortNumbers = [
                'proposal_sent_completed' => 550,
                'out_of_stock_holded' => 650
            ];

            $newStatusStates = [
                'proposal_sent_completed' => 'completed',
                'out_of_stock_holded' => 'holded'
            ];

            $setup->getConnection()->addColumn($setup->getTable('quotation_quote_status'), 'sort', 'int');

            asort($changedStatuses);
            $sortNumber = 0;

            foreach ($changedStatuses as $changedStatus => $label) {
                $sortNumber += 100;
                $setup->getConnection()->update(
                    $setup->getTable('quotation_quote_status'),
                    [
                        'label' => $label,
                        'sort' => $sortNumber
                    ],
                    [
                        "status = ?" => $changedStatus
                    ]
                );
            }

            foreach ($newStatuses as $newStatus => $label) {
                $dataStatuses[] = [
                    'status' => $newStatus,
                    'label' => $label,
                    'sort' => $newStatusSortNumbers[$newStatus]
                ];

                $dataStateStatusRelation[] = [
                    'status' => $newStatus,
                    'state' => $newStatusStates[$newStatus],
                    'is_default' => '1',
                    'visible_on_front' => '1'
                ];
            }

            $query = $setup->getConnection()->query('SELECT * FROM ' . $setup->getTable('quotation_quote_status'));
            if ($query->rowCount() == 0) {
                $setup->getConnection()->insertOnDuplicate(
                    $setup->getTable('quotation_quote_status'),
                    $dataStatuses,
                    [
                        'status',
                        'label',
                        'sort'
                    ]
                );
            }

            $query = $setup->getConnection()->query('SELECT * FROM ' . $setup->getTable('quotation_quote_status_state'));
            if ($query->rowCount() == 0) {
                $setup->getConnection()->insertOnDuplicate(
                    $setup->getTable('quotation_quote_status_state'),
                    $dataStateStatusRelation,
                    [
                        'status',
                        'state',
                        'is_default',
                        'visible_on_front'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
            $salesSetup = $this->salesSetupFactory->create();

            $salesSetup->updateEntityType(
                'quote',
                'increment_pad_length',
                9
            );
        }

        if (version_compare($context->getVersion(), '1.1.3') < 0) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $catalogSetup->updateAttribute($entityTypeId, 'cart2quote_quotable', 'backend_model', null);
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'cart2quote_quotable',
                'source_model',
                'Cart2Quote\Quotation\Model\Config\Backend\Product\Quotable'
            );
        }

        if (version_compare($context->getVersion(), '1.2.2') < 0) {
            $dataStatuses = [];
            $dataStateStatusRelation = [];

            $newStatuses = [
                'auto_proposal_sent' => 'Pending, Auto Proposal Sent'
            ];

            $newStatusSortNumbers = [
                'auto_proposal_sent' => 1250
                // The sort number of status "pending" is 1200, the sort number of status "proposal_expired" is 1300. Finally, status "auto_proposal_sent" is inserted between status "pending" and "proposal_expired".
            ];

            $newStatusStates = [
                'auto_proposal_sent' => 'pending'
            ];

            foreach ($newStatuses as $newStatus => $label) {
                $dataStatuses[] = [
                    'status' => $newStatus,
                    'label' => $label,
                    'sort' => $newStatusSortNumbers[$newStatus]
                ];

                $dataStateStatusRelation[] = [
                    'status' => $newStatus,
                    'state' => $newStatusStates[$newStatus],
                    'is_default' => '1',
                    'visible_on_front' => '1'
                ];
            }

            $setup->getConnection()->insertOnDuplicate(
                $setup->getTable('quotation_quote_status'),
                $dataStatuses,
                [
                    'status',
                    'label',
                    'sort'
                ]
            );

            $setup->getConnection()->insertOnDuplicate(
                $setup->getTable('quotation_quote_status_state'),
                $dataStateStatusRelation,
                [
                    'status',
                    'state',
                    'is_default',
                    'visible_on_front'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $dataStatuses = [];
            $dataStateStatusRelation = [];

            $changedStatuses = [
                'ordered' => 'Accepted, Ordered',
                'accepted' => 'Accepted',
            ];

            $setup->getConnection()->addColumn($setup->getTable('quotation_quote_status'), 'sort', 'int');

            asort($changedStatuses);
            $sortNumber = 0;

            foreach ($changedStatuses as $changedStatus => $label) {
                $sortNumber += 100;
                $setup->getConnection()->update(
                    $setup->getTable('quotation_quote_status'),
                    [
                        'label' => $label,
                        'sort' => $sortNumber
                    ],
                    [
                        "status = ?" => $changedStatus
                    ]
                );
            }

            $setup->getConnection()->update(
                $setup->getTable('core_config_data'),
                ['path' => 'cart2quote_pdf/quote/pdf_footer_text'],
                ['path = ?' => 'cart2quote_quotation/global/pdf_footer_text']
            );
        }

        if (version_compare($context->getVersion(), '2.0.3.1') < 0) {
            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $catalogSetup->updateAttribute($entityTypeId, 'cart2quote_quotable', 'default_value', '2');
        }

        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            $connection = $setup->getConnection();
            $select = $connection->select()
                ->joinLeft(
                    ['join_table' => $setup->getTable('quote')],
                    "`main_table`.`quote_id` = `join_table`.`entity_id`",
                    array('quotation_created_at' => 'created_at')
                );

            $query = $connection->updateFromSelect(
                $select,
                ['main_table' => $setup->getTable('quotation_quote')]
            );
            $connection->query($query);
        }

        if (version_compare($context->getVersion(), '2.1.1.1') < 0) {
            /** @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $collection */
            $collection = $this->quotationCollectionFactory->create();
            $collection->join(['q' => $setup->getTable('quote')], 'main_table.quote_id = q.entity_id');
            $collection->addFieldToSelect('main_table.quote_id');
            $collection->addFieldToFilter('main_table.is_quote', ['eq' => 1]);
            $collection->addFieldToFilter('q.is_quotation_quote', ['neq' => true]);
            $ids = $collection->getAllIds();

            if (is_array($ids)) {
                $connection = $setup->getConnection();
                $connection->update(
                    $setup->getTable('quote'),
                    ['is_quotation_quote' => true],
                    ['entity_id IN (?)' => $ids]
                );
            }
        }

        $setup->endSetup();
    }
}
