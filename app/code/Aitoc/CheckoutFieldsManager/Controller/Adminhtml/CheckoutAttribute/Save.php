<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

use Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;
use Aitoc\CheckoutFieldsManager\Helper\Data;
use Aitoc\CheckoutFieldsManager\Helper\Serializer;
use Aitoc\CheckoutFieldsManager\Model\Entity\AttributeFactory;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\Product\Url;
use Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends CheckoutAttribute
{
    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ValidatorFactory $validatorFactory
     * @param CollectionFactory $groupCollectionFactory
     * @param FilterManager $filterManager
     * @param Url $ulrGenerator
     * @param AttributeFactory $checkoutEavFactory
     * @param Data $helper
     * @param Serializer $serializer
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ValidatorFactory $validatorFactory,
        CollectionFactory $groupCollectionFactory,
        FilterManager $filterManager,
        Url $ulrGenerator,
        AttributeFactory $checkoutEavFactory,
        Data $helper,
        Serializer $serializer
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $ulrGenerator, $checkoutEavFactory);
        $this->filterManager = $filterManager;
        $this->helper = $helper;
        $this->validatorFactory = $validatorFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->serializer = $serializer;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $redirectBack = $this->getRequest()->getParam('back', false);
            /** @var $model \Aitoc\CheckoutFieldsManager\Model\Entity\Attribute */
            $model = $this->checkoutEavFactory->create();

            $attributeId = $this->getRequest()->getParam('attribute_id');
            $attributeCode = $this->getRequest()->getParam('attribute_code');
            $frontendLabel = $this->getRequest()->getParam('frontend_label');
            $attributeCode = $attributeCode ?: $this->generateCode($frontendLabel[0]);
            if (($this->getRequest()->getParam('attribute_code')) !== '') {
                $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                            'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );

                    return $this->_redirect(
                        'aitoccheckoutfieldsmanager/*/edit',
                        ['attribute_id' => $attributeId, '_current' => true]
                    );
                }
            }
            $data['attribute_code'] = $attributeCode;

            //validate frontend_input
            if (isset($data['frontend_input'])) {
                /** @var $inputType \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator */
                $inputType = $this->validatorFactory->create();
                $inputType->addInputType('radiobutton')
                    ->addInputType('checkbox')
                    ->addInputType('label');
                if (!$inputType->isValid($data['frontend_input'])) {
                    foreach ($inputType->getMessages() as $message) {
                        $this->messageManager->addErrorMessage($message);
                    }

                    return $this->_redirect(
                        'aitoccheckoutfieldsmanager/*/edit',
                        ['attribute_id' => $attributeId, '_current' => true]
                    );
                }
            }

            if ($attributeId) {
                $model->load($attributeId);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));

                    return $this->_redirect('aitoccheckoutfieldsmanager/*/');
                }
                // entity type check
                if ($model->getEntityTypeId() != $this->entityTypeId) {
                    $this->messageManager->addErrorMessage(__('We can\'t update the attribute.'));
                    $this->_session->setCheckoutAttributeData($data);

                    return $this->_redirect('aitoccheckoutfieldsmanager/*/');
                }

                $data['attribute_code']  = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input']  = $model->getFrontendInput();
            }

            if ($data['frontend_input'] == 'label') {
                $data['is_required'] = 0;
            }

            if (($model->getIsUserDefined() === null) || ($model->getIsUserDefined() != 0)) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }
            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);

            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            $validationRulesEnabledFor = ['text', 'textarea'];
            $isValidateRulesEnabled = in_array($data['frontend_input'], $validationRulesEnabledFor);

            $data['validate_rules'] = ($isValidateRulesEnabled && $data['frontend_class'])
                ? $this->serializer->serialize([$data['frontend_class'] => true])
                : ''
            ;

            $model->addData($data);

            if (!$attributeId) {
                $model->setEntityTypeId($this->entityTypeId);
                $model->setIsUserDefined(1);
            }

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the checkout attribute.'));

                $this->_session->setCheckoutAttributeData(false);
                if ($redirectBack) {
                    return $this->_redirect(
                        'aitoccheckoutfieldsmanager/*/edit',
                        ['attribute_id' => $model->getId(), '_current' => true]
                    );
                }

                return $this->_redirect('aitoccheckoutfieldsmanager/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setCheckoutAttributeData($data);

                return $this->_redirect(
                    'aitoccheckoutfieldsmanager/*/edit',
                    ['attribute_id' => $attributeId, '_current' => true]
                );
            }
        }

        return $this->_redirect('aitoccheckoutfieldsmanager/*/');
    }
}
