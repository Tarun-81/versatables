<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Plugin\Checkout\Model;

use Aitoc\CheckoutFieldsManager\Model\Entity\Attribute;
use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\Collection;
use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;

class CheckoutDefaultProvider
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scope;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * CheckoutDefaultProvider constructor.
     *
     * @param ScopeConfigInterface $scope
     * @param UrlInterface $urlBuilder
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        ScopeConfigInterface $scope,
        UrlInterface $urlBuilder,
        CollectionFactory $collectionFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->scope = $scope;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param DefaultConfigProvider $object
     * @param $config
     *
     * @return mixed
     */
    public function afterGetConfig(DefaultConfigProvider $object, $config)
    {
        $status = $this->scope->getValue('checkoutfieldsmanager/general/read_cart_in_checkout');
        $config['additional_cfm'] =
            [
                'read_cart_in_checkout' => $status,
                'updateItemQtyUrl' => $this->getUpdateItemQtyUrl(),
                'removeItemUrl' => $this->getRemoveItemUrl(),
                'updateConfig' => $this->getUpdateConfig(),
                'attributes' => $this->getAttributesConfig(),
            ];

        return $config;
    }

    /**
     * Get update cart item url
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getUpdateItemQtyUrl()
    {
        return $this->getUrl('checkout/sidebar/updateItemQty');
    }

    /**
     * Get remove cart item url
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getRemoveItemUrl()
    {
        return $this->getUrl('checkout/sidebar/removeItem');
    }

    /**
     * Get update cart item url
     *
     * @return string
     */
    public function getUpdateConfig()
    {
        return $this->getUrl('aitoccheckoutfieldsmanager/updatecart/index');
    }

    /**
     * @param $route
     * @param array $params
     *
     * @return string
     */
    protected function getUrl($route, $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }

    /**
     * @return array
     */
    private function getAttributesConfig()
    {
        $collection = $this->collectionFactory->create();

        return $this->attributeCollectionToConfig($collection);
    }

    /**
     * @param Collection $collection
     * @return array
     */
    private function attributeCollectionToConfig(Collection $collection)
    {
        $ret = [];

        /** @var Attribute $attribute */
        foreach ($collection as $attribute) {
            $ret[$attribute->getAttributeCode()] = $this->attributeToConfig($attribute);
        }

        return $ret;
    }

    /**
     * @param Attribute $attribute
     * @return array
     */
    private function attributeToConfig(Attribute $attribute)
    {
        return [
            'label' => $attribute->getStoreLabel(),
            'frontend_input' => $attribute->getFrontendInput(),
            'options' => $this->getAttributeOptionsConfig($attribute),
            'is_visible' => $attribute->getIsVisible(),
        ];
    }

    /**
     * @param Attribute $attribute
     * @return array|null
     */
    private function getAttributeOptionsConfig(Attribute $attribute)
    {
        if (in_array($attribute->getFrontendInput(), ['select', 'multiselect', 'checkbox', 'radiobutton'])) {
            return $this->getOptionedAttributeOptionsConfig($attribute);
        } else {
            return null;
        }
    }

    /**
     * @param Attribute $attribute
     * @return array
     */
    private function getOptionedAttributeOptionsConfig(Attribute $attribute)
    {
        $options = $attribute->getOptions();

        $optionsConfig = [];

        foreach ($options as $option) {
            if ($optionValue = $option->getValue()) {
                $optionsConfig[$option->getValue()] = $option->getLabel();
            }
        }

        return $optionsConfig;
    }
}
