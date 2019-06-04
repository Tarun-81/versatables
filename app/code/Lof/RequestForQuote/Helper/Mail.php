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

namespace Lof\RequestForQuote\Helper;

class Mail extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_currency;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    protected $logger;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Url $urlBuilder,
        \Lof\RequestForQuote\Model\Mail\UploadTransportBuilder $transportBuilder,
        \Lof\RequestForQuote\Helper\Data $rfqHelper
        ) {
        parent::__construct($context);
        $this->context           = $context;
        $this->filterManager     = $filterManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->dateTime          = $dateTime;
        $this->messageManager    = $messageManager;
        $this->transportBuilder  = $transportBuilder;
        $this->_storeManager     = $storeManager;
        $this->timezone          = $timezone;
        $this->_layout           = $layout;
        $this->_urlBuilder       = $urlBuilder;
        $this->rfqHelper         = $rfqHelper;
        $this->logger            = $context->getLogger();
    }

    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'requestforquote/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function send( $templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId, $file = [], $filetype="PDF")
    {
        $this->inlineTranslation->suspend();
        try {
            $attach_type = "";
            if($filetype == "PDF") {
                $attach_type = 'application/pdf';  
            }
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $this->transportBuilder
            ->setTemplateIdentifier($templateName)
            ->setTemplateOptions([
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId,
                ])
            ->setTemplateVars($variables)
            ->setFrom([
                'name'  => $senderName,
                'email' => $senderEmail
                ])
            ->addTo($recipientEmail, $recipientName)
            ->setReplyTo($senderEmail);

            if($file && $attach_type) {
                $file_content = isset($file['output'])?$file['output']:'';
                $file_name = isset($file['filename'])?$file['filename']:'';
                $this->transportBuilder->addAttachment($file_content, $file_name, $attach_type);
            }
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t send the email quote right now.'));
            $this->logger->critical($e);
        }

        $this->inlineTranslation->resume();
        return true;
    }

    /**
     * Get formatted order created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormatted($time, $store, $format)
    {
        return $this->timezone->formatDateTime(
            new \DateTime($time),
            $format,
            $format,
            null,
            $this->timezone->getConfigTimezone('store', $store)
            );
    }

    public function initDataQuoteOnEmail($lofquote) {
        $email_admin_note     = $this->getConfig('quote_admin_setting/email_admin_note');
        $email_terms     = $this->getConfig('quote_admin_setting/email_terms');
        $email_wtexpect     = $this->getConfig('quote_admin_setting/email_wtexpect');
        $email_break_line     = $this->getConfig('quote_admin_setting/email_break_line');
        if(!$email_admin_note){
            $lofquote->setData('admin_note', '');
        }
        if(!$email_terms){
            $lofquote->setData('terms', '');
        }
        if(!$email_wtexpect){
            $lofquote->setData('wtexpect', '');
        }
        if(!$email_break_line){
            $lofquote->setData('break_line', '');
        }
        return $lofquote;
    }

    public function sendNotificationNewQuoteEmail($mageQuote, $quote, $file = [], $file_type="PDF")
    {
        $block = $this->_layout->createBlock('Lof\RequestForQuote\Block\Quote\Items');
        $templateName = $this->getConfig('email_templates/new_quote');
        $storeScope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $senderId     = $this->getConfig('general/sender_email_identity');

        if ($senderId) {
            $quote = $this->initDataQuoteOnEmail($quote);
            $sender_email   = $this->scopeConfig->getValue('trans_email/ident_' . $senderId . '/name', $storeScope);
            $recipientEmail = $mageQuote->getCustomerEmail();
            $recipientName  = '';
            $variables      = [
                'increment_id' => $quote->getIncrementId(),
                'created_at'   => $this->getCreatedAtFormatted($mageQuote->getCreatedAt(), $mageQuote->getstore(), \IntlDateFormatter::MEDIUM),
                'quote'        => $mageQuote,
                'lofquote'     => $quote
            ];

            $storeId     = $this->_storeManager->getStore()->getId();
            $senderName  = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/name");
            $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/email");

            $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId, $file, $file_type);

            $bcc = $this->getConfig('general/bcc');
            if ($bcc) {
                $bcc = explode(",", $bcc);
                foreach ($bcc as $email) {
                    $email = trim($email);
                    $this->send($templateName, $senderName, $senderEmail, $email, $recipientName, $variables, $storeId);
                }
            }
            return true;
        }
        return false;
    }

    public function sendNotificationRemindQuoteEmail($mageQuote, $quote, $file = [], $file_type="PDF")
    {
        $block = $this->_layout->createBlock('Lof\RequestForQuote\Block\Quote\Items');
        $templateName = $this->getConfig('email_templates/remind_quote');
        $storeScope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $senderId     = $this->getConfig('general/sender_email_identity');

        if ($senderId) {
            $quote = $this->initDataQuoteOnEmail($quote);
            $sender_email   = $this->scopeConfig->getValue('trans_email/ident_' . $senderId . '/name', $storeScope);
            $recipientEmail = $mageQuote->getCustomerEmail();
            $recipientName  = '';
            $variables      = [
                'increment_id' => $quote->getIncrementId(),
                'created_at'   => $this->getCreatedAtFormatted($mageQuote->getCreatedAt(), $mageQuote->getstore(), \IntlDateFormatter::MEDIUM),
                'quote'        => $mageQuote,
                'lofquote'     => $quote
            ];

            $storeId     = $this->_storeManager->getStore()->getId();
            $senderName  = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/name");
            $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/email");

            $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId, $file, $file_type);

            $bcc = $this->getConfig('general/bcc');
            if ($bcc) {
                $bcc = explode(",", $bcc);
                foreach ($bcc as $email) {
                    $email = trim($email);
                    $this->send($templateName, $senderName, $senderEmail, $email, $recipientName, $variables, $storeId, $file, $file_type);
                }
            }
            return true;
        }
        return false;
    }

    public function sendNotificationAcceptQuoteEmail($mageQuote, $quote, $file = [], $file_type="PDF")
    {
        $block          = $this->_layout->createBlock('Lof\RequestForQuote\Block\Quote\Items');
        $templateName   = $this->getConfig('email_templates/accept_quote');
        $storeScope     = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $senderId       = $this->getConfig('general/sender_email_identity');
        $sender_email   = $this->scopeConfig->getValue('trans_email/ident_' . $senderId . '/name', $storeScope);
        $recipientEmail = $mageQuote->getCustomerEmail();
        $recipientName  = '';

        if ($senderId) {
            $quote = $this->initDataQuoteOnEmail($quote);
            $url = $this->_storeManager->getStore()->getUrl();
            $variables      = [
                'increment_id'  => $quote->getIncrementId(),
                'created_at'    => $this->getCreatedAtFormatted($mageQuote->getCreatedAt(), $mageQuote->getstore(), \IntlDateFormatter::MEDIUM),
                'quote'         => $mageQuote,
                'lofquote'      => $quote,
                'purchase_link' => $url,
                'expired_at'    => $this->rfqHelper->formatDate($quote->getExpiry(), \IntlDateFormatter::SHORT)
            ];

            $storeId        = $this->_storeManager->getStore()->getId();
            $senderName     = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/name");
            $senderEmail    = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/email");

            $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);

            $sendQuoteRequestNotificationTo = $this->getConfig('general/send_quote_request_notification_to');
            if ($sendQuoteRequestNotificationTo) {
                $sendQuoteRequestNotificationTo = explode(",", $sendQuoteRequestNotificationTo);
                foreach ($sendQuoteRequestNotificationTo as $email) {
                    $this->send($templateName, $senderName, $senderEmail, $email, $recipientName, $variables, $storeId, $file, $file_type);
                }
            }
            return true;
        }
        return false;
    }

    public function sendNotificationQuoteCancelledEmail($mageQuote, $quote, $file = [], $file_type="PDF")
    {
        $block          = $this->_layout->createBlock('Lof\RequestForQuote\Block\Quote\Items');
        $templateName   = $this->getConfig('email_templates/quote_cancelled');
        $storeScope     = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $senderId       = $this->getConfig('general/sender_email_identity');
        $sender_email   = $this->scopeConfig->getValue('trans_email/ident_' . $senderId . '/name', $storeScope);
        $recipientEmail = $mageQuote->getCustomerEmail();
        $recipientName  = '';

        if ($senderId) {
            $quote = $this->initDataQuoteOnEmail($quote);
            $url = $this->_storeManager->getStore()->getUrl();
            $variables      = [
                'increment_id'  => $quote->getIncrementId(),
                'created_at'    => $this->getCreatedAtFormatted($mageQuote->getCreatedAt(), $mageQuote->getstore(), \IntlDateFormatter::MEDIUM),
                'quote'         => $mageQuote,
                'lofquote'      => $quote,
                'purchase_link' => $url,
                'expired_at'    => $this->rfqHelper->formatDate($quote->getExpiry(), \IntlDateFormatter::SHORT)
            ];

            $storeId     = $this->_storeManager->getStore()->getId();
            $senderName  = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/name");
            $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/email");

            $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);

            $sendQuoteRequestNotificationTo = $this->getConfig('general/send_quote_request_notification_to');
            if ($sendQuoteRequestNotificationTo) {
                $sendQuoteRequestNotificationTo = explode(",", $sendQuoteRequestNotificationTo);
                foreach ($sendQuoteRequestNotificationTo as $email) {
                    $this->send($templateName, $senderName, $senderEmail, $email, $recipientName, $variables, $storeId, $file, $file_type);
                }
            }
            return true;
        }
        return false;
    }

    public function sendNotificationQuoteReviewedEmail($mageQuote, $quote, $file = [], $file_type="PDF")
    {
        $block          = $this->_layout->createBlock('Lof\RequestForQuote\Block\Quote\Items');
        $templateName   = $this->getConfig('email_templates/quote_reviewed');
        $storeScope     = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $senderId       = $this->getConfig('general/sender_email_identity');
        $sender_email   = $this->scopeConfig->getValue('trans_email/ident_' . $senderId . '/name', $storeScope);
        $recipientEmail = $mageQuote->getCustomerEmail();
        $recipientName  = '';

        if ($senderId) {
            $quote = $this->initDataQuoteOnEmail($quote);
            $url = $this->_storeManager->getStore()->getUrl();
            $variables = [
                'increment_id'  => $quote->getIncrementId(),
                'created_at'    => $this->getCreatedAtFormatted($mageQuote->getCreatedAt(), $mageQuote->getstore(), \IntlDateFormatter::MEDIUM),
                'quote'         => $mageQuote,
                'lofquote'     => $quote,
                'purchase_link' => $url,
                'expired_at'    => $this->rfqHelper->formatDate($quote->getExpiry(), \IntlDateFormatter::SHORT)
            ];

            $storeId     = $this->_storeManager->getStore()->getId();
            $senderName  = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/name");
            $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/email");

            $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);
            $sendQuoteRequestNotificationTo = $this->getConfig('general/send_quote_request_notification_to');
            if ($sendQuoteRequestNotificationTo) {
                $sendQuoteRequestNotificationTo = explode(",", $sendQuoteRequestNotificationTo);
                foreach ($sendQuoteRequestNotificationTo as $email) {
                    $this->send($templateName, $senderName, $senderEmail, $email, $recipientName, $variables, $storeId, $file, $file_type);
                }
            }
            return true;
        }
        return false;
    }

    public function sendNotificationQuoteExpiredEmail($mageQuote, $quote, $file = [], $file_type="PDF")
    {
        $block          = $this->_layout->createBlock('Lof\RequestForQuote\Block\Quote\Items');
        $templateName   = $this->getConfig('email_templates/quote_expired');
        $storeScope     = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $senderId       = $this->getConfig('general/sender_email_identity');
        $sender_email   = $this->scopeConfig->getValue('trans_email/ident_' . $senderId . '/name', $storeScope);
        $recipientEmail = $mageQuote->getCustomerEmail();
        $recipientName  = '';

        if ($senderId) {
            $quote = $this->initDataQuoteOnEmail($quote);
            $url = $this->_storeManager->getStore()->getUrl();
            $variables      = [
                'increment_id'  => $quote->getIncrementId(),
                'created_at'    => $this->getCreatedAtFormatted($mageQuote->getCreatedAt(), $mageQuote->getstore(), \IntlDateFormatter::MEDIUM),
                'quote'         => $mageQuote,
                'lofquote'      => $quote,
                'purchase_link' => $url,
                'expired_at'    => $this->rfqHelper->formatDate($quote->getExpiry(), \IntlDateFormatter::SHORT)
            ];

            $storeId     = $this->_storeManager->getStore()->getId();
            $senderName  = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/name");
            $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/email");
            $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);
            $sendQuoteRequestNotificationTo = $this->getConfig('general/send_quote_request_notification_to');
            if ($sendQuoteRequestNotificationTo) {
                $sendQuoteRequestNotificationTo = explode(",", $sendQuoteRequestNotificationTo);
                foreach ($sendQuoteRequestNotificationTo as $email) {
                    $this->send($templateName, $senderName, $senderEmail, $email, $recipientName, $variables, $storeId, $file, $file_type);
                }
            }
            return true;
        }
        return false;
    }

    public function sendNotificationChangeExpiredEmail($mageQuote, $quote, $oldDate, $newDate, $file = [], $file_type="PDF")
    {
        $block          = $this->_layout->createBlock('Lof\RequestForQuote\Block\Quote\Items');
        $templateName   = $this->getConfig('email_templates/quote_change_expired_date');
        $storeScope     = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $senderId       = $this->getConfig('general/sender_email_identity');
        $sender_email   = $this->scopeConfig->getValue('trans_email/ident_' . $senderId . '/name', $storeScope);
        $recipientEmail = $mageQuote->getCustomerEmail();
        $recipientName  = '';

        if ($senderId) {
            $quote = $this->initDataQuoteOnEmail($quote);
            $url = $this->_storeManager->getStore()->getUrl();
            $variables      = [
                'increment_id'  => $quote->getIncrementId(),
                'created_at'    => $this->getCreatedAtFormatted($mageQuote->getCreatedAt(), $mageQuote->getstore(), \IntlDateFormatter::MEDIUM),
                'quote'         => $mageQuote,
                'lofquote'      => $quote,
                'purchase_link' => $url,
                'old_date'      => $this->getCreatedAtFormatted($oldDate, $mageQuote->getstore(), \IntlDateFormatter::MEDIUM),
                'expired_at'    => $this->getCreatedAtFormatted($newDate, $mageQuote->getstore(), \IntlDateFormatter::MEDIUM)
            ];

            $storeId     = $this->_storeManager->getStore()->getId();
            $senderName  = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/name");
            $senderEmail = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/email");

            $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);
            $sendQuoteRequestNotificationTo = $this->getConfig('general/send_quote_request_notification_to');
            if ($sendQuoteRequestNotificationTo) {
                $sendQuoteRequestNotificationTo = explode(",", $sendQuoteRequestNotificationTo);
                foreach ($sendQuoteRequestNotificationTo as $email) {
                    $this->send($templateName, $senderName, $senderEmail, $email, $recipientName, $variables, $storeId, $file, $file_type);
                }
            }
            return true;
        }
        return false;
    }
}