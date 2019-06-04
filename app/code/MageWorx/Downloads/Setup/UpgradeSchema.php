<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use MageWorx\Downloads\Model\ResourceModel\Attachment as AttachmentResource;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magento\Framework\Module\ResourceInterface
     */
    protected $moduleResource;

    /**
     * InstallSchema constructor.
     * @param \Magento\Framework\Module\ResourceInterface $moduleResource
     */
    public function __construct(
        \Magento\Framework\Module\ResourceInterface $moduleResource
    ) {
        $this->moduleResource = $moduleResource;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->addCustomerGroupRelationTable($setup);
        }

        $setup->endSetup();
    }

    /**
     *
     * @param SchemaSetupInterface $setup
     */
    private function addCustomerGroupRelationTable(SchemaSetupInterface $setup)
    {
        /**
         * Create customer group relation table for attachment
         */
        if (!$setup->tableExists(AttachmentResource::CUSTOMER_GROUP_RELATION_TABLE)) {
            $tableAttachmentCustomerGroup = $setup->getConnection()
                ->newTable($setup->getTable(AttachmentResource::CUSTOMER_GROUP_RELATION_TABLE));
            $tableAttachmentCustomerGroup->addColumn(
                'attachment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                        'unsigned' => true,
                        'nullable' => false,
                        'primary'   => true,
                    ],
                'Attachment ID'
            )
                ->addColumn(
                    'customer_group_id',
                    $this->getCustomerGroupColumnType(),
                    null,
                    [
                        'unsigned'  => true,
                        'nullable'  => false,
                        'primary'   => true,
                    ],
                    'Customer Group ID'
                )
                ->addIndex(
                    $setup->getIdxName(AttachmentResource::CUSTOMER_GROUP_RELATION_TABLE, ['customer_group_id']),
                    ['customer_group_id']
                )
                ->addForeignKey(
                    $setup->getFkName(
                        AttachmentResource::CUSTOMER_GROUP_RELATION_TABLE,
                        'attachment_id',
                        'mageworx_downloads_attachment',
                        'attachment_id'
                    ),
                    'attachment_id',
                    $setup->getTable('mageworx_downloads_attachment'),
                    'attachment_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        AttachmentResource::CUSTOMER_GROUP_RELATION_TABLE,
                        'customer_group_id',
                        'customer_group',
                        'customer_group_id'
                    ),
                    'customer_group_id',
                    $setup->getTable('customer_group'),
                    'customer_group_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('MageWorx Attachments To Customer Groups Relations');

            $setup->getConnection()->createTable($tableAttachmentCustomerGroup);
        }
    }

    /**
     * @return string
     */
    protected function getCustomerGroupColumnType()
    {
        $customerDbVersion = $this->moduleResource->getDbVersion('Magento_Customer');

        if (version_compare($customerDbVersion, '2.0.10', '<')) {
            return \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT;
        }

        return \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER;
    }
}
