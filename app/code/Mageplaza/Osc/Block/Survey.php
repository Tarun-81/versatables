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
use Magento\Checkout\Model\Session;

/**
 * Class Survey
 * @package Mageplaza\Osc\Block\Survey
 */
class Survey extends Template
{
	/**
	 * @var \Mageplaza\Osc\Helper\Data
	 */
	protected $_helperData;

	/**
	 * @var $_helperConfig
	 */
	protected $_helperConfig;

	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $_checkoutSession;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Mageplaza\Osc\Helper\Data $helperData
	 * @param array $data
	 */
	public function __construct(
		Template\Context $context,
		HelperData $helperData,
		Session $checkoutSession,
		array $data = []
	) {

		$this->_helperData 		= $helperData;
		$this->_checkoutSession = $checkoutSession;

		parent::__construct($context, $data);
		$this->getLastOrderId();
	}

	/**
	 * @return bool
	 */
	public function isEnableSurvey()
	{
		return $this->_helperData->getConfig()->isEnabled() && !$this->_helperData->getConfig()->isDisableSurvey();
	}

	public function getLastOrderId(){
		$orderId = $this->_checkoutSession->getLastRealOrder()->getEntityId();
		$this->_checkoutSession->setOscData(array('survey'=>array('orderId' => $orderId )));
	}

	/**
	 * @return mixed
	 */
	public function getSurveyQuestion(){
		return $this->_helperData->getConfig()->getSurveyQuestion();
	}

	/**
	 * @return array
	 */
	public function getAllSurveyAnswer(){
		$answers=[];
		foreach ($this->_helperData->getConfig()->getSurveyAnswers() as $key=>$item){
			$answers[]=['id'=>$key,'value'=>$item['value']];
		}
		return $answers;
	}

	/**
	 * @return mixed
	 */
	public function isAllowCustomerAddOtherOption(){
		return $this->_helperData->getConfig()->isAllowCustomerAddOtherOption();
	}
}
