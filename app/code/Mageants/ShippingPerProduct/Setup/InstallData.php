<?php
/**
 * @category   Mageants Shipping Per Product
 * @package    Mageants_Shipping
 * @copyright  Copyright (c) 2016 Mageants
 * @author     Mageants Team <support@mageants.com>
 */
 
namespace Mageants\ShippingPerProduct\Setup;
 
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
 
    /**
     * @var StoreManagerInterface
     */
    private $StoreManager;
 
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param StoreManagerInterface $StoreManager
     */
    public function __construct(EavSetupFactory $eavSetupFactory, StoreManagerInterface $StoreManager)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->StoreManager=$StoreManager;
    }
 
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $context=$context;
        
        /**
         * @var EavSetup $eavSetup
         */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 
        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'shipping_for_product',
            ['group' => 'Product Details',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Shipping For Product',
            'input' => 'text',
            'class' => '',
            'source' => '',
            'global' => 1,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => 'simple'
                ]
        );
        
       /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'calculate_shipping',
            [
                'group' => 'Product Details',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Calculate Shipping',
                'input' => 'select',
                'class' => '',
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'source' => 'Mageants\ShippingPerProduct\Model\Config\Source\Options'
            ]
        );

       /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'enable_shipping_per_product',
            [
                'group' => 'Product Details',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Shipping Per Product',
                'input' => 'select',
                'class' => '',
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'source' => 'Mageants\ShippingPerProduct\Model\Config\Source\Yesno'
            ]
        );
    }
}
