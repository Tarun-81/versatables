<?php

namespace Cryozonic\StripeSubscriptions\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Cryozonic\StripePayments\Helper\Logger;
use Cryozonic\StripePayments\Model\PaymentMethod;
use Cryozonic\StripeSubscriptions\Model\Config;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
        // \Magento\Setup\Module\SetupFactory $setupFactory,
        // \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        // $this->_setupFactory = $setupFactory;
        // $this->_context = $context;
        // $this->_setup = $this->_setupFactory->create($this->_context->getResources());
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->endSetup();
    }
}
