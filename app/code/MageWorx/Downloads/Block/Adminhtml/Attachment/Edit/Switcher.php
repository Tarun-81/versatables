<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Attachment\Edit;

class Switcher extends \Magento\Backend\Block\Template
{
    /**
     *
     * Template file
     *
     * @var string
     */
    protected $_template = 'switcher/js.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }
}
