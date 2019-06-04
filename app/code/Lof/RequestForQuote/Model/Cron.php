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
 * @copyright  Copyright (c) 2018 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuote\Model;

use Magento\Cron\Model\Schedule;
use Lof\RequestForQuote\Model\Quote;
class Cron
{
    protected $quote;
    /**
     * @var \Lof\RequestForQuote\Model\Quote
     */
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
     /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;
     /**
     * @var Validator
     */
    protected $validator;
    /**
     * @var \Lof\RequestForQuote\Helper\Data
     */
    protected $helper;

    protected $rfqMail;

    protected $quoteRepository;
    protected $_moduleManager;

    public function __construct(
        \Lof\RequestForQuote\Model\Quote $quote,
        \Lof\RequestForQuote\Helper\Mail $rfqMail,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Lof\RequestForQuote\Helper\Data $helper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->quote            = $quote;
        $this->logger           = $logger;
        $this->rfqMail          = $rfqMail;
        $this->dateTime         = $dateTime;
        $this->helper           = $helper;
        $this->quoteRepository = $quoteRepository;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * Send notification when quote expiry (cron process)
     *
     * @return void
     */
    public function scheduledSendExpiry()
    {
      $now  = date(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT, $this->dateTime->timestamp());
      $collection = $this->quote->getCollection();
      $collection->addExpiryDateForFilter($now);
      $collection->addFieldToFilter("send_expiry_email", 1);
      if($collection->getSize()){
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $pdfModel = $_objectManager->create('\Lof\RequestForQuotePdf\Model\Quote\Pdf\Quote');
        foreach($collection as $quote){
            $mageQuote = $this->quoteRepository->get($quote->getQuoteId());
            $file = [];
            if ($this->_moduleManager->isEnabled('Lof_RequestForQuotePdf')) {
                $pdfModel = $_objectManager->create('\Lof\RequestForQuotePdf\Model\Quote\Pdf\Quote');
                $file = $pdfModel->generatePdf($quote, $mageQuote);
            }
            $this->rfqMail->sendNotificationQuoteExpiredEmail($mageQuote, $quote, $file);
        }
      }
    }
    /**
     * Send remind notification (cron process)
     *
     * @return void
     */
    public function scheduledSendRemind()
    {
      $now  = date(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT, $this->dateTime->timestamp());
      $collection = $this->quote->getCollection();
      $collection->addRemindDateForFilter($now);
      $collection->addFieldToFilter("send_remind_email", 1);
      $collection->addFieldToFilter("status", ['neq' => \Lof\RequestForQuote\Model\Quote::STATE_EXPIRED]);
      if($collection->getSize()){
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $pdfModel = $_objectManager->create('\Lof\RequestForQuotePdf\Model\Quote\Pdf\Quote');
        foreach($collection as $quote){
            $mageQuote = $this->quoteRepository->get($quote->getQuoteId());
            $file = [];
            if ($this->_moduleManager->isEnabled('Lof_RequestForQuotePdf')) {
                $pdfModel = $_objectManager->create('\Lof\RequestForQuotePdf\Model\Quote\Pdf\Quote');
                $file = $pdfModel->generatePdf($quote, $mageQuote);
            }
            $this->rfqMail->sendNotificationRemindQuoteEmail($mageQuote, $quote, $file);
        }
      }
    }
    /**
     * Send remind notification (cron process)
     *
     * @return void
     */
    public function scheduledUpdateExpiredStatus()
    {
      $now  = date(\Magento\Framework\Stdlib\DateTime::DATE_PHP_FORMAT, $this->dateTime->timestamp());
      $collection = $this->quote->getCollection();
      $collection->addFieldToFilter("status", ['neq' => \Lof\RequestForQuote\Model\Quote::STATE_EXPIRED]);
      $collection->addFieldToFilter("expiry", ['lteq' => $now]);
      if($collection->getSize()){
        foreach($collection as $quote){
            $quote_model = $this->quote->load($quote->getId());
            $quote_model->setData("status", Quote::STATE_EXPIRED);
            $quote_model->setData("send_expiry_email", 0);
            $quote_model->setData("send_remind_email", 0);
            $quote_model->save();
        }
      }
    }
}
