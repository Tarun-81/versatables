<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuotePdf\Helper;

use Dompdf\Dompdf as LOFPDF;

class Pdf extends \Magento\Framework\App\Helper\AbstractHelper
{
	public $pdf;

	public $output;

	public $_html = '';

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Lof\RequestForQuote\Helper\Data $rfqData,
        LOFPDF $domPdf
        ) {
        parent::__construct($context);
        $this->_rfqData        = $rfqData;
        $this->pdf = $domPdf;
    }

    /**
     * Load html
     *
     * @param $html
     */
    public function setData($html)
    {
        $this->_html = $html;
        if($this->pdf) {
            $this->pdf->loadHtml($html);
        }
        return $this;
    }
    /**
     * Render LOFPDF output
     *
     * @return string
     */
    public function renderOutput()
    {
        if($this->output) {
            return $this->output;
        }
        if(!$this->pdf || $this->pdf === null) {
            $this->pdf = new LOFPDF();
            $this->pdf->loadHtml($this->_html, 'UTF-8');
        }
        $this->pdf->render();
        $this->output = $this->pdf->output();
        return $this->output;
    }
    
    
}