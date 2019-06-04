<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller;

use MageWorx\Downloads\Helper\Download as DownloadHelper;
use Magento\Framework\App\Action\Context;

abstract class Download extends \Magento\Framework\App\Action\Action
{
    /**
     * @var DownloadHelper
     */
    private $downloadHelper;

    /**
     * Download constructor.
     * @param DownloadHelper $downloadHelper
     * @param Context $context
     */
    public function __construct(
        DownloadHelper $downloadHelper,
        Context $context
    ) {
        $this->downloadHelper = $downloadHelper;

        parent::__construct($context);
    }

    /**
     * Prepare response to output resource contents
     *
     * @param string $path         Path to resource
     * @param string $resourceType Type of resource (see Magento\Downloadable\Helper\Download::LINK_TYPE_* constants)
     * @return void
     */
    protected function _processDownload($path, $resourceType)
    {
        $this->downloadHelper->setResource($path, $resourceType);
        
        $fileName    = $this->downloadHelper->getFilename();
        $contentType = $this->downloadHelper->getContentType();
        $this->getResponse()->setHttpResponseCode(
            200
        )->setHeader(
            'Pragma',
            'public',
            true
        )->setHeader(
            'Cache-Control',
            'must-revalidate, post-check=0, pre-check=0',
            true
        )->setHeader(
            'Content-type',
            $contentType,
            true
        );

        if ($fileSize = $this->downloadHelper->getFileSize()) {
            $this->getResponse()->setHeader('Content-Length', $fileSize);
        }

        if ($contentDisposition = $this->downloadHelper->getContentDisposition()) {
            $this->getResponse()->setHeader('Content-Disposition', $contentDisposition . '; filename=' . $fileName);
        }

        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();

        $this->downloadHelper->output();
    }
}
