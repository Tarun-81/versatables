<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza
 * @copyright   Copyright (c) 2016 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Osc\Block\Order\View;

/**
 * Class Survey
 * @package Mageplaza\Osc\Block\Order\View
 */
class Survey extends \Magento\Framework\View\Element\Template
{
    /**
     * @type \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
    
        $this->_coreRegistry = $registry;

        parent::__construct($context, $data);
    }

	/**
	 * @return string
	 */
    public function getSurveyQuestion()
    {
        if ($order = $this->getOrder()) {
            return $order->getOscSurveyQuestion();
        }

        return '';
    }

	/**
	 * @return string
	 */
	public function getSurveyAnswers()
	{
		if ($order = $this->getOrder()) {
			return $order->getOscSurveyAnswers();
		}

		return '';
	}

    /**
     * Get current order
     *
     * @return mixed
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
}
