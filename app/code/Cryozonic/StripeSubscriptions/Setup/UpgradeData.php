<?php

namespace Cryozonic\StripeSubscriptions\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Cryozonic\StripePayments\Helper\Logger;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    protected $categorySetupFactory;

    public function __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory,
        \Magento\Eav\Model\Entity\Attribute\GroupFactory $attributeGroupFactory,
        \Magento\Eav\Model\AttributeManagement $attributeManagement,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory
    ) {
        $this->_categorySetupFactory = $categorySetupFactory;
        $this->_eavTypeFactory = $eavTypeFactory;
        $this->_attributeFactory = $attributeFactory;
        $this->_attributeSetFactory = $attributeSetFactory;
        $this->_attributeGroupFactory = $attributeGroupFactory;
        $this->_attributeManagement = $attributeManagement;
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_groupCollectionFactory = $groupCollectionFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') <= 0)
        {
            try
            {
                $this->initSubscriptions($setup);
            }
            catch (\Exception $e)
            {
                // already exists
            }

            try
            {
                $this->addTrialDays($setup);
            }
            catch (\Exception $e)
            {
                // already exists
            }
        }

        if (version_compare($context->getVersion(), '1.1.0') <= 0)
        {
            try
            {
                $this->addInitialFee($setup);
                $this->renameBillingInterval($setup);
            }
            catch (\Exception $e)
            {
                // already exists
            }
        }

        $setup->endSetup();
    }

    private function initSubscriptions($setup)
    {
        $groupName = 'Stripe Subscriptions';

        $attributes = [
            'cryozonic_sub_enabled' => [
                'type'                  => 'int',
                'label'                 => 'Subscription Enabled',
                'input'                 => 'boolean',
                'source'                => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'sort_order'            => 100,
                'global'                => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group'                 => $groupName,
                'is_used_in_grid'       => false,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => false,
                'used_for_promo_rules'  => true,
                'required'              => false
            ],
            'cryozonic_sub_interval' => [
                'type'                  => 'varchar',
                'label'                 => 'Billing Interval',
                'input'                 => 'select',
                'source'                => 'Cryozonic\StripeSubscriptions\Model\Adminhtml\Source\BillingInterval',
                'sort_order'            => 110,
                'global'                => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group'                 => $groupName,
                'is_used_in_grid'       => false,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => false,
                'used_for_promo_rules'  => true,
                'required'              => false
            ],
            'cryozonic_sub_interval_count' => [
                'type'                  => 'varchar',
                'label'                 => 'Billing Interval Count',
                'input'                 => 'text',
                'sort_order'            => 120,
                'global'                => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group'                 => $groupName,
                'is_used_in_grid'       => false,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => false,
                'used_for_promo_rules'  => true,
                'required'              => false
            ]
        ];

        $categorySetup = $this->_categorySetupFactory->create(['setup' => $setup]);

        foreach ($attributes as $code => $params)
            $categorySetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, $code, $params);

        $this->sortGroup($groupName, 11);
    }

    private function sortGroup($attributeGroupName, $order)
    {
        $entityType = $this->_eavTypeFactory->create()->loadByCode('catalog_product');
        $setCollection = $this->_attributeSetFactory->create()->getCollection();
        $setCollection->addFieldToFilter('entity_type_id', $entityType->getId());

        foreach ($setCollection as $attributeSet)
        {
            $group = $this->_groupCollectionFactory->create()
                ->addFieldToFilter('attribute_set_id', $attributeSet->getId())
                ->addFieldToFilter('attribute_group_name', $attributeGroupName)
                ->getFirstItem()
                ->setSortOrder($order)
                ->save();
        }

        return true;
    }

    private function addTrialDays($setup)
    {
        $groupName = 'Stripe Subscriptions';

        $params = [
            'type'                  => 'int',
            'label'                 => 'Trial Days',
            'input'                 => 'text',
            'sort_order'            => 130,
            'global'                => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'group'                 => $groupName,
            'is_used_in_grid'       => false,
            'is_visible_in_grid'    => false,
            'is_filterable_in_grid' => false,
            'used_for_promo_rules'  => true,
            'required'              => false
        ];

        $categorySetup = $this->_categorySetupFactory->create(['setup' => $setup]);
        $categorySetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'cryozonic_sub_trial', $params);
    }

    private function addInitialFee($setup)
    {
        $groupName = 'Stripe Subscriptions';

        $params = [
            'type'                  => 'decimal',
            'label'                 => 'Initial Fee',
            'input'                 => 'text',
            'sort_order'            => 140,
            'global'                => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'group'                 => $groupName,
            'is_used_in_grid'       => false,
            'is_visible_in_grid'    => false,
            'is_filterable_in_grid' => false,
            'used_for_promo_rules'  => true,
            'required'              => false
        ];

        $categorySetup = $this->_categorySetupFactory->create(['setup' => $setup]);
        $categorySetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'cryozonic_sub_initial_fee', $params);
    }

    private function renameBillingInterval($setup)
    {
        $groupName = 'Stripe Subscriptions';

        $attributes = [
            'cryozonic_sub_interval' => [
                'type'                  => 'varchar',
                'label'                 => 'Frequency',
                'input'                 => 'select',
                'source'                => 'Cryozonic\StripeSubscriptions\Model\Adminhtml\Source\BillingInterval',
                'sort_order'            => 110,
                'global'                => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group'                 => $groupName,
                'is_used_in_grid'       => false,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => false,
                'used_for_promo_rules'  => true,
                'required'              => false
            ],
            'cryozonic_sub_interval_count' => [
                'type'                  => 'varchar',
                'label'                 => 'Repeat Every',
                'note'                  => 'Enter a number to use with Frequency',
                'input'                 => 'text',
                'sort_order'            => 120,
                'global'                => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group'                 => $groupName,
                'is_used_in_grid'       => false,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => false,
                'used_for_promo_rules'  => true,
                'required'              => false
            ]
        ];

        $categorySetup = $this->_categorySetupFactory->create(['setup' => $setup]);

        foreach ($attributes as $code => $params)
            $categorySetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, $code, $params);
    }
}
