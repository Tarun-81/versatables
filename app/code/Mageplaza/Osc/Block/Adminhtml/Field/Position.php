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
namespace Mageplaza\Osc\Block\Adminhtml\Field;

/**
 * Class Position
 * @package Mageplaza\Osc\Block\Adminhtml\Field
 */
class Position extends \Magento\Backend\Block\Widget\Container
{
	/**
	 * @type \Mageplaza\Osc\Helper\Data
	 */
	protected $_helper;

	/**
	 * @type array
	 */
	protected $sortedFields = [];

	/**
	 * @type array
	 */
	protected $availableFields = [];

	/**
	 * @param \Magento\Backend\Block\Widget\Context $context
	 * @param \Mageplaza\Osc\Helper\Data $helperData
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Widget\Context $context,
		\Mageplaza\Osc\Helper\Data $helperData,
		array $data = []
	)
	{
		$this->_helper = $helperData;

		parent::__construct($context, $data);
	}

	/**
	 * @inheritdoc
	 */
	protected function _construct()
	{
		parent::_construct();

		$this->addButton(
			'save',
			[
				'label'   => __('Save Position'),
				'class'   => 'save primary',
				'onclick' => 'saveOscPosition()'
			],
			1
		);

		$this->prepareCollection();
	}

	/**
	 * @return array
	 */
	public function prepareCollection()
	{
		list($this->sortedFields, $this->availableFields) = $this->getHelperData()->getConfig()->getSortedField(false);
	}

	/**
	 * Retrieve the header text
	 *
	 * @return \Magento\Framework\Phrase|string
	 */
	public function getHeaderText()
	{
		return __('Field Management');
	}

	/**
	 * @return array
	 */
	public function getSortedFields()
	{
		return $this->sortedFields;
	}

	/**
	 * @return mixed
	 */
	public function getAvailableFields()
	{
		return $this->availableFields;
	}

	/**
	 * @return \Mageplaza\Osc\Helper\Data
	 */
	public function getHelperData()
	{
		return $this->_helper;
	}

	/**
	 * @return string
	 */
	public function getAjaxUrl()
	{
		return $this->getUrl('*/*/save');
	}
}
