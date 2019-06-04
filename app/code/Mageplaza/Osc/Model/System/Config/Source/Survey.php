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

namespace Mageplaza\Osc\Model\System\Config\Source;
class Survey extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray{
	/**
	 * @var \Magento\Framework\Data\Form\Element\Factory
	 */
	protected $_elementFactory;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Data\Form\Element\Factory $elementFactory,
		array $data = []
	) {

		$this->_elementFactory  = $elementFactory;
		parent::__construct($context, $data);
	}
	protected function _construct()
	{
		$this->addColumn('value', ['label' => __('Options')]);
		$this->_addAfter = false;
		$this->_addButtonLabel = __('Add');
		parent::_construct();
	}
}