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

namespace Lof\RequestForQuote\Controller\Adminhtml\Quote;

class CreateOrder extends \Magento\Backend\App\Action
{

    /**
     * @var
     */
    protected $_layout;

    /**
     * @var
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * CreateOrder constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Backend\Model\Session\Quote $sessionQuote
    ) {
        parent::__construct($context);
        $this->_sessionQuote   = $sessionQuote;
        $this->quoteRepository = $quoteRepository;
//        $this->registry = $registry;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $id = $this->getRequest()->getParam('entity_id');
        if ($id) {
            try {
                $quote = $this->quoteRepository->get($this->getRequest()->getParam('magequote_id'));
                $this->_sessionQuote->setCustomerId($quote->getCustomerId());
                $mageQuote = $this->_sessionQuote->getQuote();
                $mageQuote->merge($quote);

                $mageQuote->getShippingAddress()->setCollectShippingRates(true);
                $mageQuote->collectTotals();
                $this->quoteRepository->save($mageQuote);

                if($quote->getShippingAddress()->getShippingMethod()) {

                    $mageQuote->getShippingAddress()
                        ->setShippingMethod($quote->getShippingAddress()->getShippingMethod());

                    $shippingRatePrice = $quote->getShippingAddress()
                        ->getShippingRateByCode($quote->getShippingAddress()->getShippingMethod())
                        ->getPrice();

                    $mageQuote->getShippingAddress()
                        ->getShippingRateByCode($mageQuote->getShippingAddress()->getShippingMethod())
                        ->setPrice($shippingRatePrice);

                    $quote->collectTotals();

                    $this->quoteRepository->save($mageQuote);

                }

                $rfqQuote = $this->_objectManager->create('Lof\RequestForQuote\Model\Quote');
                $rfqQuote->load($id)->setTargetQuote($mageQuote->getId())->save();

                $this->_eventManager->dispatch(
                                'lof_rfq_controller_create_order',
                                ['mage_quote' => $mageQuote, 'lof_quote' => $rfqQuote]
                            );

//                $this->registry->register("quoteId_ordered", ['quote' => $quote, 'mageQuote' => $mageQuote]);
                // go to grid
                $resultRedirect->setUrl($this->getUrl('sales/order_create/index'));

                return $resultRedirect;         
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a quote to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}