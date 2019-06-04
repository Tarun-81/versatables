<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Attachment\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;
use Magento\Framework\File\Size as FileSize;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as ObjectConverter;
use MageWorx\Downloads\Model\SectionFactory;
use MageWorx\Downloads\Model\Attachment\Source\IsActive as IsActiveOptions;
use MageWorx\Downloads\Model\Attachment\Source\AssignType as AssignTypeOptions;
use MageWorx\Downloads\Model\Attachment\Source\ContentType as ContentTypeOptions;

class Main extends GenericForm implements TabInterface
{
    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $store;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $objectConverter;

    /**
     *
     * @var HelperInformer
     */
    protected $helperInformer;

    /**
     *
     * @var AssignTypeOptions
     */
    protected $assignTypeOptions;

    /**
     *
     * @var ContentTypeOptions
     */
    protected $contentTypeOptions;

    /**
     * @var IsActiveOptions
     */
    protected $isActiveOptions;

    /**
     *
     * @var SectionFactory
     */
    protected $sectionFactory;

    /**
     *
     * @param SectionFactory $sectionFactory
     * @param AssignTypeOptions $assignTypeOptions
     * @param ContentTypeOptions $contentTypeOptions
     * @param IsActiveOptions $isActiveOptions
     * @param Store $store
     * @param FileSize $fileSize
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        SectionFactory $sectionFactory,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ObjectConverter $objectConverter,
        AssignTypeOptions $assignTypeOptions,
        ContentTypeOptions $contentTypeOptions,
        IsActiveOptions $isActiveOptions,
        Store $store,
        FileSize $fileSize,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->sectionFactory         = $sectionFactory;
        $this->groupRepository        = $groupRepository;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->objectConverter        = $objectConverter;
        $this->assignTypeOptions      = $assignTypeOptions;
        $this->contentTypeOptions     = $contentTypeOptions;
        $this->isActiveOptions        = $isActiveOptions;
        $this->registry               = $registry;
        $this->fileSize               = $fileSize;
        $this->store                  = $store;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\Downloads\Model\Attachment $attachment */
        $attachment = $this->getAttachment();
        $attachmentData = $this->_session->getData('mageworx_downloads_attachment_data', true);

        if ($attachmentData) {
            $attachment->addData($attachmentData);
        } else {
            if (!$attachment->getId()) {
                $attachment->addData($attachment->getDefaultValues());
            }
        }
        $attachment->setAssignType(AssignTypeOptions::ASSIGN_BY_GRID);

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('attachment_');
        $form->setFieldNameSuffix('attachment');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => $this->getLegendText(),
                'class' => 'fieldset-wide'
            ]
        );

        $fieldset->addType('file', 'MageWorx\Downloads\Block\Adminhtml\Attachment\Helper\File');
        $fieldset->addType('multifile', 'MageWorx\Downloads\Block\Adminhtml\Attachment\Helper\MultiFile');

        if ($attachment->getId()) {
            $fieldset->addField(
                'attachment_id',
                'hidden',
                ['name' => 'attachment_id']
            );
        }

        $sectionList = $this->sectionFactory->create()->getSectionList();

        $fieldset->addField(
            'section_id',
            'select',
            [
                'label' => __('Section'),
                'name' => 'section_id',
                'values' => $sectionList,
                'required' => true
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => (false === strpos($_SERVER['REQUEST_URI'], '/create/')),
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'downloads_limit',
            'text',
            [
                'name' => 'downloads_limit',
                'label' => __('Downloads Limit'),
                'title' => __('Downloads Limit'),
                'class' => 'not-negative-amount integer',
            ]
        );

        $fieldset->addField(
            'assign_type',
            'radios',
            [
                'label' => 'Assign By',
                'name' => 'assign_type',
                'values' => $this->getAssignTypes(),
                'disabled' => false,
                'readonly' => false,
                'after_element_html' => '<br><small>' . __('See the changed tab on the tab list') . '</small>',
            ]
        );

        $reference = $fieldset->addField(
            'content_type',
            'select',
            [
                'label' => 'File / URL Switcher',
                'name' => 'content_type',
                'values' => $this->getContentTypes(),
            ]
        );

        if (false !== strpos($_SERVER['REQUEST_URI'], '/create/')) {
            $file = $fieldset->addField(
                'multifilename',
                'multifile',
                [
                    'label' => __('File(s)'),
                    'title' => __('File(s)'),
                    'name' => 'multifilename',
                    'index' => 'multifilename',
                    'required' => true
                ]
            );
        } else {
            $file = $fieldset->addField(
                'filename',
                'file',
                [
                    'label' => __('File'),
                    'title' => __('File'),
                    'name' => 'filename',
                    'index' => 'filename',
                    'class' => $attachment->getFilename() ? '' : 'required-entry',
                    'required' => $attachment->getFilename() ? false : true,
                    'note' => $this->getMaxUploadSizeMessage()
                ]
            );
        }

        $url = $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'index' => 'url',
                'label' => __('URL'),
                'title' => __('URL'),
                'required' => true,
            ]
        );

        $groups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        $fieldset->addField(
            'customer_group_ids',
            'multiselect',
            [
                'name' => 'customer_group_ids[]',
                'label' => __('Customer Groups'),
                'title' => __('Customer Groups'),
                'required' => true,
                'values' => $this->objectConverter->toOptionArray($groups, 'id', 'code')
            ]
        );

        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name' => 'stores[]',
                    'value' => $this->_storeManager->getStore(true)->getId()
                ]
            );
            $attachment->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->store->getStoreValuesForForm(false, true),
                ]
            );
        }

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Is Active'),
                'title' => __('Is Active'),
                'required' => true,
                'options' => $this->isActiveOptions->toArray()
            ]
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )
                ->addFieldMap($reference->getHtmlId(), $reference->getName())
                ->addFieldMap($file->getHtmlId(), $file->getName())
                ->addFieldMap($url->getHtmlId(), $url->getName())
                ->addFieldDependence(
                    $file->getName(),
                    $reference->getName(),
                    ContentTypeOptions::CONTENT_FILE
                )
                ->addFieldDependence(
                    $url->getName(),
                    $reference->getName(),
                    ContentTypeOptions::CONTENT_URL
                )
        );

        $form->addValues($attachment->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Attachment Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     *
     * @param array $attachmentData
     * @return boolean
     */
    protected function getIsFileRequired($attachmentData)
    {
        if (!empty($attachmentData['filename'])) {
            return false;
        }
        return true;
    }

    /**
     *
     * @return \MageWorx\Downloads\Model\Attachment
     */
    protected function getAttachment()
    {
        return  $this->registry->registry('mageworx_downloads_attachment');
    }

    /**
     * Retrieve filtered by same attachment type assign options
     *
     * @return array
     */
    protected function getAssignTypes()
    {
        return $this->assignTypeOptions->toOptionArray();
    }

    /**
     * Retrieve filtered by same attachment type content options
     *
     * @return array
     */
    protected function getContentTypes()
    {
        return $this->contentTypeOptions->toOptionArray();
    }

    /**
     * Get maximum upload size message
     *
     * @return \Magento\Framework\Phrase
     */
    public function getMaxUploadSizeMessage()
    {
        $maxFileSize = $this->fileSize->getMaxFileSizeInMb();
        if ($maxFileSize) {
            $message = __('The maximum file size limit is %1M.', $maxFileSize);
        } else {
            $message = __('We can\'t provide the upload settings right now.');
        }
        return $message;
    }
}
