<?php
/**
 * Created by PhpStorm.
 * User: canhnd
 * Date: 09/02/2017
 * Time: 11:48
 */
namespace Magenest\ZohocrmIntegration\Setup;

use Magento\Framework\Setup\SetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magenest\ZohocrmIntegration\Model\Queue;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $this->createQueueTable($installer);
        }
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $tableName = 'magenest_zohocrm_link_entity';
            $table = $installer->getConnection()
                ->newTable($installer->getTable($tableName))
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Id'
                )
                ->addColumn(
                    'entity_id',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false],
                    'Entity Id'
                )
                ->addColumn(
                    'zoho_entity_id',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false],
                    'Zoho entity Id'
                )
                ->addColumn(
                    'type',
                    Table::TYPE_TEXT,
                    10,
                    ['nullable' => false],
                    'Zoho type'
                )
                ->setComment('ZohoCrm Link entity');

            $installer->getConnection()->createTable($table);

            $installer->endSetup();
        }



        if (version_compare($context->getVersion(), '2.0.2') < 0) {

            $installer->getConnection()->addColumn(
                $installer->getTable('magenest_zohocrm_queue'),
                'status',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 10,
                    'nullable' => false,
                    'comment' => 'Status'
                ]
            );
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '2.0.8') < 0) {

            $installer->getConnection()->modifyColumn(
                $installer->getTable('magenest_zohocrm_link_entity'),
                'type',
                [
                    'type' => Table::TYPE_TEXT,
                    'size' => 15,
                    'nullable' => false,
                    'comment' => 'Status'
                ]
            );
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '2.0.7') < 0) {

            $tableName = $setup->getTable('magenest_zohocrm_map');

            if ($setup->getConnection()->isTableExists($tableName) == true) {

                $datas=[
                    [
                        'zoho_field' => "Account Number",
                        'magento_field' => "entity_id",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Account Site",
                        'magento_field' => "created_in",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing City",
                        'magento_field' => "bill_city",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing Code",
                        'magento_field' => "bill_postcode",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing Country",
                        'magento_field' => "bill_country_id",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing State",
                        'magento_field' => "bill_region",
                    ],
                    [
                        'zoho_field' => "Billing Street",
                        'magento_field' => "bill_street",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping City",
                        'magento_field' => "ship_city",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping Code",
                        'magento_field' => "ship_postcode",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping Country",
                        'magento_field' => "ship_country_id",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping State",
                        'magento_field' => "ship_region",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping Street",
                        'magento_field' => "ship_street",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Phone",
                        'magento_field' => "bill_telephone",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Fax",
                        'magento_field' => "bill_fax",
                        'status' => 1,
                        'type' => 'Accounts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "First Name",
                        'magento_field' => "firstname",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Last Name",
                        'magento_field' => "lastname",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Email",
                        'magento_field' => "email",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Date of Birth",
                        'magento_field' => "dob",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Mailing City",
                        'magento_field' => "bill_city",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Mailing Code",
                        'magento_field' => "bill_postcode",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Mailing Country",
                        'magento_field' => "bill_country_id",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Mailing State",
                        'magento_field' => "bill_region",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Mailing Street",
                        'magento_field' => "bill_street",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Other City",
                        'magento_field' => "ship_city",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Other Code",
                        'magento_field' => "ship_postcode",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Other Country",
                        'magento_field' => "ship_country_id",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Other State",
                        'magento_field' => "ship_region",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Other Street",
                        'magento_field' => "ship_street",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Phone",
                        'magento_field' => "bill_telephone",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Fax",
                        'magento_field' => "bill_fax",
                        'status' => 1,
                        'type' => 'Contacts',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "First Name",
                        'magento_field' => "firstname",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Last Name",
                        'magento_field' => "lastname",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Email",
                        'magento_field' => "email",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Company",
                        'magento_field' => "email",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "City",
                        'magento_field' => "bill_city",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Zip Code",
                        'magento_field' => "bill_postcode",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Country",
                        'magento_field' => "bill_country_id",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "State",
                        'magento_field' => "bill_region",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Street",
                        'magento_field' => "bill_street",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Phone",
                        'magento_field' => "bill_telephone",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Fax",
                        'magento_field' => "bill_fax",
                        'status' => 1,
                        'type' => 'Leads',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Product Name",
                        'magento_field' => "name",
                        'status' => 1,
                        'type' => 'Products',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Product Code",
                        'magento_field' => "sku",
                        'status' => 1,
                        'type' => 'Products',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Product Active",
                        'magento_field' => "status",
                        'status' => 1,
                        'type' => 'Products',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Description",
                        'magento_field' => "description",
                        'status' => 1,
                        'type' => 'Products',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Unit Price",
                        'magento_field' => "price",
                        'status' => 1,
                        'type' => 'Products',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Qty in Stock",
                        'magento_field' => "qty",
                        'status' => 1,
                        'type' => 'Products',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Subject",
                        'magento_field' => "increment_id",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Customer No",
                        'magento_field' => "customer_id",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Due Date",
                        'magento_field' => "created_at",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing City",
                        'magento_field' => "bill_city",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing Code",
                        'magento_field' => "bill_postcode",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing Country",
                        'magento_field' => "bill_country_id",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing State",
                        'magento_field' => "bill_region",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing Street",
                        'magento_field' => "bill_street",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping City",
                        'magento_field' => "ship_city",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping Code",
                        'magento_field' => "ship_postcode",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping Country",
                        'magento_field' => "ship_country_id",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping State",
                        'magento_field' => "ship_region",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping Street",
                        'magento_field' => "ship_street",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Adjustment",
                        'magento_field' => "shipping_amount",
                        'status' => 1,
                        'type' => Queue::TYPE_ORDER,
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Subject",
                        'magento_field' => "increment_id",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Invoice Date",
                        'magento_field' => "created_at",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing City",
                        'magento_field' => "bill_city",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing Code",
                        'magento_field' => "bill_postcode",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing Country",
                        'magento_field' => "bill_country_id",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing State",
                        'magento_field' => "bill_region",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Billing Street",
                        'magento_field' => "bill_street",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping City",
                        'magento_field' => "ship_city",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping Code",
                        'magento_field' => "ship_postcode",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping Country",
                        'magento_field' => "ship_country_id",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping State",
                        'magento_field' => "ship_region",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Shipping Street",
                        'magento_field' => "ship_street",
                        'status' => 1,
                        'type' => 'Invoices',
                        'description' => 'Auto Generate'
                    ],
                    [
                        'zoho_field' => "Adjustment",
                        'magento_field' => "shipping_amount",
                        'status' => 1,
                        'type' => Queue::TYPE_INVOICE,
                        'description' => 'Auto Generate'
                    ],
                ];

                $setup->getConnection()->delete($setup->getTable('magenest_zohocrm_field'));
                $setup->getConnection()->delete($tableName);
                // Insert data to table
                foreach ($datas as $item) {
                    $setup->getConnection()->insert($tableName, $item);
                }
            }
            $installer->endSetup();
        }
    }

    /**
     * Create the table magenest_zohocrm_queue
     *
     * @param SetupInterface $installer
     * @return void
     */
    private function createQueueTable($installer)
    {
        $tableName = 'magenest_zohocrm_queue';
        $table = $installer->getConnection()
            ->newTable($installer->getTable($tableName))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
                ],
                'Id'
            )
            ->addColumn(
                'type',
                Table::TYPE_TEXT,
                45,
                ['nullable' => true],
                'Entity Type'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Entity Id'
            )
            ->addColumn(
                'enqueue_time',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Enqueue Time'
            )
            ->addColumn(
                'priority',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Enqueue Time'
            )
            ->setComment('ZohoCrm Sync Queue');

        $installer->getConnection()->createTable($table);
    }
}
