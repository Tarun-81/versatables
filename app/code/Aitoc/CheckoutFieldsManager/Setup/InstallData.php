<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Setup;

use Aitoc\CheckoutFieldsManager\Setup\CheckoutSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 *
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $checkoutSetupFactory;

    /**
     * Email setup factory
     *
     * @var EmailSetupFactory
     */
    private $emailSetupFactory;

    /**
     * @param CheckoutSetupFactory $checkoutSetupFactory
     * @param EmailSetupFactory $emailSetupFactory
     */
    public function __construct(
        CheckoutSetupFactory $checkoutSetupFactory,
        EmailSetupFactory $emailSetupFactory
    ) {
        $this->checkoutSetupFactory = $checkoutSetupFactory;
        $this->emailSetupFactory = $emailSetupFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var CheckoutSetup $checkoutSetup */
        $checkoutSetup = $this->checkoutSetupFactory->create(['setup' => $setup]);

        /**
         * Install eav entity types to the eav/entity_type table
         */
        $entities = $checkoutSetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $checkoutSetup->addEntityType($entityName, $entity);
        }

        $emailSetup = $this->emailSetupFactory->create(['setup' => $setup]);
        $data = $emailSetup->getDataEmail('checkoutfieldsmanager_order_email_order_template');
        $connection = $setup->getConnection();
        $connection->insert($setup->getTable('email_template'), $data);

        $setup->endSetup();
    }
}
