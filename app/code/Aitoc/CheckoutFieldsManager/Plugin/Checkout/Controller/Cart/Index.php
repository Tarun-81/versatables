<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Plugin\Checkout\Controller\Cart;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Controller\Cart\Index as CartIndex;

class Index
{
    /**
     * @var ScopeConfigInterface
     */
    private $scope;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var Http
     */
    private $response;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param ScopeConfigInterface $scope
     * @param UrlInterface $url
     * @param Http $response
     * @param Session $checkoutSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        ScopeConfigInterface $scope,
        UrlInterface $url,
        Http $response,
        Session $checkoutSession,
        PageFactory $resultPageFactory
    ) {
        $this->scope = $scope;
        $this->url = $url;
        $this->response = $response;
        $this->checkoutSession = $checkoutSession;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart\Index $object
     *
     * @return void
     */
    public function beforeExecute(CartIndex $object)
    {
        $status = $this->scope->getValue('checkoutfieldsmanager/general/disable_cart');
        if ($status) {
            $quote = $this->checkoutSession->getQuote();
            $redirectUrl = $this->url->getUrl('checkout');
            /** if no items in checkout, redirect by "continue shopping" link */
            if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
                $redirectUrl = $this->resultPageFactory->create()
                    ->getLayout()
                    ->getBlock('checkout.cart')
                    ->getContinueShoppingUrl();
            }
            $this->response->setRedirect($redirectUrl);
        }
    }
}
