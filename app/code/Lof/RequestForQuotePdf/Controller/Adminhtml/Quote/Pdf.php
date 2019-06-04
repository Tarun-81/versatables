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

namespace Lof\RequestForQuotePdf\Controller\Adminhtml\Quote;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;

class Pdf extends \Magento\Backend\App\Action
{

    protected $_layout;
    protected $layoutFactory;
    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Lof\RequestForQuote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Lof\RequestForQuotePdf\Model\Quote\Pdf\Quote $pdf,
        \Magento\Framework\Registry $coreRegistry,
        LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
        $this->quoteFactory = $quoteFactory;
        $this->quoteRepository = $quoteRepository;
        $this->_file = $file;
        $this->_fileFactory = $fileFactory;
        $this->_pdf = $pdf;
        $this->_coreRegistry          = $coreRegistry;
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
        $id = $this->getRequest()->getParam('quote_id');

        if ($id) {
            try {
                // init model and update status
                $model = $this->_objectManager->create('\Lof\RequestForQuote\Model\Quote');
                $model->load($id);
                $magequote_id = $model->getQuoteId();
                $mageQuote = $this->quoteRepository->get($magequote_id);
               
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::VAR_DIR);
                $mediaRootDir = $mediaDirectory->getAbsolutePath();
                $quote_increment = $model->getIncrementId();
                $fileName = __('Quote_#');
                $fileName .= $quote_increment.'.pdf';
                if($model && $mageQuote){
                    $this->_coreRegistry->register('mage_quote', $mageQuote);
                    $this->_coreRegistry->register('quotation_quote', $model);

                    $return = $this->_pdf->generatePdf($model, $mageQuote);
                    $pdf_output = isset($return['output'])?$return['output']:'';
                    $this->_eventManager->dispatch(
                                    'lof_rfq_controller_pdf',
                                    ['mage_quote' => $mageQuote, 'lof_quote' => $model, 'pdf' => $pdf_output]
                                );

                    $date = $this->_objectManager->get(
                        \Magento\Framework\Stdlib\DateTime\DateTime::class
                    )->date('Y-m-d_H-i-s');

                    return $this->_fileFactory->create(
                        $fileName,
                        $pdf_output,
                        DirectoryList::VAR_DIR,
                        'application/pdf'
                    );
                } else {
                    $this->messageManager->addError(__("Error: Not Found Quotation!"));
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/');
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find the quote.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}