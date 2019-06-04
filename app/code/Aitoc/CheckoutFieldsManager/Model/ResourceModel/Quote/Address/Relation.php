<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\ResourceModel\Quote\Address;

use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory as AttributeCollectionFactory;
use Aitoc\CheckoutFieldsManager\Model\ResourceModel\QuoteCustomerData as QuoteCustomerDataResource;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;
use Magento\Framework\Registry;

/**
 * Quote Address relation. After address save, relations will be executed.
 * Address can be shipping or billing
 */
class Relation implements RelationInterface
{
    /** @var QuoteCustomerDataResource */
    private $customerDataResource;

    /** @var AttributeCollectionFactory */
    private $attributeCollectionFactory;

    /** @var Registry */
    private $registry;

    /** @var array */
    private $savedAddress = [];

    /**
     * @param QuoteCustomerDataResource $customerDataResource
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param Registry $registry
     */
    public function __construct(
        QuoteCustomerDataResource $customerDataResource,
        AttributeCollectionFactory $attributeCollectionFactory,
        Registry $registry
    ) {
        $this->customerDataResource = $customerDataResource;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->registry = $registry;
    }

    /**
     * Save Checkout Custom Attributes to additional Quote DB table
     *
     * @param \Magento\Quote\Model\Quote\Address | AbstractModel $address
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processRelation(AbstractModel $address)
    {
        if (!$this->isNedToSave($address)
            || $address->getQuote()->getIsCheckoutCart()
            || ($address->getQuote()->getIsVirtual() && $address->getAddressType() == $address::ADDRESS_TYPE_SHIPPING)
            || $this->registry->registry('cfm_ignore_validation')
        ) {
            return;
        }
        $attributes = $address->getCustomAttributes();
        $attributeIds = $insertData = [];
        /**
         * This collection for validate step fields to required fields and save empty
         * @var \Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\Collection $attrStepCollection
         */
        $attrStepCollection = $this->attributeCollectionFactory->create();
        $attrStepCollection->addEditableFilter()
            ->addFieldToFilter('checkout_step', ['eq' => $address->getAddressType()]);

        if (!empty($attributes)) {
            $attributeCodes = $this->collectAttributeCodes($attributes);
            if (empty($attributeCodes)) {
                return;
            }
            $attrStepCollection->addFieldToFilter('attribute_code', ['nin' => $attributeCodes]);
            /** @var \Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\Collection $attrConfigCollection */
            $attrConfigCollection = $this->attributeCollectionFactory->create();
            $attrConfigCollection->addFieldToFilter('attribute_code', ['in' => $attributeCodes]);

            foreach ($attributes as $attribute) {
                /** @var \Aitoc\CheckoutFieldsManager\Model\Entity\Attribute $attributeConfig */
                $attributeConfig = $attrConfigCollection
                    ->getItemByColumnValue('attribute_code', $attribute->getAttributeCode());

                if ($attributeConfig->getId()) {
                    $attributeIds[] = $attributeConfig->getId();
                    $value = $attribute->getValue();
                    $insertData[]   = [
                        'quote_id' => $address->getQuoteId(),
                        'attribute_id' => $attributeConfig->getId(),
                        'value' => $attributeConfig->processValue($value)
                    ];
                }
            }
        }

        /* add empty attribute values for current step */
        foreach ($attrStepCollection as $attributeConfig) {
            /** @var \Aitoc\CheckoutFieldsManager\Model\Entity\Attribute $attributeConfig */
            $attributeIds[] = $attributeConfig->getId();
            $insertData[]   = [
                'quote_id' => $address->getQuoteId(),
                'attribute_id' => $attributeConfig->getId(),
                'value' => $attributeConfig->processValue('')
            ];
        }

        if (!empty($insertData)) {
            $this->customerDataResource->updateQuoteAttributesData(
                $insertData,
                $address->getQuoteId(),
                array_unique($attributeIds)
            );
        }
        /* For avoid double save */
        $this->savedAddress[$address->getId()] = $address->getId();
    }

    /**
     * Collect attribute codes from CustomAttributes array
     *
     * @param \Magento\Framework\Api\AttributeInterface[] $attributes
     *
     * @return array
     */
    private function collectAttributeCodes($attributes)
    {
        $attributeCodes = [];
        foreach ($attributes as $attribute) {
            $attributeCodes[] = $attribute->getAttributeCode();
        }

        return array_unique($attributeCodes);
    }

    /**
     * Is address already saved
     * Avoiding double save
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return bool
     */
    public function isNedToSave($address)
    {
        if (array_key_exists($address->getId(), $this->savedAddress)) {
            return false;
        }

        return true;
    }
}
