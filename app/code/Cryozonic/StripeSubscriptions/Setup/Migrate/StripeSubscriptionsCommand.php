<?php

namespace Cryozonic\StripeSubscriptions\Setup\Migrate;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class StripeSubscriptionsCommand extends Command
{
    // public function __construct(
    //     \Magento\Framework\App\State $state
    // ) {
    //     $state->setAreaCode('frontend');
    //     parent::__construct();
    // }

    protected function configure()
    {
        $this->setName('cryozonic:stripe-subscriptions:migrate-stripe-subscriptions');
        $this->setDescription('Updates existing subscriptions in your Stripe account to work with the Magento 2 module.');
        $this->addArgument('Stripe Secret API Key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initStripePayments($output);

        \Stripe\Stripe::setApiVersion("2018-01-23");
        \Stripe\Stripe::setApiKey($input->getArgument('Stripe Secret API Key'));

        $profiles = $this->fetchRecurringProfiles();

        $lastSubscriptionID = null;
        do
        {
            $params = array(
                'limit' => 100,
            );
            if ($lastSubscriptionID)
                $params['starting_after'] = $lastSubscriptionID;

            $subscriptions = \Stripe\Subscription::all($params);

            foreach ($subscriptions->data as $subscription)
            {
                $output->writeln("Updating subscription " . $subscription->id);
                if (isset($profiles[$subscription->id]))
                {
                    $profile = $profiles[$subscription->id];
                    try
                    {
                        $metadata = $this->getSubscriptionMetadata($profile);
                        $subscription['metadata'] = $metadata;
                        $subscription->save();
                    }
                    catch (\Exception $e)
                    {
                        $output->writeln("ERROR: " . $e->getMessage());
                    }
                }
                else
                {
                    $output->writeln("ERROR: Could not find a subscription with this ID in the Magento 1 database.");
                }
                $lastSubscriptionID = $subscription->id;
            }
        } while ($subscriptions->has_more);
    }

    protected function initStripePayments($output)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $storeManager->setCurrentStore(0);

        $reader = $objectManager->get('Magento\Framework\Module\Dir\Reader');
        $moduleDir = $reader->getModuleDir('', 'Cryozonic_StripePayments');

        $filename = $moduleDir . "/lib/stripe-php/vendor/autoload.php";
        if (!class_exists('\Stripe\Stripe') && file_exists($filename))
            require_once $filename;
        else
            throw new \Exception("Could not load the Stripe Payments module dependency");
    }

    protected function fetchRecurringProfiles()
    {
        $profiles = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $select = $connection->select()
            ->from(array('srp' => 'sales_recurring_profile'), array('*'))
            ->joinLeft(array('srpo' => 'sales_recurring_profile_order'), 'srp.profile_id = srpo.profile_id', array('order_id'))
            ->joinLeft(array('sfo' => 'sales_order'), 'order_id = sfo.entity_id', array('increment_id'));
        $data = $connection->fetchAll($select);
        foreach ($data as $row)
        {
            $profiles[$row['reference_id']] = $row;
        }
        return $profiles;
    }

    protected function getSubscriptionMetadata($profile)
    {
        $orderInfo = unserialize($profile['order_info']);
        $shippingInfo = unserialize($profile['shipping_address_info']);
        $item = unserialize($profile['order_item_info']);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderRepository = $objectManager->get('Magento\Sales\Api\OrderRepositoryInterface');

        $metadata = array(
            "Product ID" => $item['product_id'],
            "Customer ID" => $orderInfo['customer_id'],
            "Order #" => $profile['increment_id'],
            "Module" => \Cryozonic\StripePayments\Model\Config::$moduleName . " v" . \Cryozonic\StripePayments\Model\Config::$moduleVersion,
        );

        if (!empty($shippingInfo))
        {
            $metadata["Shipping First Name"] = $shippingInfo['firstname'];
            $metadata["Shipping Last Name"] = $shippingInfo['lastname'];
            $metadata["Shipping Street"] = $shippingInfo['street'];
            $metadata["Shipping City"] = $shippingInfo['city'];
            $metadata["Shipping Region"] = $shippingInfo['region'];
            $metadata["Shipping Postcode"] = $shippingInfo['postcode'];
            $metadata["Shipping Country"] = $shippingInfo['country_id'];
            $metadata["Shipping Telephone"] = $shippingInfo['telephone'];
        }

        return $metadata;
    }
}
