<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Lof\RequestForQuotePdf\Model\Quote\Pdf;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;
/**
 * Sales Order PDF  model
 *
 * @api
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Quote extends \Magento\Framework\DataObject
{
    protected $_layout;
    protected $layoutFactory;
    protected $_coreRegistry;
    protected $_objectManager = null;

	public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Lof\RequestForQuote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Lof\RequestForQuote\Helper\Data $rfqData,
        \Lof\RequestForQuotePdf\Helper\Pdf $pdf,
        \Magento\Framework\Registry $coreRegistry,
        LayoutFactory $layoutFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->layoutFactory = $layoutFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteRepository = $quoteRepository;
        $this->_file = $file;
        $this->_fileFactory = $fileFactory;
        $this->_rfqData = $rfqData;
        $this->_pdf = $pdf;
        $this->_coreRegistry          = $coreRegistry;
    }
    public function getObjectManager(){
        if(!$this->_objectManager){
            $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        }
        return $this->_objectManager;
    }
    public function generatePdf($quote, $mageQuote, $generate_file = false){
        $return = [];
        if($quote && $mageQuote){
            $mediaDirectory = $this->getObjectManager()->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::VAR_DIR);
            $mediaRootDir = $mediaDirectory->getAbsolutePath();
            $quote_increment = $quote->getIncrementId();
            $fileName = __('Quote_#');
            $fileName .= $quote_increment.'.pdf';
            $pdfFilePath = $mediaRootDir . $fileName;
            if ($this->_file->isExists($pdfFilePath))  { 
                $this->_file->deleteFile($pdfFilePath);
            }
            $content_html = $this->getHtmlForPdf($quote, $mageQuote);
            $this->_pdf->setData($content_html);
            $pdf_output = $this->_pdf->renderOutput();
            if($generate_file) {
                $this->_fileFactory->create(
                            $fileName,
                            $pdf_output,
                            DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
            }
            $return["output"] = $pdf_output;
            $return['filename'] = $fileName;
        }
        return $return;
    }
    public function getHtmlForPdf($quote, $mageQuote)
    {
        $block = $this->layoutFactory->create()->createBlock('Lof\RequestForQuotePdf\Block\Adminhtml\Quote\QuotePdf');
        $block->setTemplate('Lof_RequestForQuotePdf::quote/pdf.phtml');

        $items_block = $this->layoutFactory->create()->createBlock('Lof\RequestForQuotePdf\Block\Adminhtml\Quote\QuotePdf\Items');
        $items_block->setTemplate('Lof_RequestForQuotePdf::quote/pdf/items.phtml');

        $totals_block = $this->layoutFactory->create()->createBlock('Lof\RequestForQuotePdf\Block\Adminhtml\Quote\QuotePdf\Totals');
        $totals_block->setTemplate('Lof_RequestForQuotePdf::quote/pdf/totals.phtml');

        $block->addChild("quote.pdf.items", $items_block);
        $block->addChild("quote.pdf.totals", $totals_block);
        
        $data = [
            'quote' => $quote,
            'mage_quote' => $mageQuote
        ];
        $block->setData($data);
        return $block->toHtml();
    }
}