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
 * @copyright   Copyright (c) 2016 Mageplaza (http://mageplaza.com/)
 * @license     http://mageplaza.com/license-agreement.html
 */
namespace Mageplaza\Osc\Model\System\Config\Source;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ComponentPosition
 * @package Mageplaza\Osc\Model\System\Config\Source
 */
class ComponentPosition extends AbstractModel
{
	const NOT_SHOW = 0;
	const SHOW_IN_PAYMENT = 1;
	const SHOW_IN_REVIEW = 2;

	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		return [
			self::NOT_SHOW        => __('No'),
			self::SHOW_IN_PAYMENT => __('In Payment Area'),
			self::SHOW_IN_REVIEW  => __('In Review Area')
		];
	}
}