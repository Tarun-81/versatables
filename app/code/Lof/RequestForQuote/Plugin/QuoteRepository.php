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
namespace Lof\RequestForQuote\Plugin;

use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\QuoteRepository\SaveHandler;

class QuoteRepository
{
    public function aroundSave(\Magento\Quote\Model\QuoteRepository $subject, $proceed, \Magento\Quote\Api\Data\CartInterface $quote)
    {
        if ($quote->getId()) {
            $currentQuote = $quote;
            foreach ($currentQuote->getData() as $key => $value) {
                if (!$quote->hasData($key)) {
                    $quote->setData($key, $value);
                }
            }
        }
        $saveHandler = ObjectManager::getInstance()->get(SaveHandler::class);
        $saveHandler->save($quote);
    }
}