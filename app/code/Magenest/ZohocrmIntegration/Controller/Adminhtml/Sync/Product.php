<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync;

use Magenest\ZohocrmIntegration\Model\Sync;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Class Product
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync
 */
class Product extends Action
{
    /**
     * @var Sync\Product
     */
    protected $_product;

    /**
     * Customer constructor.
     * @param Context $context
     * @param Sync\Product $product
     */
    public function __construct(
        Context $context,
        Sync\Product $product
    ) {
        $this->_product = $product;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {
            $response = $this->_product->syncAllQueue();
            $this->messageManager->addSuccess(
                __('Product is synced successfully')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Something happen during syncing process. Detail: ' . $e->getMessage() . '. Response Log: '.serialize($response))
            );
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::config_zohocrm');
    }
}
