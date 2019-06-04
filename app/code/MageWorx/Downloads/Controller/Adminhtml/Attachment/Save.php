<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use MageWorx\Downloads\Controller\Adminhtml\Attachment as AttachmentController;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Registry;
use MageWorx\Downloads\Model\AttachmentFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\Downloads\Model\Attachment\Link as FileLinkModel;
use MageWorx\Downloads\Model\Upload;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Framework\Filesystem;
use MageWorx\Downloads\Model\Attachment\Source\ContentType;
use MageWorx\Downloads\Model\Attachment\Source\AssignType;

class Save extends AttachmentController
{
    /**
     * Attachment factory
     * @var \MageWorx\Downloads\Model\AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * File model
     *
     * @var FileLinkModel
     */
    protected $fileLinkModel;

    /**
     * Upload model
     *
     * @var \MageWorx\Downloads\Model\Upload
     */
    protected $uploadModel;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     *
     * @param JsHelper $jsHelper
     * @param FileLinkModel $fileLinkModel
     * @param Upload $uploadModel
     * @param Registry $registry
     * @param AttachmentFactory $attachmentFactory
     * @param Filesystem $fileSystem
     * @param Context $context
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        JsHelper $jsHelper,
        FileLinkModel $fileLinkModel,
        Upload $uploadModel,
        Registry $registry,
        AttachmentFactory $attachmentFactory,
        Filesystem $fileSystem,
        Context $context
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->jsHelper                 = $jsHelper;
        $this->fileLinkModel            = $fileLinkModel;
        $this->uploadModel              = $uploadModel;
        $this->fileSystem               = $fileSystem;
        parent::__construct($registry, $attachmentFactory, $context);
    }


    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('attachment');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->prepareData($data);
            $attachment = $this->initAttachment();
            $attachment->addData($data);

            try {
                if ($attachment->getContentType() == ContentType::CONTENT_FILE) {
                    $file = $this->uploadModel->uploadFileAndGetName('filename', $this->fileLinkModel->getBaseDir(), $data);

                    if ($file) {
                        $attachment->setFilename($file);
                        $attachment->setType(substr($file, strrpos($file, '.') + 1));
                        $attachment->setUrl('');
                        $attachment->setSize(filesize($this->fileLinkModel->getBaseDir() . $file));
                    }
                } elseif ($attachment->getContentType() == ContentType::CONTENT_URL) {
                    $attachment->setFilename('');
                    $attachment->setType('');
                }

                $productIds = $this->getProductIds($data);
                $attachment->setProductsData($productIds);

                $this->_eventManager->dispatch(
                    'mageworx_downloads_attachment_prepare_save',
                    [
                        'attachment' => $attachment,
                        'request' => $this->getRequest()
                    ]
                );
                $attachment->getResource()->save($attachment);
                $this->messageManager->addSuccessMessage(__('The attachment has been saved.'));
                $this->_getSession()->setMageWorxDownloadsAttachmentData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageworx_downloads/*/edit',
                        [
                            'attachment_id' => $attachment->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageworx_downloads/*/');
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->_getSession()->setMageWorxDownloadsAttachmentData($data);
            $resultRedirect->setPath(
                'mageworx_downloads/*/edit',
                [
                    'attachment_id' => $attachment->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }

        $resultRedirect->setPath('mageworx_downloads/*/');
        return $resultRedirect;
    }

    /**
     *
     * @param array $data
     * @return array
     */
    protected function getProductIds($data)
    {
        $productIds = null;

        if ($data['assign_type'] == AssignType::ASSIGN_BY_GRID) {
            $products = $this->getRequest()->getPost('products', -1);
            if ($products != -1) {
                $productIds = $this->jsHelper->decodeGridSerializedInput($products);
            }
        } elseif ($data['assign_type'] == AssignType::ASSIGN_BY_IDS) {
            $productIds  = $this->convertMultiStringToArray($data['productids'], 'intval');
        } elseif ($data['assign_type'] == AssignType::ASSIGN_BY_SKUS) {
            $productSkus = $this->convertMultiStringToArray($data['productskus']);

            if ($productSkus) {
                $collection = $this->productCollectionFactory->create();
                $collection->addFieldToFilter('sku', ['in' => $productSkus]);
                $productIds = array_map('intval', $collection->getAllIds());
            }
        }

        return $productIds;
    }

    /**
     *
     * @param string $string
     * @param string $finalFunction
     * @return array
     */
    protected function convertMultiStringToArray($string, $finalFunction = null)
    {
        if (!trim($string)) {
            return [];
        }

        $rawLines = array_filter(preg_split('/\r?\n/', $string));
        $rawLines = array_map('trim', $rawLines);
        $lines = array_filter($rawLines);

        if (!$lines) {
            return [];
        }

        $array = [];
        foreach ($lines as $line) {
            $rawIds  = explode(',', $line);
            $rawIds  = array_map('trim', $rawIds);
            $lineIds = array_filter($rawIds);
            if (!$finalFunction) {
                $lineIds = array_map($finalFunction, $lineIds);
            }
            $array = array_merge($array, $lineIds);
        }

        return $array;
    }

    /**
     * Prepares specific data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        if (array_search(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $data['stores']) !== false) {
            $data['stores'] = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }

        return $data;
    }
}
