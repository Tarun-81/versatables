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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Class Uninstall
 * @package Cart2Quote\Quotation\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;
    /**
     * @var \Cart2Quote\Quotation\Setup\SalesSetupFactory
     */
    private $salesSetupFactory;
    /**
     * @var \Magento\Authorization\Model\ResourceModel\Rules\Collection
     */
    private $rulesResourceModelCollection;
    /**
     * @var \Magento\Authorization\Model\ResourceModel\RulesFactory
     */
    private $rulesResourceModelFactory;
    /**
     * @var \Magento\SalesSequence\Model\ResourceModel\MetaFactory
     */
    private $metaResourceModelFactory;
    /**
     * @var \Magento\SalesSequence\Model\ResourceModel\ProfileFactory
     */
    private $profileResourceModelFactory;

    /**
     * Uninstall constructor.
     * @param \Magento\SalesSequence\Model\ResourceModel\ProfileFactory $profileResourceModelFactory
     * @param \Magento\SalesSequence\Model\ResourceModel\MetaFactory $metaResourceModelFactory
     * @param \Magento\Authorization\Model\ResourceModel\RulesFactory $rulesResourceModelFactory
     * @param \Magento\Authorization\Model\ResourceModel\Rules\Collection $rulesResourceCollection
     * @param \Cart2Quote\Quotation\Setup\SalesSetupFactory $salesSetupFactory
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\SalesSequence\Model\ResourceModel\ProfileFactory $profileResourceModelFactory,
        \Magento\SalesSequence\Model\ResourceModel\MetaFactory $metaResourceModelFactory,
        \Magento\Authorization\Model\ResourceModel\RulesFactory $rulesResourceModelFactory,
        \Magento\Authorization\Model\ResourceModel\Rules\Collection $rulesResourceCollection,
        \Cart2Quote\Quotation\Setup\SalesSetupFactory $salesSetupFactory,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->rulesResourceModelCollection = $rulesResourceCollection;
        $this->rulesResourceModelFactory = $rulesResourceModelFactory;
        $this->metaResourceModelFactory = $metaResourceModelFactory;
        $this->profileResourceModelFactory = $profileResourceModelFactory;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->dropTables($setup);
        $this->dropColumns($setup);

        $this->removeAclResources();
        $this->removeProductAttribute($setup);
        $this->removeSaleSequence($setup);
        $this->removeConfigurationValues($setup);

        $setup->endSetup();
    }

    /**
     * Remove acl rules related to cart2quote quotation
     */
    private function removeAclResources()
    {
        $this->rulesResourceModelCollection->addFieldToFilter('resource_id', [['like' => '%Cart2Quote_Quotation::%']]);
        /**
         * @var \Magento\Authorization\Model\Rules $rule
         */
        foreach ($this->rulesResourceModelCollection->getItems() as $rule) {
            $this->rulesResourceModelFactory->create()->delete($rule);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function removeProductAttribute(SchemaSetupInterface $setup)
    {
        /**
         * @var \Magento\Catalog\Setup\CategorySetup $catalogSetup
         */
        $catalogSetup = $this->categorySetupFactory->create();
        $catalogAttributeId = $catalogSetup->getAttributeId(
            \Magento\Catalog\Model\Product::ENTITY,
            'cart2quote_quotable'
        );
        $catalogSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cart2quote_quotable'
        );
        $setup->getConnection()->delete(
            $setup->getTable('catalog_eav_attribute'),
            ['attribute_id = ?' => $catalogAttributeId]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function removeSaleSequence(SchemaSetupInterface $setup)
    {
        /**
         * Remove entity type and sales sequence tables
         * @var \Cart2Quote\Quotation\Setup\SalesSetup $salesSetup
         */
        $salesSetup = $this->salesSetupFactory->create();
        foreach ($salesSetup->getDefaultEntities() as $entityType => $entity) {
            $salesSetup->removeEntityType($entityType);
            foreach ($this->storeManager->getStores(true) as $store) {
                /**
                 * @var \Magento\SalesSequence\Model\ResourceModel\Meta $metaResourceModel
                 */
                $metaResourceModel = $this->metaResourceModelFactory->create();
                $meta = $metaResourceModel->loadByEntityTypeAndStore($entityType, $store->getId());

                /**
                 * @var \Magento\SalesSequence\Model\ResourceModel\Profile $profileResourceModel
                 */
                $profileResourceModel = $this->profileResourceModelFactory->create();
                $profile = $profileResourceModel->loadActiveProfile($meta->getId());

                $metaResourceModel->delete($meta);
                $profileResourceModel->delete($profile);

                $sequenceTable = $salesSetup->getTable(sprintf('sequence_%s_%s', $entityType, $store->getId()));
                $setup->getConnection()->dropTable($sequenceTable);
            }
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function removeConfigurationValues(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->delete(
            $setup->getTable('core_config_data'),
            ['path LIKE ?' => 'cart2quote_quotation%']
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function dropTables(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->dropTable($setup->getTable('quotation_quote'));
        $setup->getConnection()->dropTable($setup->getTable('quotation_quote_sections'));
        $setup->getConnection()->dropTable($setup->getTable('quotation_quote_section_items'));
        $setup->getConnection()->dropTable($setup->getTable('quotation_quote_status'));
        $setup->getConnection()->dropTable($setup->getTable('quotation_quote_status_state'));
        $setup->getConnection()->dropTable($setup->getTable('quotation_quote_status_label'));
        $setup->getConnection()->dropTable($setup->getTable('quotation_quote_status_history'));
        $setup->getConnection()->dropTable($setup->getTable('quotation_quote_tier_item'));
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function dropColumns(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->dropColumn($setup->getTable('quote'), 'is_quotation_quote');
        $setup->getConnection()->dropColumn($setup->getTable('quote'), 'linked_quotation_id');
    }
}