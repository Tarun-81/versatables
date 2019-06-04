<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Osc\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
	/**
	 * @var QuoteSetupFactory
	 */
	protected $quoteSetupFactory;

	/**
	 * @var SalesSetupFactory
	 */
	protected $salesSetupFactory;

	/**
	 * @param QuoteSetupFactory $quoteSetupFactory
	 * @param SalesSetupFactory $salesSetupFactory
	 */
	public function __construct(
		QuoteSetupFactory $quoteSetupFactory,
		SalesSetupFactory $salesSetupFactory
	)
	{
		$this->quoteSetupFactory = $quoteSetupFactory;
		$this->salesSetupFactory = $salesSetupFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		/** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
		$quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

		/** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
		$salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

		$setup->startSetup();
		if (version_compare($context->getVersion(), '2.1.0') < 0) {
			$entityAttributesCodes = [
				'osc_gift_wrap_amount'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
				'base_osc_gift_wrap_amount' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL
			];
			foreach ($entityAttributesCodes as $code => $type) {
				$quoteInstaller->addAttribute('quote_address', $code, ['type' => $type, 'visible' => false]);
				$quoteInstaller->addAttribute('quote_item', $code, ['type' => $type, 'visible' => false]);
				$salesInstaller->addAttribute('order', $code, ['type' => $type, 'visible' => false]);
				$salesInstaller->addAttribute('order_item', $code, ['type' => $type, 'visible' => false]);
				$salesInstaller->addAttribute('invoice', $code, ['type' => $type, 'visible' => false]);
				$salesInstaller->addAttribute('creditmemo', $code, ['type' => $type, 'visible' => false]);
			}

			$quoteInstaller->addAttribute('quote_address', 'used_gift_wrap', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN, 'visible' => false]);
			$quoteInstaller->addAttribute('quote_address', 'gift_wrap_type', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 'visible' => false]);
			$salesInstaller->addAttribute('order', 'gift_wrap_type', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 'visible' => false]);

		}

		if (version_compare($context->getVersion(), '2.1.1') < 0) {
			$salesInstaller->addAttribute('order', 'osc_delivery_time', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'visible' => false]);
		}
		if (version_compare($context->getVersion(), '2.1.2') < 0) {
			$salesInstaller->addAttribute('order', 'osc_survey_question', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'visible' => false]);
			$salesInstaller->addAttribute('order', 'osc_survey_answers', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'visible' => false]);
		}
        if (version_compare($context->getVersion(), '2.1.3') < 0) {
            $salesInstaller->addAttribute('order', 'osc_order_house_security_code', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'visible' => false]);
        }

		$setup->endSetup();
	}
}
