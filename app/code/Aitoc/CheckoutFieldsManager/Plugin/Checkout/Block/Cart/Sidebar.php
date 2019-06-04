<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Plugin\Checkout\Block\Cart;

use Magento\Checkout\Block\Cart\Sidebar as CartSidebar;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Sidebar
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scope;

    /**
     * Sidebar constructor.
     *
     * @param ScopeConfigInterface $scope
     */
    public function __construct(
        ScopeConfigInterface $scope
    ) {
        $this->scope = $scope;
    }

    /**
     * @param CartSidebar $object
     * @param $config
     *
     * @return mixed
     */
    public function afterGetConfig(CartSidebar $object, $config)
    {
        $status = $this->scope->getValue('checkoutfieldsmanager/general/disable_cart');
        $config['additional_cfm'] = ['disable_cart' => $status];

        return $config;
    }
}
