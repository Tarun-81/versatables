<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Block\Checkout;

use Magento\Framework\View\Element\Template;

class CompatibleConfig extends Template
{
    /**
     * @var string $_template
     */
    protected $_template = "onepage/compatible-config.phtml";

    /**
     * @var \Mageplaza\Osc\Helper\Config
     */
    protected $_oscConfig;

    /**
     * CompatibleConfig constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Mageplaza\Osc\Helper\Config $oscConfig,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_oscConfig = $oscConfig;
    }

    /**
     * @return bool
     */
    public function isEnableModulePostNL()
    {
        return $this->_oscConfig->isEnableModulePostNL();
    }
}