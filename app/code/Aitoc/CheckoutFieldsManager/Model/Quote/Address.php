<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Quote;

use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory as ResourceModelAttributeCollectionFactory;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Model\Address\Config as ModelAddressConfig;
use Magento\Customer\Model\Address\Mapper;
use Magento\Directory\Helper\Data;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Eav\Model\Config as EavModelConfig;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject\Copy;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote\Address\CustomAttributeListInterface;
use Magento\Quote\Model\Quote\Address\ItemFactory;
use Magento\Quote\Model\Quote\Address\RateCollectorInterfaceFactory;
use Magento\Quote\Model\Quote\Address\RateFactory;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Quote\Model\Quote\Address\Total\CollectorFactory as ModelQuoteAddressTotalCollectorFactory;
use Magento\Quote\Model\Quote\Address\TotalFactory;
use Magento\Quote\Model\Quote\Address\Validator;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\Quote\Model\Quote\TotalsReader;
use Magento\Quote\Model\ResourceModel\Quote\Address\Item\CollectionFactory as ResourceModelQuoteAddressItemCollectionFactory;
use Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory as ResourceModelQuoteAddressRateCollectionFactory;
use Magento\Shipping\Model\CarrierFactoryInterface;

class Address extends \Magento\Quote\Model\Quote\Address
{
    protected $collectionFactoryAttributes;
    
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $directoryData,
        EavModelConfig $eavConfig,
        ModelAddressConfig $addressConfig,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory,
        AddressMetadataInterface $metadataService,
        AddressInterfaceFactory $addressDataFactory,
        RegionInterfaceFactory $regionDataFactory,
        DataObjectHelper $dataObjectHelper,
        ScopeConfigInterface $scopeConfig,
        ItemFactory $addressItemFactory,
        ResourceModelQuoteAddressItemCollectionFactory $itemCollectionFactory,
        RateFactory $addressRateFactory,
        RateCollectorInterfaceFactory $rateCollector,
        ResourceModelQuoteAddressRateCollectionFactory $rateCollectionFactory,
        RateRequestFactory $rateRequestFactory,
        ModelQuoteAddressTotalCollectorFactory $totalCollectorFactory,
        TotalFactory $addressTotalFactory,
        Copy $objectCopyService,
        CarrierFactoryInterface $carrierFactory,
        Validator $validator,
        Mapper $addressMapper,
        CustomAttributeListInterface $attributeList,
        TotalsCollector $totalsCollector,
        TotalsReader $totalsReader,
        ResourceModelAttributeCollectionFactory $collectionFactoryAttributes,
        $resource = null,
        $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $directoryData,
            $eavConfig,
            $addressConfig,
            $regionFactory,
            $countryFactory,
            $metadataService,
            $addressDataFactory,
            $regionDataFactory,
            $dataObjectHelper,
            $scopeConfig,
            $addressItemFactory,
            $itemCollectionFactory,
            $addressRateFactory,
            $rateCollector,
            $rateCollectionFactory,
            $rateRequestFactory,
            $totalCollectorFactory,
            $addressTotalFactory,
            $objectCopyService,
            $carrierFactory,
            $validator,
            $addressMapper,
            $attributeList,
            $totalsCollector,
            $totalsReader,
            $resource,
            $resourceCollection,
            $data
        );
        $this->collectionFactoryAttributes = $collectionFactoryAttributes;
    }

    protected function getCustomAttributesCodes()
    {
        $attributesCollection = $this->collectionFactoryAttributes->create();
        $attributesCollection->getItems();
        return $attributesCollection->getColumnValues('attribute_code');
    }
}
