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

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade Schema script
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade schema action
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.20') < 0) {
            // Get module table
            $tableName = $setup->getTable('quotation_quote');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'hash' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => 40,
                        'default' => '',
                        'comment' => 'hash value',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            // Get module table
            $tableName = $setup->getTable('quotation_quote');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'is_quote' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'length' => 5,
                        'default' => 1,
                        'comment' => 'check quote is live',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.1.2') < 0) {
            // Get module table
            $tableName = $setup->getTable('quotation_quote_tier_item');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'comment' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => '64k',
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'comment per line item',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.1.3') < 0) {
            /**
             * Alter quote table with is_quotation_quote
             */
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'is_quotation_quote',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'default' => '0',
                    'comment' => 'Is Quotation Quote'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.1') < 0) {
            // Get module table
            $tableName = $setup->getTable('quotation_quote_tier_item');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'item_has_comment' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 1,
                        'nullable' => false,
                        'comment' => 'check item has comment',
                    ],
                    'make_optional' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 0,
                        'nullable' => false,
                        'comment' => 'check make optional',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.2') < 0) {
            // Get module table
            $tableName = $setup->getTable('quotation_quote');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'expiry_date' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'quotation expiry date',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.3') < 0) {
            // Get module table
            $tableName = $setup->getTable('quotation_quote');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'reminder_date' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'quotation reminder date',
                    ],
                    'reminder_email_sent' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 0,
                        'nullable' => false,
                        'comment' => 'check reminder email sent or not',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.4') < 0) {
            // Get module table
            $tableName = $setup->getTable('quotation_quote');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'expiry_email_sent' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 0,
                        'nullable' => false,
                        'comment' => 'check expiry email sent or not',
                    ],
                    'expiry_enabled' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 1,
                        'nullable' => false,
                        'comment' => 'check expiry enable or not',
                    ],
                    'reminder_enabled' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 1,
                        'nullable' => false,
                        'comment' => 'check reminder enable or not',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            $tableName = $setup->getTable('quotation_quote_tier_item');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'original_base_price');
                if ($columnExists) {
                    $connection->changeColumn(
                        $tableName,
                        'original_base_price',
                        'base_original_price',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4'
                        ]
                    );
                }

                $connection->addColumn(
                    $tableName,
                    'base_custom_price',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => false,
                        'length' => '12,4',
                        'comment' => 'Base custom price'
                    ]
                );
            }

            $tableName = $setup->getTable('quotation_quote');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'original_base_subtotal');
                if ($columnExists) {
                    $connection->changeColumn(
                        $tableName,
                        'original_base_subtotal',
                        'base_original_subtotal',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_custom_price_total');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_custom_price_total',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base custom price total'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'custom_price_total');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'custom_price_total',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Custom price total'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_quote_adjustment');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_quote_adjustment',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base quote adjustment total'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'quote_adjustment');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'quote_adjustment',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Quote adjustment total'
                        ]
                    );
                }

                $quotationQuoteTable = $setup->getTable('quotation_quote');
                $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'email_sent');
                if ($columnExists) {
                    $connection->dropColumn($quotationQuoteTable, 'email_sent');
                }
                $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'send_email');
                if ($columnExists) {
                    $connection->dropColumn($quotationQuoteTable, 'send_email');
                }
                $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'reminder_email_sent');
                if ($columnExists) {
                    $connection->dropColumn($quotationQuoteTable, 'reminder_email_sent');
                }
                $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'expiry_email_sent');
                if ($columnExists) {
                    $connection->dropColumn($quotationQuoteTable, 'expiry_email_sent');
                }

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_request_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send request email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'request_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Request email sent'
                    ]
                );

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_quote_canceled_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send quote canceled email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'quote_canceled_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Quote canceled email sent'
                    ]
                );

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_proposal_accepted_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send proposal accepted email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'proposal_accepted_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Proposal accepted email sent'
                    ]
                );

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_proposal_expired_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send proposal expired email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'proposal_expired_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Proposal expired email sent'
                    ]
                );

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_proposal_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send proposal email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'proposal_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Proposal email sent'
                    ]
                );

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_reminder_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send reminder email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'reminder_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Reminder email sent'
                    ]
                );
            }

        }

        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $tableName = $setup->getTable('quotation_quote');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'fixed_shipping_price');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'fixed_shipping_price',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base custom price'
                        ]
                    );
                }
            }

            $isQuotationQuoteColumnName = 'is_quotation_quote';
            $quoteTable = $setup->getTable('quote');
            if ($setup->getConnection()->tableColumnExists($quoteTable, $isQuotationQuoteColumnName)) {
                $setup->getConnection()->dropColumn($quoteTable, $isQuotationQuoteColumnName);
            }

            $linkedQuotationQuoteIdColumnName = 'linked_quotation_id';
            if (!$setup->getConnection()->tableColumnExists($quoteTable, $linkedQuotationQuoteIdColumnName)) {
                $setup->getConnection()->addColumn(
                    $quoteTable,
                    $linkedQuotationQuoteIdColumnName,
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned' => true,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'Linked Quotation Quote'
                    ]
                );
                $quotationQuoteTable = $setup->getTable('quotation_quote');
                $setup->getConnection()->addForeignKey(
                    $setup->getFkName(
                        $quoteTable,
                        \Magento\Quote\Model\Quote::KEY_ENTITY_ID,
                        $quotationQuoteTable,
                        'quote_id'
                    ),
                    $quoteTable,
                    $linkedQuotationQuoteIdColumnName,
                    $quotationQuoteTable,
                    'quote_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
                );
            }

            $this->copyProductComment($setup);
            $this->removeUnusedProductComment($setup);
        }

        if (version_compare($context->getVersion(), '2.0.3.2') < 0) {
            $tableName = $setup->getTable('quotation_quote_tier_item');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'base_cost_price');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_cost_price',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base cost price'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'cost_price');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'cost_price',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Cost price'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_discount_amount');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_discount_amount',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base discount amount'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'discount_amount');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'discount_amount',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Discount amount'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_row_total');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_row_total',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base row total'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'row_total');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'row_total',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Row total'
                        ]
                    );
                }

                // todo: validate if the index exists
                $connection->addIndex(
                    $tableName,
                    $connection->getIndexName(
                        $tableName, ['qty', 'item_id'], AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['qty', 'item_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                );
            }
        }

        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            /**
             * Create table 'quotation_quote_sections'
             */
            $table = $setup->getConnection()->newTable(
                $setup->getTable('quotation_quote_sections')
            )->addColumn(
                'section_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Section ID'
            )->addColumn(
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Quotation Quote Id'
            )->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false],
                'Label'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Sort Order'
            )->addIndex(
                $setup->getIdxName('quotation_quote_sections', ['section_id']),
                ['section_id']
            )->addIndex(
                $setup->getIdxName('quotation_quote_sections', ['quote_id']),
                ['quote_id']
            )->addForeignKey(
                $setup->getFkName('quotation_quote_sections', 'quote_id', $setup->getTable('quotation_quote'),
                    'entity_id'),
                'quote_id',
                $setup->getTable('quotation_quote'),
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->setComment(
                'Quotation Quote Sections Table'
            );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'quotation_quote_sections'
             */
            $table = $setup->getConnection()->newTable(
                $setup->getTable('quotation_quote_section_items')
            )->addColumn(
                'section_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Section Item ID'
            )->addColumn(
                'section_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Section ID'
            )->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Quote Item Id'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Sort Order'
            )->addIndex(
                $setup->getIdxName('quotation_quote_section_items', ['section_id']),
                ['section_id']
            )->addIndex(
                $setup->getIdxName('quotation_quote_section_items', ['item_id']),
                ['item_id']
            )->addForeignKey(
                $setup->getFkName('quotation_quote_section_items', 'section_id',
                    $setup->getTable('quotation_quote_sections'),
                    'section_id'),
                'section_id',
                $setup->getTable('quotation_quote_sections'),
                'section_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->addForeignKey(
                $setup->getFkName('quotation_quote_section_items', 'item_id', $setup->getTable('quote_item'),
                    'item_id'),
                'item_id',
                $setup->getTable('quote_item'),
                'item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->setComment(
                'Quotation Quote Sections Table'
            );
            $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            $isQuotationQuoteColumnName = 'is_quotation_quote';
            $quoteTable = $setup->getTable('quote');
            if (!$setup->getConnection()->tableColumnExists($quoteTable, $isQuotationQuoteColumnName)) {
                /**
                 * Alter quote table with is_quotation_quote
                 */
                $setup->getConnection()->addColumn(
                    $setup->getTable('quote'),
                    'is_quotation_quote',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => '0',
                        'comment' => 'Is Quotation Quote'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            $tableName = $setup->getTable('quotation_quote');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'quotation_created_at');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'quotation_created_at',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            'unsigned' => true,
                            'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                            'comment' => 'Quotation created date'
                        ]
                    );
                }
            }
        }

        $setup->endSetup();
    }

    /**
     * Copy the product comments
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function copyProductComment(SchemaSetupInterface $setup)
    {
        $sql = sprintf(
            "UPDATE %s as item
             INNER JOIN %s as tier
             ON tier.item_id = item.item_id
             SET item.description = (CONCAT(COALESCE(item.description, ''), COALESCE(tier.comment, '')));",
            $setup->getTable('quote_item'),
            $setup->getTable('quotation_quote_tier_item')
        );

        $setup->getConnection()->query($sql);
    }

    /**
     * Remove comment from tier item
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function removeUnusedProductComment(SchemaSetupInterface $setup)
    {
        $quotationTierItem = $setup->getTable('quotation_quote_tier_item');

        if ($setup->getConnection()->tableColumnExists($quotationTierItem, 'comment')) {
            $setup->getConnection()->dropColumn(
                $quotationTierItem,
                'comment'
            );
        }

        if ($setup->getConnection()->tableColumnExists($quotationTierItem, 'item_has_comment')) {
            $setup->getConnection()->dropColumn(
                $quotationTierItem,
                'item_has_comment'
            );
        }
    }
}
