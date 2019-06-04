<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

/**
 * Aitoc Checkout Attribute controller
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml;

use Aitoc\CheckoutFieldsManager\Model\Entity\AttributeFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\Product\Url;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

abstract class CheckoutAttribute extends Action
{
    /**
     * @var string
     */
    protected $entityTypeId;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Url
     */
    protected $ulrGenerator;

    /**
     * @var AttributeFactory
     */
    protected $checkoutEavFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param Url $ulrGenerator
     * @param AttributeFactory $checkoutEavFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        Url $ulrGenerator,
        AttributeFactory $checkoutEavFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->ulrGenerator = $ulrGenerator;
        $this->checkoutEavFactory = $checkoutEavFactory;
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $this->entityTypeId = $this->_objectManager
            ->create('Magento\Eav\Model\Entity')
            ->setType('aitoc_checkout')
            ->getTypeId();

        return parent::dispatch($request);
    }

    /**
     * Generate code from label
     *
     * @param string $label
     *
     * @return string
     * @throws \Zend_Validate_Exception
     */
    protected function generateCode($label)
    {
        $code = substr(
            preg_replace('/[^a-z_0-9]/', '_', $this->ulrGenerator->formatUrlKey($label)),
            0,
            30
        );
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }

        return $code;
    }

    /**
     * @param \Magento\Framework\Phrase|null $title
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
        }
        $resultPage->getConfig()->getTitle()->prepend(__('Checkout Attributes'));

        return $resultPage;
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aitoc_CheckoutFieldsManager::attributes');
    }
}
