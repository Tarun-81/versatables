<?php

namespace Cryozonic\StripeSubscriptions\Setup\Migrate;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductConfigurationCommand extends Command
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Framework\App\State $state
    ) {
        $this->productCollection = $productCollection;
        $this->state = $state;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cryozonic:stripe-subscriptions:migrate-products-configuration');
        $this->setDescription('Copies the settings from the "Migrate_Recurring Profile" attribute group into the "Stripe Subscriptions" attribute group.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('frontend');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->productCollection = $objectManager->get('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');

        $products = $this->productCollection->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_recurring', 1)
            ->load();

        if ($products->count() == 0)
        {
            $output->writeln("Could not find any products with an enabled Recurring Profile");
            return;
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('Cryozonic\StripePayments\Helper\Generic');

        foreach ($products as $product)
        {
            $output->writeln("Migrating " . $product->getName());
            $profile = unserialize($product->getRecurringProfile());

            $productModel = $helper->loadProductById($product->getId());
            $productModel->setCryozonicSubEnabled(1);
            if ($profile['period_unit'] == 'semi_month')
            {
                $profile['period_unit'] = 'week';
                $profile['period_frequency'] = $profile['period_frequency'] / 2;
            }
            $productModel->setCryozonicSubInterval($profile['period_unit']);
            $productModel->setCryozonicSubIntervalCount((string)$profile['period_frequency']);
            $productModel->setCryozonicSubTrial($this->getTrialDays($profile['trial_period_unit'], $profile['trial_period_frequency']));
            $productModel->setStoreId(0);
            $productModel->save();
        }
    }

    public function getTrialDays($unit, $frequency)
    {
        $days = 0;
        switch ($unit) {
            case 'day':
                $days = $frequency;
                break;
            case 'week':
                $days = $frequency * 7;
                break;
            case 'semi_month':
                $days = $frequency * 14;
                break;
            case 'month':
                $days = $frequency * 30;
                break;
            case 'year':
                $days = $frequency * 356;
                break;
            default:
                break;
        }
        return $days;
    }
}
