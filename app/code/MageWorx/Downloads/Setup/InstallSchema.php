<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use MageWorx\Downloads\Model\ResourceModel\Attachment as AttachmentResource;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'mageworx_downloads_attachment'
         */
        $tableAttachment = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_downloads_attachment')
        )
            ->addColumn(
                'attachment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                ],
                'Attachment ID'
            )
            ->addColumn(
                'section_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                [
                'unsigned' => true,
                'nullable' => false,
                'default'  => \MageWorx\Downloads\Model\Section::DEFAULT_ID,
                ],
                'Section ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                'nullable' => false,
                ],
                'Name'
            )
            ->addColumn(
                'is_attach',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                [
                'unsigned' => true,
                'nullable' => false,
                'default'  => 0
                ],
                'Is Attach'
            )
            ->addColumn(
                'filename',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                'default' => '',
                ],
                'File Name'
            )
            ->addColumn(
                'url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1024,
                [
                'default'  => '',
                ],
                'URL'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                16,
                [
                'nullable' => false,
                ],
                'Type'
            )
            ->addColumn(
                'size',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                [
                'unsigned' => true,
                'nullable' => false,
                'default'  => 0
                ],
                'Size'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                'nullable' => false,
                ],
                'Description'
            )
            ->addColumn(
                'allow_guests',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                [
                'unsigned' => true,
                'nullable' => false,
                'default'  => 1
                ],
                'Allow Quests'
            )
            ->addColumn(
                'downloads',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                [
                'unsigned' => true,
                'nullable' => false,
                ],
                'Downloads'
            )
            ->addColumn(
                'downloads_limit',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                [
                'unsigned' => true,
                'nullable' => false,
                ],
                'Downloads Limit'
            )
            ->addColumn(
                'date_modified',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Last Modify Date'
            )
            ->addColumn(
                'date_added',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Added Date'
            )
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                [
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                ],
                'Is Active'
            );
        $installer->getConnection()->createTable($tableAttachment);

        /**
         * Create table 'mageworx_downloads_section'
         */
        $tableSection = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_downloads_section')
        )
            ->addColumn(
                'section_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                ],
                'Section ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                'nullable' => false,
                ],
                'Name'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                'nullable' => false,
                ],
                'Description'
            )
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                [
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                ],
                'Is Active'
            );
        $installer->getConnection()->createTable($tableSection);

        /**
         * Create store relation table for attachment
         */
        if (!$installer->tableExists(AttachmentResource::STORE_RELATION_TABLE)) {
            $tableAttachmentStore = $installer->getConnection()
                ->newTable($installer->getTable(AttachmentResource::STORE_RELATION_TABLE));
            $tableAttachmentStore->addColumn(
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
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned'  => true,
                        'nullable'  => false,
                        'primary'   => true,
                    ],
                    'Store ID'
                )
                ->addIndex(
                    $installer->getIdxName(AttachmentResource::STORE_RELATION_TABLE, ['store_id']),
                    ['store_id']
                )
                ->addForeignKey(
                    $installer->getFkName(
                        AttachmentResource::STORE_RELATION_TABLE,
                        'attachment_id',
                        'mageworx_downloads_attachment',
                        'attachment_id'
                    ),
                    'attachment_id',
                    $installer->getTable('mageworx_downloads_attachment'),
                    'attachment_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(AttachmentResource::STORE_RELATION_TABLE, 'attachment_id', 'store', 'store_id'),
                    'store_id',
                    $installer->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Attachments To Store Linkage Table');
            $installer->getConnection()->createTable($tableAttachmentStore);
        }

        /**
         * Create product relation table for attachment
         */
        $tableAttachmentRelation = $installer->getConnection()->newTable(
            $installer->getTable(AttachmentResource::PRODUCT_RELATION_TABLE)
        )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                ],
                'ID'
            )
            ->addColumn(
                'attachment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                [
                'unsigned' => true,
                'nullable' => false,
                ],
                'Attachment ID'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                [
                'unsigned' => true,
                'nullable' => false,
                ],
                'Product ID'
            )
            ->addIndex(
                $installer->getIdxName(
                    AttachmentResource::PRODUCT_RELATION_TABLE,
                    ['attachment_id', 'product_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['attachment_id', 'product_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $installer->getFkName(
                    AttachmentResource::PRODUCT_RELATION_TABLE,
                    'attachment_id',
                    'mageworx_downloads_attachment',
                    'attachment_id'
                ),
                'attachment_id',
                $installer->getTable('mageworx_downloads_attachment'),
                'attachment_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    AttachmentResource::PRODUCT_RELATION_TABLE,
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Attachments To Products And Options Linkage Table'
            );
        $installer->getConnection()->createTable($tableAttachmentRelation);
        $installer->endSetup();
    }
}
