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

namespace Cart2Quote\Quotation\Model\Quote\Pdf;

/**
 * Quote PDF model
 */
class Quote extends \Cart2Quote\Quotation\Model\Quote\Pdf\AbstractPdf
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Address\Renderer|\Magento\Sales\Model\Order\Address\Renderer
     */
    protected $_addressRenderer;
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;
    /**
     * @var array
     */
    protected $quotes;

    /**
     * @var \Magento\Framework\Translate
     */
    protected $translate;

    /**
     * Quote constructor.
     * @param \Magento\Framework\Translate $translate
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sales\Model\Order\Pdf\Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param Items\QuoteItem $renderer
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Translate $translate,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Items\QuoteItem $renderer,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        array $data = []
    ) {
        $this->translate = $translate;
        $this->fileFactory = $fileFactory;
        $this->_storeManager = $storeManager;
        $this->localeResolver = $localeResolver;
        $this->_addressRenderer = $addressRenderer;
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $renderer,
            $data
        );
    }

    /**
     * Set Pdf model
     *
     * @param  \Cart2Quote\Quotation\Model\Quote\Pdf\AbstractPdf $pdf
     * @return $this
     */
    public function setPdf(\Cart2Quote\Quotation\Model\Quote\Pdf\AbstractPdf $pdf)
    {
        $this->_pdf = $pdf;

        return $this;
    }

    /**
     * Creates the Quote PDF and return the filepath
     *
     * @param array $quotes
     * @return string|null
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function createQuotePdf(array $quotes)
    {
        $this->setQuotes($quotes);
        $pdf = $this->getPdf();
        if (isset($pdf)) {
            $fileName = sprintf('quotation/%s.pdf', $this->getIncrementId($quotes));
            $this->_mediaDirectory->writeFile(
                $fileName,
                $pdf->render()
            );

            return $fileName;
        }

        return null;
    }

    /**
     * Get PDF document
     * @return \Zend_Pdf
     * @internal param array|\Cart2Quote\Quotation\Traits\Model\Quote\Pdf\Collection $quotes
     */
    public function getPdf()
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('quotation');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($this->getQuotes() as $quote) {
            if ($quote->getStoreId()) {
                $this->setPdfLocale($quote->getStoreId());
            }
            $store = $quote->getStore();
            $page = $this->newPage();

            /* Add image */
            $this->insertLogo($page, $store);

            /* Add address */
            $this->insertAddress($page, $store);

            /* Add quote data */
            $this->insertQuote($page, $quote);
            /**
             * @var \Cart2Quote\Quotation\Model\Quote $quote
             */
            foreach ($quote->getSections() as $section) {
                $sectionItems = $quote->getSectionItems($section->getSectionId());
                if (count($sectionItems)) {
                    if ($section->getLabel()) {
                        $this->_drawSectionHeader($page, $section);
                    }
                    /* Add table */
                    $this->_drawHeader($page);
                    /* Add body */
                    foreach ($sectionItems as $item) {
                        if ($item->getParentItem() && ($item->getParentItem()->getProductType() != 'bundle')) {
                            continue;
                        }
                        /* Draw item */
                        $this->_drawQuoteItem($item, $page, $quote);
                        $page = end($pdf->pages);
                    }
                }
            }

            /* Add totals */
            $totalsY = $this->y;
            $this->insertTotals($page, $quote);

            /* Draw Comments */
            $this->y = $totalsY;
            if ($quote->getCustomerNoteNotify() && $quote->getCustomerNote() != '') {
                $this->insertComments($page, $quote);
            }

            $page = end($pdf->pages);
            $this->insertFooter($page);

            if ($quote->getStoreId()) {
                $this->localeResolver->revert();
            }
        }
        $this->_afterGetPdf();

        return $pdf;
    }

    /**
     * Get array of quotes
     *
     * @return array
     */
    public function getQuotes()
    {
        return $this->quotes;
    }

    /**
     * Set array of quotes
     *
     * @param array $quotes
     * @return $this
     * @throws \Exception
     */
    public function setQuotes(array $quotes)
    {
        foreach ($quotes as $quote) {
            if (!$quote instanceof \Cart2Quote\Quotation\Model\Quote) {
                throw new \Exception(__('Invalid quote class provided for the PDF. ' .
                    'Expected class \Cart2Quote\Quotation\Model\Quote'));
            }
        }

        $this->quotes = $quotes;

        return $this;
    }

    /**
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface $section
     */
    protected function _drawSectionHeader(
        \Zend_Pdf_Page $page,
        \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface $section
    ) {
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y + 10, 570, $this->y - 35);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = [
            'text' => $section->getLabel(),
            'feed' => 35,
            'font_size' => 15,
            'font' => 'bold',
        ];
        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
    }

    /**
     * @param \Zend_Pdf_Page $page
     * @param array $draw
     * @param array $pageSettings
     * @return \Zend_Pdf_Page
     */
    public function drawLineBlocks(\Zend_Pdf_Page $page, array $draw, array $pageSettings = [])
    {
        return parent::drawLineBlocks($page, $draw, $pageSettings);
    }

    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Products'), 'feed' => 35];
        $lines[0][] = ['text' => __('SKU'), 'feed' => 220, 'align' => 'right'];
        $lines[0][] = ['text' => __('Qty'), 'feed' => 420, 'align' => 'right'];
        $lines[0][] = ['text' => __('Price'), 'feed' => 360, 'align' => 'right'];
        $lines[0][] = ['text' => __('Tax'), 'feed' => 488, 'align' => 'right'];
        $lines[0][] = ['text' => __('Subtotal'), 'feed' => 560, 'align' => 'right'];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 10;
    }

    /**
     * Get array of increments
     *
     * @param array $quotes
     * @return string
     */
    public function getIncrementId(array $quotes)
    {
        $incrementIds = [];
        foreach ($quotes as $quote) {
            $incrementIds[] = $quote->getIncrementId();
        }

        return implode("-", $incrementIds);
    }

    /**
     * Set the correct store locale to the PDF
     *
     * @param int $storeId
     */
    public function setPdfLocale($storeId)
    {
        $locale = $this->localeResolver->emulate($storeId);
        $this->translate->setLocale($locale);
        $this->translate->loadData();
    }
}
