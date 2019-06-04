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

namespace Cart2Quote\Quotation\Model\Admin\Quote;

use Magento\Quote\Model\Quote\Item;

/**
 * Class Create
 * @package Cart2Quote\Quotation\Model\Admin\Quote
 */
class Create extends \Magento\Sales\Model\AdminOrder\Create
{

    /**
     * Tier item factory
     *
     * @var \Cart2Quote\Quotation\Model\Quote\TierItemFactory
     */
    protected $tierItemFactory;

    /**
     * Tier item collection factory
     *
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory
     */
    protected $tierItemCollectionFactory;

    /**
     * The calculation quote (duplicate of the original)
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $calculationQuote;
    
    /**
     * @var \Magento\CatalogInventory\Model\StockStateProvider
     */
    protected $stockStateProvider;

    /**
     * Create constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer $quoteInitializer
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\AdminOrder\EmailSender $emailSender
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param Item\Updater $quoteItemUpdater
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param \Magento\Quote\Api\CartManagementInterface $quoteManagement
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory
     * @param \Magento\CatalogInventory\Model\StockStateProvider $stockStateProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer $quoteInitializer,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\AdminOrder\EmailSender $emailSender,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Quote\Model\Quote\Item\Updater $quoteItemUpdater,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory,
        \Magento\CatalogInventory\Model\StockStateProvider $stockStateProvider,
        array $data = []
    ) {
        parent::__construct(
            $objectManager,
            $eventManager,
            $coreRegistry,
            $salesConfig,
            $quoteSession,
            $logger,
            $objectCopyService,
            $messageManager,
            $quoteInitializer,
            $customerRepository,
            $addressRepository,
            $addressFactory,
            $metadataFormFactory,
            $groupRepository,
            $scopeConfig,
            $emailSender,
            $stockRegistry,
            $quoteItemUpdater,
            $objectFactory,
            $quoteRepository,
            $accountManagement,
            $customerFactory,
            $customerMapper,
            $quoteManagement,
            $dataObjectHelper,
            $orderManagement,
            $quoteFactory,
            $data
        );

        // Overwrite the Magento quote session with the Quotation Session.
        $this->_session = $quotationSession;
        $this->tierItemFactory = $tierItemFactory;
        $this->tierItemCollectionFactory = $tierItemCollectionFactory;
        $this->stockStateProvider = $stockStateProvider;
    }

    /**
     * Update tier items of quotation items
     *
     * @param array $items
     * @return $this
     * @throws \Exception|\Magento\Framework\Exception\LocalizedException
     */
    public function updateTierItems($items)
    {
        if (!is_array($items)) {
            return $this;
        }

        try {
            foreach ($items as $itemId => $info) {
                if (!empty($info['tier_item']) && is_array($info['tier_item'])) {
                    $item = $this->getQuote()->getItemById($itemId);
                    if (!$item->isDeleted() &&
                        !(isset($info['action']) && $info['action'] == 'remove')) {
                        $selectedTierId = $info['selected_tier'];
                        $newTierItems = [];
                        unset($info['tier_item']['%template%']);

                        if (isset($info['tier_item']['new'])) {
                            $this->processItems($item, $info['tier_item']['new']);
                            $this->processCustomPrice($info['tier_item']['new']);
                            $newTierItems = $info['tier_item']['new'];
                            unset($info['tier_item']['new']);
                        }

                        $info['tier_item'] = $this->processOptionalValues($info['tier_item']);

                        $this->processCustomPrice($info['tier_item']);
                        $existingTierItems = $this->processExistingTierItems($item, $info);
                        $existingTierItems = $this->processNewTierItems($existingTierItems, $newTierItems, $item);
                        $existingTierItems = $this->calculateTierPrices($existingTierItems, $itemId, $selectedTierId);

                        if ($selectedTier = $existingTierItems->getItemById($selectedTierId)) {
                            $this->selectTierItem($selectedTier, $item);
                            $existingTierItems->setItemTiers($item, $selectedTierId);
                        }

                        $this->recollectCart();
                    } elseif ($item->isDeleted() && isset($info['configured']) && $info['configured'])  {
                        foreach ($this->getQuote()->getAllItems() as &$updatedItem) {
                            if (!$updatedItem->isDeleted()) {
                                $buyRequest = $updatedItem->getBuyRequest();
                                if ($buyRequest->getSelectedTier() == $info['selected_tier']) {
                                    $updatedItem->setData('tier_items', $item->getTierItems());
                                    $updatedItem->setData('current_tier_item', $item->getCurrentTierItem());
                                    break;
                                }
                            }
                        }

                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->recollectCart();
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }

        return $this;
    }

    /**
     * Process optional values
     * Set the "on" value to true for saving
     *
     * @param array $tierItems
     * @return array
     */
    protected function processOptionalValues(array $tierItems)
    {
        foreach ($tierItems as &$tierItem) {
            if (isset($tierItem['make_optional']) && $tierItem['make_optional'] == "on") {
                $tierItem['make_optional'] = true;
            }
        }

        return $tierItems;
    }

    /**
     * Check for allowed custom price value
     *
     * @param array $tierItems
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processCustomPrice(array $tierItems)
    {
        foreach ($tierItems as &$tierItem) {
            if ($tierItem['custom_price'] <= 0) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a number 0 or greater in this field.'));
            }
        }
    }

    /**
     * Process item for quantity check
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $tierItems
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processItems($item, array $tierItems)
    {
        $product = $item->getProduct();
        foreach ($tierItems as &$tierItem) {
            switch ($product->getTypeId()) {
                case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                    $this->processBundle($item, $tierItem['qty']);
                    break;
                case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                    $this->processConfigurable($item, $tierItem['qty']);
                    break;
                default:
                    $this->processQuantity($product->getProductId(), $tierItem['qty']);
                    break;
            }
        }
    }

    /**
     * Process bundle product for quantity check
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $tierItemQty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processBundle($item, $tierItemQty)
    {
        $children = $item->getChildren();
        if (isset($children)) {
            foreach ($children as $bundleItem) {
                $qty = $bundleItem->getQty();
                $finalQty = $tierItemQty * $qty;
                $this->processQuantity($bundleItem->getProductId(), $finalQty);
            }
        }
    }

    /**
     * Process configurable product for quantity check
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $tierItemQty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processConfigurable($item, $tierItemQty)
    {
        $children = $item->getChildren();
        if (isset($children[0])) {
            $productId = $children[0]->getProductId();
            if (isset($productId)) {
                $this->processQuantity($productId, $tierItemQty);
            }
        }
    }

    /**
     * Check tier quantity for stock settings
     *
     * @param int $productId
     * @param int $qty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processQuantity($productId, $qty)
    {
        $stockItem = $this->stockRegistry->getStockItem($productId);
        $result = $this->stockStateProvider->checkQuoteItemQty(
            $stockItem,
            $qty,
            $qty,
            $qty
        );

        if ($result->getHasError()
            || $result->getMessage()
            && $stockItem->getBackorders() == \Magento\CatalogInventory\Model\Stock::BACKORDERS_NO
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__($result->getMessage()));
        }
    }

    /**
     * Process existing tier items
     *
     * @param int $item
     * @param array $info
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    protected function processExistingTierItems($item, $info)
    {
        $tierItemCollection = $this->tierItemCollectionFactory->create();
        $tierItemCollection->getTierItemsByItemId($item->getId());

        /**
         * @var int $id
         * @var \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
         */
        foreach ($tierItemCollection as $id => $tierItem) {
            if (isset($info['tier_item'][$id])) {
                $tierItem = $tierItemCollection->getItemById($id);
                $tierItem->setData(array_replace($tierItem->getData(), $info['tier_item'][$id]));
            } else {
                $tierItem->delete();
            }
        }

        return $tierItemCollection;
    }

    /**
     * Process new tier items
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $existingTiers
     * @param array $newTierItems
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    protected function processNewTierItems(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $existingTiers,
        $newTierItems,
        $item
    ) {
        $existingQtys = [];
        if (count($newTierItems)) {
            $existingQtys = $existingTiers->getQtys();
        }

        foreach ($newTierItems as $newTierItem) {
            if (in_array(($newTierItem['qty'] * 1), $existingQtys)) {
                continue;
            }

            $tierItem = $this->tierItemFactory->createFromItem($item, $newTierItem['qty']);
            $tierItem->setCustomPrice($newTierItem['custom_price']);
            $tierItem->setBaseCustomPrice(
                $this->getQuote()->convertPriceToQuoteBaseCurrency($newTierItem['custom_price'])
            );

            $existingTiers->addItem($tierItem);
        }

        return $existingTiers;
    }

    /**
     * Calculate tier price
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemCollection
     * @param $quoteItemId
     * @param $selectedTierId
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    protected function calculateTierPrices(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemCollection,
        $quoteItemId,
        $selectedTierId
    ) {
        $quoteItem = $this->getQuoteItemByPreviousId($this->getCalculationQuote(), $quoteItemId);

        if ($quoteItem instanceof \Magento\Quote\Model\Quote\Item) {
            /** @var \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem */
            foreach ($tierItemCollection as &$tierItem) {
                if ($this->_needCollect && !$tierItem->isDeleted()) {
                    $tierId = $tierItem->getId();
                    $quoteItem = $tierItem->setItem($quoteItem)->setSelected();
                    $this->getCalculationQuote()->setTotalsCollectedFlag(false)->collectTotals();

                    $tierItem = $tierItem->setData(array_replace($tierItem->getData(), $quoteItem->getData()))
                        ->setId($tierId)
                        ->setItemId($quoteItemId)
                        ->save();

                    if ($quoteItem->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                        && $tierId == $selectedTierId) {
                        foreach ($quoteItem->getChildren() as $child) {
                            if ($currentTierItem = $child->getCurrentTierItem()) {
                                $currentTierItem->save();
                            }
                        }
                    }
                }
            }
        }

        return $tierItemCollection;
    }

    /**
     * Get quote item by previous quote id
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $previousQuoteId
     * @return bool|\Magento\Quote\Model\Quote\Item
     */
    protected function getQuoteItemByPreviousId(\Magento\Quote\Model\Quote $quote, $previousQuoteId)
    {
        $children = [];
        $previousQuoteItem = false;

        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        foreach ($quote->getAllItems() as &$quoteItem) {
            if ($quoteItem->getParentItemId() == $previousQuoteId) {
                $children[] = $quoteItem;
            }

            if ($quoteItem->getPreviousId() == $previousQuoteId) {
                $previousQuoteItem = $quoteItem;
            }
        }

        if (count($children) && $previousQuoteItem instanceof \Magento\Quote\Model\Quote\Item) {
            foreach ($children as $child) {
                $previousQuoteItem->addChild($child);
            }
        }

        return $previousQuoteItem;
    }

    /**
     * Get the calculation quote
     * This is a different quote that doesn't interfere with the original quotation quote.
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function getCalculationQuote()
    {
        if ($this->calculationQuote == null) {
            $this->calculationQuote = $this->getQuote()->copy($this->getQuote());
        }

        return $this->calculationQuote;
    }

    /**
     * Select the tier item
     *
     * @param $tierItem
     * @param \Magento\Quote\Model\Quote\Item &$item
     * @return void
     */
    protected function selectTierItem($tierItem, \Magento\Quote\Model\Quote\Item &$item)
    {
        $item->setQty($tierItem->getQty());
    }
}
