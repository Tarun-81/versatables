<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model;

use Aitoc\CheckoutFieldsManager\Api\Data\AbstractCustomerDataInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Api\ExtensionAttributesInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

abstract class AbstractCustomerData extends AbstractExtensibleModel implements AbstractCustomerDataInterface
{
    /**
     * @var Entity\Attribute
     */
    protected $attr;

    /**
     * AbstractModel constructor.
     *
     * @param Entity\Attribute $attr
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Entity\Attribute $attr,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->attr = $attr;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::KEY_VALUE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeId()
    {
        return $this->getData(self::KEY_ATTRIBUTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData(self::KEY_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode()
    {
        if (!$this->attr->getId()) {
            $this->attr->load($this->getAttributeId());
        }
        return $this->attr->getAttributeCode();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($valueId)
    {
        $this->setData(self::KEY_VALUE_ID, $valueId);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeId($attrId)
    {
        $this->setData(self::KEY_ATTRIBUTE_ID, $attrId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->setData(self::KEY_VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        ExtensionAttributesInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
