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
namespace Mageplaza\Osc\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Mageplaza\Osc\Helper\Data as HelperData;


class Geoip extends Field
{
	/**
	 * @var string
	 */
	protected $_template = 'Mageplaza_Osc::system/config/geoip.phtml';

	/**
	 * @type \Mageplaza\Osc\Helper\Data
	 */
	protected $_helperData;

	/**
	 * @param Context $context
	 * @param array $data
	 */
	public function __construct(
		Context $context,
		HelperData $helperData,
		array $data = []
	) {
		$this->_helperData = $helperData;
		parent::__construct($context, $data);
	}

	/**
	 * Remove scope label
	 *
	 * @param  AbstractElement $element
	 * @return string
	 */
	public function render(AbstractElement $element)
	{
		$element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
		return parent::render($element);
	}

	/**
	 * Return element html
	 *
	 * @param  AbstractElement $element
	 * @return string
	 */
	protected function _getElementHtml(AbstractElement $element)
	{
		return $this->_toHtml();
	}

	/**
	 * Return ajax url for collect button
	 *
	 * @return string
	 */
	public function getAjaxUrl()
	{
		return $this->getUrl('onestepcheckout/system_config/geoip');
	}

	/**
	 * Generate collect button html
	 *
	 * @return string
	 */
	public function getButtonHtml()
	{
		$button = $this->getLayout()->createBlock(
			'Magento\Backend\Block\Widget\Button'
		)->setData(
			[
				'id' => 'geoip_button',
				'label' => __('Download Library'),
			]
		);

		return $button->toHtml();
	}

	/**
	 * @return string
	 */
	public function isDisplayIcon(){
		return $this->_helperData->checkHasLibrary() ? '' : 'hidden="hidden';
	}

}
?>