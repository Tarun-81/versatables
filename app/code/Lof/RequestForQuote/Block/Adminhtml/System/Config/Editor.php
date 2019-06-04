<?php
/**
 * landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    landofcoder
 * @package     Lof_RequestForQuote
 * @copyright   Copyright (c) 2018 landofcoder (https://landofcoder.com/)
 * @license     https://landofcoder.com/LICENSE.txt
 */
namespace Lof\RequestForQuote\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;

class Editor extends Field
{
    /**
     * @var WysiwygConfig
     */
    private $wysiwygConfig;

    /**
     * @param Context $context
     * @param WysiwygConfig $wysiwygConfig
     * @param array $data
     */
    public function __construct(Context $context, WysiwygConfig $wysiwygConfig, array $data = [])
    {
        parent::__construct($context, $data);
        $this->wysiwygConfig = $wysiwygConfig;
    }

    /**
     * Enable wysiwyg editor for the element.
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->addData([
            'wysiwyg' => true,
            'config'  => $this->wysiwygConfig->getConfig($element)
        ]);
        return parent::_getElementHtml($element);
    }
}
