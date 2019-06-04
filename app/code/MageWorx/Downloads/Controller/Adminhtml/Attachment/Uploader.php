<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

class Uploader extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_Downloads::attachments';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \MageWorx\Downloads\Model\Upload
     */
    protected $uploadModel;

    /**
     * @var \MageWorx\Downloads\Model\Attachment\Link
     */
    protected $fileLinkModel;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \MageWorx\Downloads\Model\Attachment\Link $fileLinkModel,
        \MageWorx\Downloads\Model\Upload $uploadModel
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->uploadModel      = $uploadModel;
        $this->fileLinkModel    = $fileLinkModel;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            $file = $this->uploadModel->uploadFileAndGetName('multifile', $this->fileLinkModel->getBaseDir(), []);

            $fileDataAfterUpload = $this->uploadModel->getFileDataAfterUpload();

            $result = [];
            $result['file'] = $file;
            $result['url']  = $this->fileLinkModel->getBaseUrl() . $result['file'];
            $result['name'] = $fileDataAfterUpload['name'];
            $result['size'] = $fileDataAfterUpload['size'];

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }

    /**
     * Is action allowed
     * (only constant doesn't work in 2.0.x)
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_Downloads::attachments');
    }
}
