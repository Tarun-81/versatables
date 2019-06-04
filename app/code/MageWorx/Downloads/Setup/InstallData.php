<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Install default section
         */
        $bind = [
            'section_id' => \MageWorx\Downloads\Model\Section::DEFAULT_ID,
            'name'        => 'Default',
            'description' => 'Default section',
            'is_active'   => \MageWorx\Downloads\Model\Section::STATUS_ENABLED
        ];
        $setup->getConnection()->insertOnDuplicate($setup->getTable('mageworx_downloads_section'), $bind);
    }
}
