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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Cart2Quote\Quotation\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'quotation_quote'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('quotation_quote')
        )->addColumn(
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Quote ID'
        )->addColumn(
            'state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Quote State'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Quote Status'
        )->addColumn(
            'increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Quote readable ID'
        )->addColumn(
            'proposal_sent',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Last updated date'
        )->addColumn(
            'email_sent',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Email Sent'
        )->addColumn(
            'send_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Send Email'
        )->addColumn(
            'original_base_subtotal',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Original Base Subtotal'
        )->addColumn(
            'original_subtotal',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Original Subtotal'
        )->addIndex(
            $installer->getIdxName('quotation_quote', ['send_email']),
            ['send_email']
        )->addIndex(
            $installer->getIdxName('quotation_quote', ['email_sent']),
            ['email_sent']
        )->addForeignKey(
            $installer->getFkName('quotation_quote', 'quote_id', 'quote', 'entity_id'),
            'quote_id',
            $installer->getTable('quote'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Quotation main Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'quotation_quote_status'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('quotation_quote_status')
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => false, 'primary' => true],
            'Status'
        )->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            ['nullable' => false],
            'Label'
        )->setComment(
            'Quotation Quote Status Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'quotation_quote_status_state'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('quotation_quote_status_state')
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => false, 'primary' => true],
            'Status'
        )->addColumn(
            'state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => false, 'primary' => true],
            'Label'
        )->addColumn(
            'is_default',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Is Default'
        )->addColumn(
            'visible_on_front',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Visible on front'
        )->addForeignKey(
            $installer->getFkName('quotation_quote_status_state', 'status', 'quotation_quote_status', 'status'),
            'status',
            $installer->getTable('quotation_quote_status'),
            'status',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Quotation Quote Status Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'quotation_quote_status_label'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('quotation_quote_status_label')
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => false, 'primary' => true],
            'Status'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store Id'
        )->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            ['nullable' => false],
            'Label'
        )->addIndex(
            $installer->getIdxName('quotation_quote_status_label', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('quotation_quote_status_label', 'status', 'quotation_quote_status', 'status'),
            'status',
            $installer->getTable('quotation_quote_status'),
            'status',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('quotation_quote_status_label', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Quotation Quote Status Label Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'quotation_quote_status_history'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('quotation_quote_status_history')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'parent_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Parent Id'
        )->addColumn(
            'is_customer_notified',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Is Customer Notified'
        )->addColumn(
            'is_visible_on_front',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Is Visible On Front'
        )->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Comment'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'entity_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => true],
            'Shows what entity history is bind to.'
        )->addIndex(
            $installer->getIdxName('quotation_quote_status_history', ['parent_id']),
            ['parent_id']
        )->addIndex(
            $installer->getIdxName('quotation_quote_status_history', ['created_at']),
            ['created_at']
        )->addForeignKey(
            $installer->getFkName('quotation_quote_status_history', 'parent_id', 'quotation_quote', 'entity_id'),
            'parent_id',
            $installer->getTable('quotation_quote'),
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Quotation Flat Quote Status History'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'quotation_quote_tier_item'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('quotation_quote_tier_item')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Item ID'
        )->addColumn(
            'qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['identity' => false, 'unsigned' => false, 'nullable' => false, 'primary' => true],
            'Tier qty'
        )->addColumn(
            'custom_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Custom Price'
        )->addColumn(
            'original_base_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Original Base Price'
        )->addColumn(
            'original_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Original Price'
        )->addForeignKey(
            $installer->getFkName('quotation_quote_tier_item', 'item_id', 'quote_item', 'item_id'),
            'item_id',
            $installer->getTable('quote_item'),
            'item_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Quotation Quote tier items'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
