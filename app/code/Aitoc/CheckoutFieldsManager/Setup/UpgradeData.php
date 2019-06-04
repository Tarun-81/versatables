<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Setup;

use Aitoc\CheckoutFieldsManager\Model\Entity\Attribute;
use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory;
use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Entity\Attribute as EntityAttributeResource;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Aitoc\CheckoutFieldsManager\Helper\Serializer;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var EntityAttributeResource
     */
    private $entityAttributeResource;

    /**
     * UpgradeData constructor.
     * @param CollectionFactory $collectionFactory
     * @param Serializer $serializer
     * @param EntityAttributeResource $entityAttributeResource
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Serializer $serializer,
        EntityAttributeResource $entityAttributeResource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->serializer = $serializer;
        $this->entityAttributeResource = $entityAttributeResource;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->updateValidationRulesByFrontendClass();
        }

        $setup->endSetup();
    }

    /**
     * @throws \Exception
     */
    private function updateValidationRulesByFrontendClass()
    {
        $attributesCollection = $this->collectionFactory->create();

        foreach ($attributesCollection as $attribute) {
            if ($frontendClass = $attribute->getFrontendClass()) {
                $this->addValidateRule($attribute, $frontendClass);
            }
        }
    }

    /**
     * Add validate rule to attribute.
     *
     * @param Attribute $attribute
     * @param string $validateRule
     * @throws \Exception
     */
    private function addValidateRule(Attribute $attribute, $validateRule)
    {
        $validateRulesSerialized = $attribute->getValidateRules();
        $validateRules = $validateRulesSerialized ? $this->serializer->unserialize($validateRulesSerialized) : [];
        $validateRules[$validateRule] = true;

        $validateRulesSerialized = $this->serializer->serialize($validateRules);
        $attribute->setValidateRules($validateRulesSerialized);
        $this->entityAttributeResource->save($attribute);
    }
}
