<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_ZendeskChat
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\ZendeskChat\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Config XML paths.
     */
    const CONFIG_XML_PATH_ENABLE = 'extait_zendesk_chat/general/enable';
    const CONFIG_XML_PATH_SCRIPT = 'extait_zendesk_chat/general/script';

    /**
     * Check whether the module is enabled.
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::CONFIG_XML_PATH_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get the Zendesk Chat script.
     *
     * @return string
     */
    public function getZendeskChatScript()
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_XML_PATH_SCRIPT, ScopeInterface::SCOPE_STORE);
    }
}
