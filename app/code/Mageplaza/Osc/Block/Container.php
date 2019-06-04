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
 * @category   Mageplaza
 * @package    Mageplaza_Osc
 * @version    3.0.0
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Osc\Block;

use Magento\Framework\View\Element\Template;
use Mageplaza\Osc\Helper\Data as HelperData;

/**
 * Class Container
 * @package Mageplaza\Osc\Block
 */
class Container extends Template
{
    protected $_helperData;
    protected $_helperConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageplaza\Osc\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HelperData $helperData,
        array $data = []
    ) {
    
        $this->_helperData = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCheckoutDescription()
    {
        return $this->_helperData->getConfig()->getGeneralConfig('description');
    }
}
