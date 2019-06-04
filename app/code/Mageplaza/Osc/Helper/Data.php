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
namespace Mageplaza\Osc\Helper;

use Magento\Checkout\Helper\Data as HelperData;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData as AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Locale\Resolver;
use Magento\Directory\Model\Region;

/**
 * Class Data
 * @package Mageplaza\Osc\Helper
 */
class Data extends AbstractHelper
{
	/**
	 * @type \Magento\Checkout\Helper\Data
	 */
	protected $_helperData;

	/**
	 * @type \Mageplaza\Osc\Helper\Config
	 */
	protected $_helperConfig;

	/**
	 * @type \Magento\Framework\Pricing\PriceCurrencyInterface
	 */
	protected $_priceCurrency;

	/**
	 * @type \Magento\Framework\App\Filesystem\DirectoryList
	 */
	protected $_directoryList;

	/**
	 * @type \Magento\Framework\Locale\Resolver
	 */
	protected $_resolver;

	/**
	 * @type \Magento\Directory\Model\Region
	 */
	protected $_region;

	/**
	 * @param Context $context
	 * @param HelperData $helperData
	 * @param StoreManagerInterface $storeManager
	 * @param Config $helperConfig
	 * @param ObjectManagerInterface $objectManager
	 * @param PriceCurrencyInterface $priceCurrency
	 * @param DirectoryList $directoryList
	 * @param Resolver $resolver
	 * @param Region $region
	 */
	public function __construct(
		Context $context,
		HelperData $helperData,
		StoreManagerInterface $storeManager,
		Config $helperConfig,
		ObjectManagerInterface $objectManager,
		PriceCurrencyInterface $priceCurrency,
		DirectoryList $directoryList,
		Resolver $resolver,
		Region $region
	)
	{
		$this->_helperData   = $helperData;
		$this->_helperConfig = $helperConfig;
		$this->_priceCurrency   = $priceCurrency;
		$this->_directoryList = $directoryList;
		$this->_resolver      = $resolver;
		$this->_region        = $region;


		parent::__construct($context, $objectManager, $storeManager);
	}

	/**
	 * @return \Mageplaza\Osc\Helper\Config
	 */
	public function getConfig()
	{
		return $this->_helperConfig;
	}

	/**
	 * @param null $store
	 * @return bool
	 */
	public function isEnabled($store = null)
	{
		return $this->getConfig()->isEnabled($store);
	}

	/**
	 * @param $amount
	 * @param null $store
	 * @return float
	 */
	public function convertPrice($amount, $store = null)
	{
		return $this->_priceCurrency->convert($amount, $store);
	}

	/**
	 * @param $quote
	 * @return float|int
	 */
	public function calculateGiftWrapAmount($quote)
	{
		$baseOscGiftWrapAmount = $this->getConfig()->getOrderGiftwrapAmount();
		if ($baseOscGiftWrapAmount < 0.0001) {
			return 0;
		}

		$giftWrapType = $this->getConfig()->getGiftWrapType();
		if ($giftWrapType == \Mageplaza\Osc\Model\System\Config\Source\Giftwrap::PER_ITEM) {
			$giftWrapBaseAmount    = $baseOscGiftWrapAmount;
			$baseOscGiftWrapAmount = 0;
			foreach ($quote->getAllVisibleItems() as $item) {
				if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
					continue;
				}
				$baseOscGiftWrapAmount += $giftWrapBaseAmount * $item->getQty();
			}
		}

		return $this->convertPrice($baseOscGiftWrapAmount, $quote->getStore());
	}

	/**
	 * Check has library at path var/Mageplaza/Osc/GeoIp/
	 * @return bool|string
	 */
	public function checkHasLibrary()
	{
		$path = $this->_directoryList->getPath('var') . '/Mageplaza/Osc/GeoIp';
		if (!file_exists($path)) return false;
		$folder   = scandir($path, true);
		$pathFile = $path . '/' . $folder[0] . '/GeoLite2-City.mmdb';
		if (!file_exists($pathFile)) return false;

		return $pathFile;
	}

	/**
	 * @param $data
	 * @return mixed
	 */
	public function getGeoIpData($data)
	{
		$geoIpData['city']       = $this->filterData($data, 'city', 'names');
		$geoIpData['country_id'] = $this->filterData($data, 'country', 'iso_code', false);
		if (!empty($this->getRegionId($data, $geoIpData['country_id']))) {
			$geoIpData['region_id'] = $this->getRegionId($data, $geoIpData['country_id']);
		} else {
			$geoIpData['region'] = $this->filterData($data, 'subdivisions', 'names');
		}
		if (isset($data['postal'])) {
			$geoIpData['postcode'] = $this->filterData($data, 'postal', 'code', false);
		}

		return $geoIpData;
	}

	/**
	 * Filter GeoIP data
	 * @param $data
	 * @param $field
	 * @param $child
	 * @param bool|true $convert
	 * @return string
	 */
	public function filterData($data, $field, $child, $convert = true)
	{
		$output = '';
		if (isset($data[$field]) && is_array($data[$field])) {
			if ($field == 'subdivisions') {
				foreach ($data[$field][0] as $key => $value) {
					$data[$field][$key] = $value;
				}
			}
			if (isset($data[$field][$child])) {
				if ($convert) {
					if (is_array($data[$field][$child])) {
						$locale   = $this->_resolver->getLocale();
						$language = substr($locale, 0, 2) ? substr($locale, 0, 2) : 'en';
						$output   = isset($data[$field][$child][$language]) ? $data[$field][$child][$language] : '';
					}
				} else {
					$output = $data[$field][$child];
				}
			}
		}

		return $output;
	}

	/**
	 * Find region id by Country
	 * @param $data
	 * @param $country
	 * @return mixed
	 */
	public function getRegionId($data, $country)
	{
		$regionId = $this->_region->loadByCode($this->filterData($data, 'subdivisions', 'iso_code', false), $country)->getId();

		return $regionId;
	}
}
