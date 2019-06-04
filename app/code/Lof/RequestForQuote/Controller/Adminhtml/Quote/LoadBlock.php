<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\RequestForQuote\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;

class LoadBlock extends Action
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    protected $resultPageFactory;

    protected $quoteFactory;

    protected $rfqQuoteFactory;

    protected $quoteRepository;

    protected $_coreRegistry;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Lof\RequestForQuote\Model\QuoteFactory $rfqQuoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct(
            $context
        );
        $this->resultRawFactory = $resultRawFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->quoteFactory = $quoteFactory;
        $this->rfqQuoteFactory = $rfqQuoteFactory;
        $this->quoteRepository = $quoteRepository;
        $this->_coreRegistry = $coreRegistry;
    }

    public function execute()
    {
        $request = $this->getRequest();
        $entityId = $request->getParam('entity_id');
        $model = $this->_objectManager->create('Lof\RequestForQuote\Model\Quote');

        if ($entityId) {
            $model->load($entityId);
            if ($model->getId()) {
                $mageQuote = $this->quoteRepository->get($model->getQuoteId());
                $this->_coreRegistry->register('mage_quote', $mageQuote);
            } else {
                $this->messageManager->addError(__('This quote no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/index');
            }
        }

        try {
            $this->processRequest();

            $this->_coreRegistry->unregister('mage_quote');
            $model = $this->_objectManager->create('Lof\RequestForQuote\Model\Quote');
            if ($entityId) {
                $model->load($entityId);
                if ($model->getId()) {
                    $mageQuote = $this->quoteRepository->get($model->getQuoteId());
                    $this->_coreRegistry->register('mage_quote', $mageQuote);
                } else {
                    $this->messageManager->addError(__('This quote no longer exists.'));
                    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/index');
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_reloadQuote();
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_reloadQuote();
            $this->messageManager->addException($e, $e->getMessage());
        }

        $asJson = $request->getParam('json');
        $block  = $request->getParam('block');

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if ($asJson) {
            $resultPage->addHandle('quotation_quote_load_block_json');
        } else {
            $resultPage->addHandle('quotation_quote_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $resultPage->addHandle('quotation_quote_load_block_' . $block);
            }
        }

        $result = $resultPage->getLayout()->renderElement('content');
        if ($request->getParam('as_js_varname')) {
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setUpdateResult($result);
            return $this->resultRedirectFactory->create()->setPath('quotation/quote/showUpdateResult');
        }
        return $this->resultRawFactory->create()->setContents($result);
    }

    public function processRequest()
    {
        if ($this->getRequest()->isAjax()) {
            $entity_id = $this->getRequest()->getParam('entity_id');
            $items = $this->getRequest()->getPostValue('item');
            $collectShippingRates = $this->getRequest()->getPostValue('collect_shipping_rates');

            //Tam thoi dang su dung cac thu vien cua module Magento_Sales
            //Se thay the trong cac phien ban sau.
            $data = $this->getRequest()->getPost('order');


            if ($items) {
                $model = $this->_objectManager->create('Lof\RequestForQuote\Model\Quote');
                $model->load($entity_id);
                $mageQuote = $model->getMageQuote();

                foreach ($items as $productId => $productData) {
                    $product = $this->_objectManager->create(
                        \Magento\Catalog\Model\Product::class
                    )->load(
                        $productId
                    );

                    $config = new \Magento\Framework\DataObject($productData);

                    $mageQuote->addProduct($product, $config);
                }

                $mageQuote->getShippingAddress()->setShippingMethod(false);
                $mageQuote->getShippingAddress()->removeAllShippingRates();
                $mageQuote->collectTotals();
                $this->quoteRepository->save($mageQuote);
            }

            if ($collectShippingRates) {
                $quote = $this->getQuote();
                $quote->getShippingAddress()->setCollectShippingRates(true);
                $quote->collectTotals();

                $this->quoteRepository->save($quote);
            }

            if (isset($data['shipping_method'])) {
                $quote = $this->getQuote();
                $shippingAddress = $quote->getShippingAddress();
                $shippingAddress->setShippingMethod($data['shipping_method']);

                if (isset($data['fixed_shipping_price'])) {
                    $shippingRate = $shippingAddress->getShippingRateByCode($data['shipping_method']);
                    if ($shippingRate) {
                        $shippingRate->setPrice($data['fixed_shipping_price']);
                        $shippingRate->save();
                    }
                }

                $quote->collectTotals();

                $this->quoteRepository->save($quote);
            }

            if ( isset($data['subtotal_proposal']) && $data['subtotal_proposal'] !== '' ) {
                $quote = $this->getQuote();    
                $proposalTotal = 0;

                $originalSubtotal = 0;
                foreach ($quote->getAllVisibleItems() as $item) {
                    $originalSubtotal += $item->getOriginalPrice() * $item->getQty(); 
                }   
                if( isset($data['is_percentage']) && $data['is_percentage'] == 'true' ){ 
                    $subtotalProposal = (float)$originalSubtotal * (float)$data['subtotal_proposal'] / 100; 
                }else{
                    $subtotalProposal = (float)$data['subtotal_proposal'];
                }
                $pricePerTotal = $subtotalProposal/$originalSubtotal;  
                foreach ($quote->getAllVisibleItems() as $item) { 
                    $itemCustomPrice = $item->getOriginalPrice();
                    $itemCustomPrice = isset($itemCustomPrice) ? $itemCustomPrice : $item->getPrice(); 
                    $newProductPrice = number_format((float)($itemCustomPrice * $pricePerTotal), 2, '.', '');
                    $item->setCustomPrice((float)$newProductPrice);
                    $item->setOriginalCustomPrice((float)$newProductPrice);
                    $item->setIsSuperMode(true); 
                }  

                $adjustSubtotal =  (float)$subtotalProposal - (float)$originalSubtotal;
                $quote->setAdjustSubtotal($adjustSubtotal);
                $quote->collectTotals(); 
                $this->quoteRepository->save($quote);
            }
        }
    }

    public function getQuote()
    {
        $quoteId = $this->rfqQuoteFactory->create()
            ->load($this->getRequest()->getParam('entity_id'))
            ->getQuoteId();

        $quote = $this->quoteFactory->create()
            ->load($quoteId);

        return $quote;
    }
}
