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

namespace Extait\ZendeskChat\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Extait\ZendeskChat\Helper\Data as ZendeskChatHelper;

/** @api */
class ZendeskChat extends Template
{
    /**
     * @var \Extait\ZendeskChat\Helper\Data
     */
    protected $zendeskChatHelper;

    /**
     * ZendeskChat constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Extait\ZendeskChat\Helper\Data $zendeskChatHelper
     * @param array $data
     */
    public function __construct(Context $context, ZendeskChatHelper $zendeskChatHelper, array $data = [])
    {
        parent::__construct($context, $data);

        $this->zendeskChatHelper = $zendeskChatHelper;
    }

    /**
     * Get the Zendesk Chat script.
     *
     * @return string
     */
    public function getZendeskChatScript()
    {
        return $this->zendeskChatHelper->getZendeskChatScript();
    }
}
