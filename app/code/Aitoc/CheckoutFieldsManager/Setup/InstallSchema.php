<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->createOrderValueTable($installer);
        $this->createQuoteValueTable($installer);
        $this->createEavAttributeTable($installer);
        $this->createEavAttributeStoreTable($installer);

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createOrderValueTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_sales_order_value'))
            ->addColumn(
                'value_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Value ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Order ID'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Attribute ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                '255',
                ['nullable' => true],
                'Value can be option_id'
            )
            ->addIndex(
                $installer->getIdxName('aitoc_sales_order_value', ['order_id', 'attribute_id']),
                ['order_id', 'attribute_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_sales_order_value',
                    'order_id',
                    'sales_order',
                    'entity_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_sales_order_value',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $installer->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Sales Order Attribute Values');

        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createQuoteValueTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_sales_quote_value'))
            ->addColumn(
                'value_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Value ID'
            )
            ->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Quote ID'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Attribute ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                '255',
                ['nullable' => true],
                'Value can be option_id'
            )
            ->addIndex(
                $installer->getIdxName('aitoc_sales_quote_value', ['quote_id', 'attribute_id']),
                ['quote_id', 'attribute_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_sales_quote_value',
                    'quote_id',
                    'quote',
                    'quote_id'
                ),
                'quote_id',
                $installer->getTable('quote'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_sales_quote_value',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $installer->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Sales Quote Attribute Values');

        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createEavAttributeTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_checkout_eav_attribute'))
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Attribute ID'
            )
            ->addColumn(
                'is_visible',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 1],
                'Attribute ID'
            )
            ->addColumn(
                'sort_order',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Sort Order'
            )
            ->addColumn(
                'validate_rules',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Validate Rules'
            )
            ->addColumn(
                'display_area',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Display Area'
            )
            ->addColumn(
                'checkout_step',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Checkout Step name'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_checkout_eav_attribute',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $installer->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )
            ->setComment('aitoc checkout eav attribute');

        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createEavAttributeStoreTable($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_checkout_eav_attribute_store'))
            ->addColumn(
                'entity_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Attribute ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store ID'
            )
            ->addIndex(
                $installer->getIdxName('aitoc_checkout_eav_attribute_store', ['attribute_id', 'store_id']),
                ['attribute_id', 'store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_checkout_eav_attribute_store',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $installer->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aitoc_checkout_eav_attribute_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('aitoc checkout eav attribute');

        $installer->getConnection()->createTable($table);
    }
}
