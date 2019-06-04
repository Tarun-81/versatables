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
namespace Mageplaza\Osc\Controller\Survey;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use Mageplaza\Osc\Helper\Config as OscConfig;

class Save extends Action
{

	/**
	 * @var \Magento\Framework\Json\Helper\Data
	 */
	protected $jsonHelper;

	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $_checkoutSession;

	/**
	 * @var \Magento\Sales\Model\Order
	 */
	protected $_order;

	/**
	 * @var \Mageplaza\Osc\Helper\Config
	 */
	protected $oscConfig;

	/**
	 * Save constructor.
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Sales\Model\Order $order
	 * @param \Mageplaza\Osc\Helper\Config $oscConfig
	 */
	public function __construct(
		Context $context,
		Data $jsonHelper,
		Session $checkoutSession,
		Order $order,
		OscConfig $oscConfig
	) {

		$this->jsonHelper = $jsonHelper;
		$this->_checkoutSession = $checkoutSession;
		$this->_order			= $order;
		$this->oscConfig		= $oscConfig;
		parent::__construct($context);
	}

	/**
	 * @return mixed
	 */
	public function execute()
	{
		$response= array();
		if($this->getRequest()->getParam('answerChecked') && isset( $this->_checkoutSession->getOscData()['survey'])){
			try{
				$order= $this->_order->load($this->_checkoutSession->getOscData()['survey']['orderId']);
				$answers = '';
				foreach ($this->getRequest()->getParam('answerChecked') as $item){
					$answers.= $item .' - ';
				}
				$order->setData('osc_survey_question', $this->oscConfig->getSurveyQuestion());
				$order->setData('osc_survey_answers', substr($answers, 0, -2));
				$order->save();

				$response['status'] = 'success';
				$response['message']='Thank you for completing our survey!';
				$this->_checkoutSession->unsOscData();
			}catch (\Exception $e){
				$response['status'] = 'error';
				$response['message'] = "Can't save survey answer. Please try again! ";
			}
		return $this->getResponse()->representJson($this->jsonHelper->jsonEncode($response));
		}
	}

}
