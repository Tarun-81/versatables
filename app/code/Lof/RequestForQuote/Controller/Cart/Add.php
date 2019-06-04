<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuote\Controller\Cart;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Lof\RequestForQuote\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Lof\RequestForQuote\Model\Cart $cart
     * @param \Magento\Framework\Url $urlBuilder
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Lof\RequestForQuote\Model\Cart $cart,
        \Magento\Framework\Url $urlBuilder
    ) {
        parent::__construct($context);
        $this->_formKeyValidator = $formKeyValidator;
        $this->productRepository = $productRepository;
        $this->_checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('quotation/quote');
        }

        $result = [];
        $params = $this->getRequest()->getParams();

        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }


            $productId = (int)$this->getRequest()->getParam('product');
            $product = $this->_initProduct();
            $this->cart->addProduct($product, $params);
            $this->cart->save();

            $this->_eventManager->dispatch(
                'lof_rfq_controller_cart_add',
                ['mage_cart' => $this->cart, 'product' => $product]
            );

            $child_product = $this->getChildProduct($productId);

            $this->messageManager->addSuccessMessage(__('You added %1 to your quote.', $product->getName()));
            $result['html'] = $this->_view->getLayout()->createBlock("Magento\Framework\View\Element\Template")
                ->setProduct($product)
                ->setChildProduct($child_product)
                ->setTemplate("Lof_RequestForQuote::ajax/success.phtml")
                ->toHtml();
            $result['status'] = true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your quote.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        if (isset($params['ajax'])) {
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
            );
            return;
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }

    /**
     * @param int $productId
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getChildProduct($productId = 0)
    {
        $child_product = null;
        foreach ($this->cart->getQuote()->getAllVisibleItems() as $item) {
            $currentProductId = $item->getProduct()->getId();
            if (($productId == $currentProductId) && ($option = $item->getOptionByCode('simple_product'))) {
                $child_product = $option->getProduct();
                break;
            }
        }
        return $child_product;
    }


    /**
     * Resolve response
     *
     * @param string $backUrl
     * @param \Magento\Catalog\Model\Product $product
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack($backUrl = null, $product = null)
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}