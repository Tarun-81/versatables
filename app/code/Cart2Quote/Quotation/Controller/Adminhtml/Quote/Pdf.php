<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;

/**
 * Class Pdf
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Pdf extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Pdf\Quote
     */
    protected $_pdfModel;
    /**
     * Download helper
     *
     * @var \Magento\Downloadable\Helper\Download
     */
    protected $_downloadHelper;

    /**
     * Pdf constructor.
     * @param \Magento\Framework\Escaper $escaper
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Cart2Quote\Quotation\Helper\Data $helperData
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel
     * @param \Magento\Downloadable\Helper\Download $downloadHelper
     * @param \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Escaper $escaper,
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Cart2Quote\Quotation\Helper\Data $helperData,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel,
        \Magento\Downloadable\Helper\Download $downloadHelper,
        \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct(
            $escaper,
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $helperData,
            $quoteFactory,
            $statusCollection,
            $quoteCreate,
            $scopeConfig
        );

        $this->_pdfModel = $pdfModel;
        $this->_downloadHelper = $downloadHelper;
    }

    /**
     * Download PDF for the quotation quote item
     */
    public function execute()
    {
        if ($results = parent::execute()) {
            return $results;
        }

        ini_set('zlib.output_compression', '0');
        $quote = $this->_initQuote();
        $filePath = $this->_pdfModel->createQuotePdf([$quote]);

        $this->_downloadHelper->setResource($filePath, \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE);
        $fileName = $this->_downloadHelper->getFilename();
        $contentType = $this->_downloadHelper->getContentType();
        //$contentDisposition = $this->_downloadHelper->getContentDisposition()
        $contentDisposition = 'attachment';

        $this->getResponse()->setHttpResponseCode(
            200
        )->setHeader(
            'target',
            '_blank',
            true
        )->setHeader(
            'Pragma',
            'public',
            true
        )->setHeader(
            'Cache-Control',
            'private, max-age=0, must-revalidate, post-check=0, pre-check=0',
            true
        )->setHeader(
            'Content-type',
            $contentType,
            true
        );

        if ($fileSize = $this->_downloadHelper->getFileSize()) {
            $this->getResponse()->setHeader('Content-Length', $fileSize);
        }

        $this->getResponse()->setHeader('Content-Disposition', $contentDisposition . '; filename=' . $fileName);

        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();

        $this->_downloadHelper->output();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::actions_view');
    }
}
