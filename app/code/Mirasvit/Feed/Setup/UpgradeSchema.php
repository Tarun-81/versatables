<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.0.91
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Feed\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Mirasvit\Feed\Api\Data\ValidationInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $connection = $installer->getConnection();

        // Add report tables
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $connection->dropTable($installer->getTable('mst_feed_report'));

            $table = $installer->getConnection()->newTable(
                $installer->getTable('mst_feed_report')
            )->addColumn(
                'row_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Row Id'
            )->addColumn(
                'session',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Session'
            )->addColumn(
                'feed_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Feed Id'
            )->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => true],
                'Product Id'
            )->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => true],
                'Order Id'
            )->addColumn(
                'is_click',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false, 'default' => 0],
                'Is Click?'
            )->addColumn(
                'subtotal',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Order subtotal (for product)'
            )->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Store Id'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['unsigned' => false, 'nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('mst_feed_report', ['product_id']),
                ['product_id']
            )->addIndex(
                $installer->getIdxName('mst_feed_report', ['feed_id']),
                ['feed_id']
            )->addIndex(
                $installer->getIdxName('mst_feed_report', ['session']),
                ['session']
            )->addIndex(
                $installer->getIdxName('mst_feed_report', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName(
                    'mst_feed_report',
                    'feed_id',
                    'mst_feed_feed',
                    'feed_id'
                ),
                'feed_id',
                $installer->getTable('mst_feed_feed'),
                'feed_id',
                Table::ACTION_CASCADE
            )->setComment('Feed Report');

            $installer->getConnection()->createTable($table);
        }

        // Add dynamic attribute
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $connection->dropTable($installer->getTable('mst_feed_dynamic_attribute'));

            $table = $installer->getConnection()->newTable(
                $installer->getTable('mst_feed_dynamic_attribute')
            )->addColumn(
                'attribute_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Attribute Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )->addColumn(
                'code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Code'
            )->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Conditions'
            )->setComment('Dynamic Attributes');

            $installer->getConnection()->createTable($table);
        }

        // Add dynamic variable
        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $connection->dropTable($installer->getTable('mst_feed_dynamic_variable'));

            $table = $installer->getConnection()->newTable(
                $installer->getTable('mst_feed_dynamic_variable')
            )->addColumn(
                'variable_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Variable Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )->addColumn(
                'code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Code'
            )->addColumn(
                'php_code',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'PHP Code'
            )->setComment('Dynamic Variables');

            $installer->getConnection()->createTable($table);
        }

        // Add feed generation validation
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(ValidationInterface::TABLE_NAME)
            )->addColumn(
                ValidationInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Validation Id'
            )->addColumn(
                ValidationInterface::FEED_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Feed ID'
            )->addColumn(
                ValidationInterface::LINE_NUM,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Line Number'
            )->addColumn(
                ValidationInterface::ENTITY_ID,
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => true],
                'Entity ID'
            )->addColumn(
                ValidationInterface::VALIDATOR,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Validator'
            )->addColumn(
                ValidationInterface::ATTRIBUTE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Attribute'
            )->addColumn(
                ValidationInterface::VALUE,
                Table::TYPE_TEXT,
                '64K',
                ['nullable' => true],
                'Value'
            )->addIndex(
                $installer->getIdxName(ValidationInterface::TABLE_NAME, [ValidationInterface::LINE_NUM]),
                ValidationInterface::LINE_NUM
            )->addIndex(
                $installer->getIdxName(ValidationInterface::TABLE_NAME, [ValidationInterface::ENTITY_ID]),
                ValidationInterface::ENTITY_ID
            )->addIndex(
                $installer->getIdxName(ValidationInterface::TABLE_NAME, [ValidationInterface::ATTRIBUTE]),
                ValidationInterface::ATTRIBUTE
            )->addIndex(
                $installer->getIdxName(ValidationInterface::TABLE_NAME, [ValidationInterface::VALIDATOR]),
                ValidationInterface::VALIDATOR
            )->addForeignKey(
                $installer->getFkName(
                    ValidationInterface::TABLE_NAME,
                    ValidationInterface::FEED_ID,
                    'mst_feed_feed',
                    'feed_id'
                ),
                ValidationInterface::FEED_ID,
                $installer->getTable('mst_feed_feed'),
                'feed_id',
                Table::ACTION_CASCADE
            );

            $installer->getConnection()->createTable($table);
        }
    }
}
