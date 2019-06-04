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
 * Quotation PDF abstract model
 */
abstract class AbstractPdf extends \Magento\Sales\Model\Order\Pdf\AbstractPdf
{
    /**
     * Predefined constants
     */
    const XML_PATH_SALES_PDF_INVOICE_PACKINGSLIP_ADDRESS = 'sales/identity/address';

    /**
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
     * @param Items\QuoteItem $renderer
     * @param array $data
     */
    public function __construct(
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
        \Cart2Quote\Quotation\Model\Quote\Pdf\Items\QuoteItem $renderer,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->_paymentData = $paymentData;
        $this->_localeDate = $localeDate;
        $this->string = $string;
        $this->_scopeConfig = $scopeConfig;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_rootDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $this->_pdfConfig = $pdfConfig;
        $this->_pdfTotalFactory = $pdfTotalFactory;
        $this->_pdfItemsFactory = $pdfItemsFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->_renderer = $renderer;
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
            $data
        );
    }

    /**
     * get StringUtils Object
     * @return \Magento\Framework\Stdlib\StringUtils
     */
    public function getStringUtils()
    {
        return $this->string;
    }

    /**
     * Insert address to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param null $store
     * @return void
     */
    protected function insertAddress(&$page, $store = null)
    {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $font = $this->_setFontRegular($page, 10);
        $page->setLineWidth(0);
        $this->y = $this->y ? $this->y : 815;
        $top = 815;
        $font = $this->_setFontRegular($page, 10);
        $addr = $this->_scopeConfig->getValue(
            self::XML_PATH_SALES_PDF_INVOICE_PACKINGSLIP_ADDRESS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) . "\n";
        $rightAlign = 130;
        if ($addr == "\n") {
            $rightAlign = $rightAlign - 12;
            $font = $this->_setFontBold($page, 13);
            $addr = __("Quotation") . "\n";
        }

        foreach (explode("\n", $addr) as $value) {
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $tst = $this->getAlignRight($_value, $rightAlign, 440, $font, 10);
                    $page->drawText(
                        trim(strip_tags($_value)),
                        350,
                        $top,
                        'UTF-8'
                    );
                    $top -= 10;
                }
            }
        }
        $this->y = $this->y > $top ? $top : $this->y;
    }

    /**
     * Insert General comment to PDF
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function insertFooter(\Zend_Pdf_Page $page)
    {
        $text = $this->_scopeConfig->getValue(
            'cart2quote_pdf/quote/pdf_footer_text',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $boxTop = 35;
        $top = $boxTop + 20;
        $boxHeight = 20;
        $boxMargin = 20;
        $font = $this->_setFontRegular($page, 10);

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        foreach ($this->string->split($text, 100, true, true) as $_value) {
            $boxHeight += 10;
        } 
        //$page->drawRectangle($boxMargin, $boxTop, $page->getWidth() - $boxMargin, $boxTop + $boxHeight);
		
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.20));
		$this->_setFontRegular($page, 12);
		$page->drawText(__('Terms & Conditions:'), 10, $top +15, 'UTF-8');
		
		$page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.20));
		$this->_setFontRegular($page, 10);
        foreach ($this->string->split($text, 130, true, true) as $_value) {
            $feed = 10;

            $page->drawText(
                trim(strip_tags($_value)),
                $feed,
                $top,
                'UTF-8'
            );

            $top -= 10;
        }
    }

    /**
     * Insert quote comment to PDF
     *
     * @param \Zend_Pdf_Page $page
     * @param $quote
     * @return \Zend_Pdf_Page
     * @throws \Zend_Pdf_Exception
     */
    protected function insertComments(\Zend_Pdf_Page $page, $quote)
    {
        //Add title
        $commentLabel = __("Comment with quote: ");
        $comments = array_merge([$commentLabel], explode("\n", $quote->getCustomerNote()));
        $lines = [];
        foreach ($comments as $value) {
            if (!empty($value)) {
                //Replace html breaks with empty strings
                $value = preg_replace('/<br[^>]*>/i', "", $value);
                //Split the string for specified length
                foreach ($this->string->split($value, 70, true, true) as $part) {
                    $lines[] = $part;
                }
            }
        }

        $fontSize = 10;
        $lineCount = count($lines);
        $lineHeight = $fontSize + 2;
        $margin = 10;
        $feed = 35;
        $top = $this->y - $margin;
        $left = $feed - $margin;
        $right = 370;

        $bottom = ($top - ($lineHeight * $lineCount)) - $margin;

        //Draw comment box
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.90));
        $page->drawRectangle($left, $top, $right, $bottom);

        //Draw comments
        $this->_setFontBold($page, $fontSize);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $nextLine = $top;
        foreach ($lines as $line) {
            $page->drawText(trim(strip_tags($line)), $feed, $nextLine -= $lineHeight, 'UTF-8');
            $this->_setFontRegular($page, $fontSize);
        }

        $this->y -= 20;

        return $page;
    }

    /**
     * Insert Quote to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param \Magento\Sales\Model\Order $obj
     * @param bool $putQuoteId
     * @return void
     */
    protected function insertQuote(&$page, $obj, $putQuoteId = true)
    {
        if ($obj instanceof \Cart2Quote\Quotation\Model\Quote) {
            $shipment = null;
            $quote = $obj;
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 75);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 75]);
        $this->_setFontRegular($page, 10);

        if ($putQuoteId) {
            $page->drawText(__('Quotation # ') . $quote->getIncrementId(), 35, $top -= 30, 'UTF-8');
        }
        $page->drawText(
            __('Quotation Date: ') .
            $this->_localeDate->formatDate(
                $this->_localeDate->scopeDate(
                    $quote->getStore(),
                    $quote->getQuotationCreatedAt(),
                    true
                ),
                \IntlDateFormatter::MEDIUM,
                false
            ),
            35,
            $top -= 15,
            'UTF-8'
        );
        if ($quote->getExpiryEnabled() && !is_null($quote->getExpiryDate())) {
            $page->drawText(
                __('Quotation Valid Until: ') .
                $this->_localeDate->formatDate(
                    $this->_localeDate->scopeDate(
                        $quote->getStore(),
                        $quote->getExpiryDate(),
                        true
                    ),
                    \IntlDateFormatter::MEDIUM,
                    false
                ),
                35,
                $top -= 15,
                'UTF-8'
            );
        }

        /**
         * Guest should not display addres or name.
         * still return a white line to ensure layout does not break.
         */
        if ($quote->getCustomerIsGuest()) {
            $this->y = $top - 40;

            return;
            /** The rest of the styling is ignored */
        }

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, $top - 25);
        $page->drawRectangle(275, $top, 570, $top - 25);

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress(
            $this->addressRenderer->formatQuoteAddress($quote->getBillingAddress(), 'pdf')
        );

        /* Payment */
        if ($quote->getPayment()->getMethod()) {
            $paymentBlock = $this->_paymentData->getInfoBlock($quote->getPayment());
            $paymentBlock->addChild('payment_instructions',
                'Cart2Quote\Quotation\Block\Adminhtml\Payment\Info\Instructions', $paymentBlock->getData());
            $paymentInfo = $paymentBlock->setIsSecureMode(true)->toPdf();
            $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
            $payment = explode('{{pdf_row_separator}}', $paymentInfo);
            foreach ($payment as $key => $value) {
                if (strip_tags(trim($value)) == '') {
                    unset($payment[$key]);
                }
            }
            reset($payment);
        } else {
            $payment = [];
        }


        /* Shipping Address and Method */
        if (!$quote->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress(
                $this->addressRenderer->formatQuoteAddress($quote->getShippingAddress(), 'pdf')
            );
            $shippingMethod = $quote->getShippingDescription();
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(__('Bill to:'), 35, $top - 15, 'UTF-8');

        if (!$quote->getIsVirtual()) {
            $page->drawText(__('Ship to:'), 285, $top - 15, 'UTF-8');
        } else {
           //$page->drawText(__('Payment Method:'), 285, $top - 15, 'UTF-8');
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }

        $addressesEndY = $this->y;

        if (!$quote->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value) {
                if ($value !== '') {
                    $text = [];
                    foreach ($this->string->split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            //$page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            //$page->setLineWidth(0.5);
            //$page->drawRectangle(25, $this->y, 275, $this->y - 25);
            //$page->drawRectangle(275, $this->y, 570, $this->y - 25);

            //$this->y -= 15;
            //$this->_setFontBold($page, 12);
            //$page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            //$page->drawText(__('Payment Method'), 35, $this->y, 'UTF-8');
            //$page->drawText(__('Shipping Method:'), 285, $this->y, 'UTF-8');

            //$this->y -= 10;
            //$page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

            //$this->_setFontRegular($page, 10);
            //$page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 0;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }

        /*foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }*/

        if ($quote->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            //$page->drawLine(25, $top - 25, 25, $yPayments);
            //$page->drawLine(570, $top - 25, 570, $yPayments);
            //$page->drawLine(25, $yPayments, 570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin = 0;
            $methodStartY = $this->y;
            $this->y -= 0;

            /*foreach ($this->string->split($quote->getShippingAddress()->getShippingDescription(), 45, true,
                true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
                $this->y -= 15;
            }*/

            $yShipments = $this->y;
            $totalShippingChargesText = "(";
            $totalShippingChargesText .= __('Total Shipping Charges');
            $totalShippingChargesText .= " ";
            $totalShippingChargesText .= $quote->formatPriceTxt($quote->getShippingAddress()->getShippingAmount());
            $totalShippingChargesText .= ")";

           // $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
            $yShipments -= $topMargin + 0;
           
            $tracks = [];
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            } 
            /*if (count($tracks)) {
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    $page->drawText($truncatedTitle, 292, $yShipments, 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments, 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }*/

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            //$page->drawLine(25, $methodStartY, 25, $currentY);
            //left
            //$page->drawLine(25, $currentY, 570, $currentY);
            //bottom
            //$page->drawLine(570, $currentY, 570, $methodStartY);
            //right

            $this->y = $currentY;
            $this->y -= 0;
        }
    }

    /**
     * Insert totals to pdf page
     *
     * @param \Zend_Pdf_Page $page
     * @param \Magento\Sales\Model\AbstractModel $quote
     * @return \Zend_Pdf_Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function insertTotals($page, $quote)
    {
        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 15];
        $quote->collectTotals();
        foreach ($totals as $total) {
            $total->setQuote($quote)->setSource($quote);
            $candisplay = $total->canDisplay();
            if ($candisplay) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = [
                        [
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                            'addToTop' => 2
                        ],
                        [
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                            'addToTop' => 2
                        ],
                    ];
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, [$lineBlock]);

        return $page;
    }

    /**
     * Draw lines
     *
     * Draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param  \Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Zend_Pdf_Page
     */
    public function drawLineBlocks(\Zend_Pdf_Page $page, array $draw, array $pageSettings = [])
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We don\'t recognize the draw line data. Please define the "lines" array.')
                );
            }
            $lines = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = [$column['text']];
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }
            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    if (isset($column['imageUrl'])) {
                        continue;
                    }
                    $fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = \Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = [$column['text']];
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    if (is_array($column['text'])) {
                        $lineSpacing = 10;
                    }
                    $top = 0;
                    if (isset($column['isProductLine'])) {
                        $top += 10;
                    }

                    if (isset($column['addToTop'])) {
                        $top += $column['addToTop'];
                    }
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < 15) {
                            $page = $this->newPage($pageSettings);
                        }
                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                } else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                            default:
                                break;
                        }
                        $page->drawText($part, $feed, $this->y - $top, 'UTF-8');
                        $top += $lineSpacing;
                    }
                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }

    /**
     * Before getPdf processing
     *
     * @return void
     */
    protected function _beforeGetPdf()
    {
        if ($this->inlineTranslation != null) {
            $this->inlineTranslation->suspend();
        }
    }

    /**
     * After getPdf processing
     *
     * @return void
     */
    protected function _afterGetPdf()
    {
        if ($this->inlineTranslation != null) {
            $this->inlineTranslation->resume();
        }
    }

    /**
     * Draw Quote Item process
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return \Zend_Pdf_Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _drawQuoteItem(
        \Magento\Framework\DataObject $item,
        \Zend_Pdf_Page $page,
        \Cart2Quote\Quotation\Model\Quote $quote
    ) {
        $type = $item->getProductType();
        $renderer = $this->getRenderer('quoteItem');
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setQuote($quote);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);
        $renderer->draw();

        return $renderer->getPage();
    }

    /**
     * Add name to the top.
     *
     * @param $page
     * @param $quote
     * @param $top
     * @return int
     */
    private function addName(&$page, $quote, $top)
    {
        $page->drawText(
            __('Name: ') . $quote->getCustomerName(),
            35,
            $top -= 15,
            'UTF-8'
        );

        return $top;
    }
}
