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

use Magento\Customer\Helper\Address;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Customer\Model\ResourceModel\Address\Attribute\CollectionFactory as AddressFactory;
use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory as CustomerFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\Osc\Model\System\Config\Source\ComponentPosition;

/**
 * Class Config
 * @package Mageplaza\Osc\Helper
 */
class Config extends AbstractData
{
	/** Is enable module path */
	const GENERAL_IS_ENABLED = 'osc/general/is_enabled';

	/** Field position */
	const SORTED_FIELD_POSITION = 'osc/field/position';

	/** General configuration path */
	const GENERAL_CONFIGUARATION = 'osc/general';

	/** Display configuration path */
	const DISPLAY_CONFIGUARATION = 'osc/display_configuration';

	/** Design configuration path */
	const DESIGN_CONFIGUARATION = 'osc/design_configuration';

	/** Geo configuration path */
	const GEO_IP_CONFIGUARATION = 'osc/geoip_configuration';

	/** Is enable Geo Ip path */
	const GEO_IP_IS_ENABLED    = 'osc/geoip_configuration/is_enable_geoip';

	/** @var \Magento\Customer\Model\ResourceModel\Address\Attribute\CollectionFactory */
	protected $_addressesFactory;

	/** @var \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory */
	protected $_customerFactory;

	/** @var \Magento\Customer\Helper\Address */
	protected $addressHelper;

	/** @var \Magento\Customer\Model\AttributeMetadataDataProvider */
	private $attributeMetadataDataProvider;

	/**
	 * Config constructor.
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Customer\Helper\Address $addressHelper
	 * @param \Magento\Customer\Model\ResourceModel\Address\Attribute\CollectionFactory $addressesFactory
	 * @param \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $customerFactory
	 * @param \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
	 */
	public function __construct(
		Context $context,
		ObjectManagerInterface $objectManager,
		StoreManagerInterface $storeManager,
		Address $addressHelper,
		AddressFactory $addressesFactory,
		CustomerFactory $customerFactory,
		AttributeMetadataDataProvider $attributeMetadataDataProvider
	)
	{
		parent::__construct($context, $objectManager, $storeManager);

		$this->addressHelper                 = $addressHelper;
		$this->_addressesFactory             = $addressesFactory;
		$this->_customerFactory              = $customerFactory;
		$this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
	}

	/**
	 * Check the current page is osc
	 *
	 * @param null $store
	 * @return bool
	 */
	public function isOscPage($store = null)
	{
		$moduleEnable = $this->isEnabled($store);
		$isOscModule  = ($this->_request->getRouteName() == 'onestepcheckout');

		return $moduleEnable && $isOscModule;
	}

	/**
	 * Is enable module on frontend
	 *
	 * @param null $store
	 * @return bool
	 */
	public function isEnabled($store = null)
	{
		$isModuleOutputEnabled = $this->isModuleOutputEnabled();

		return $isModuleOutputEnabled && $this->getConfigValue(self::GENERAL_IS_ENABLED, $store);
	}

	/**
	 * Get position to display on one step checkout
	 *
	 * @return array
	 */
	public function getAddressFieldPosition()
	{
		$fieldPosition = [];
		$sortedField   = $this->getSortedField();
		foreach ($sortedField as $field) {
			$fieldPosition[$field->getAttributeCode()] = [
				'sortOrder' => $field->getSortOrder(),
				'colspan'   => $field->getColspan(),
				'isNewRow'  => $field->getIsNewRow()
			];
		}

		return $fieldPosition;
	}

	/**
	 * Get attribute collection to show on osc and manage field
	 *
	 * @param bool|true $onlySorted
	 * @return array
	 */
	public function getSortedField($onlySorted = true)
	{
		$availableFields = [];
		$sortedFields    = [];
		$sortOrder       = 1;

		/** @var \Magento\Eav\Api\Data\AttributeInterface[] $collection */
		$collection = $this->attributeMetadataDataProvider->loadAttributesCollection(
			'customer_address',
			'customer_register_address'
		);
		foreach ($collection as $key => $field) {
			if (!$this->isAddressAttributeVisible($field)) {
				continue;
			}
			$availableFields[] = $field;
		}

		/** @var \Magento\Eav\Api\Data\AttributeInterface[] $collection */
		$collection = $this->attributeMetadataDataProvider->loadAttributesCollection(
			'customer',
			'customer_account_create'
		);
		foreach ($collection as $key => $field) {
			if (!$this->isCustomerAttributeVisible($field)) {
				continue;
			}
			$availableFields[] = $field;
		}

		$isNewRow    = true;
		$fieldConfig = $this->getFieldPosition();
		foreach ($fieldConfig as $field) {
			foreach ($availableFields as $key => $avField) {
				if ($field['code'] == $avField->getAttributeCode()) {
					$avField->setColspan($field['colspan'])
						->setSortOrder($sortOrder++)
						->setIsNewRow($isNewRow);
					$sortedFields[] = $avField;
					unset($availableFields[$key]);

					$this->checkNewRow($field['colspan'], $isNewRow);
					break;
				}
			}
		}

		return $onlySorted ? $sortedFields : [$sortedFields, $availableFields];
	}

	/**
	 * Check if address attribute can be visible on frontend
	 *
	 * @param $attribute
	 * @return bool|null|string
	 */
	public function isAddressAttributeVisible($attribute)
	{
		$code   = $attribute->getAttributeCode();
		$result = $attribute->getIsVisible();
		switch ($code) {
			case 'vat_id':
				$result = $this->addressHelper->isVatAttributeVisible();
				break;
			case 'region':
				$result = false;
				break;
		}

		return $result;
	}

	/**
	 * Check if customer attribute can be visible on frontend
	 *
	 * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
	 * @return bool|null|string
	 */
	public function isCustomerAttributeVisible($attribute)
	{
		$code = $attribute->getAttributeCode();
		if (in_array($code, ['gender', 'taxvat', 'dob'])) {
			return $attribute->getIsVisible();
		} else if (!$attribute->getIsUserDefined()) {
			return false;
		}

		return true;
	}

	/************************ Field Position *************************
	 * @return array|mixed
	 */
	public function getFieldPosition()
	{
		$fields = $this->getConfigValue(self::SORTED_FIELD_POSITION);

		try {
			$result = \Zend_Json::decode($fields);
		} catch (\Exception $e) {
			$result = [];
		}

		return $result;
	}

	/**
	 * @param $colSpan
	 * @param $isNewRow
	 * @return $this
	 */
	private function checkNewRow($colSpan, &$isNewRow)
	{
		if ($colSpan == 6 && $isNewRow) {
			$isNewRow = false;
		} else if ($colSpan == 12 || ($colSpan == 6 && !$isNewRow)) {
			$isNewRow = true;
		}

		return $this;
	}

	/**
	 * One step checkout page title
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getCheckoutTitle($store = null)
	{
		return $this->getGeneralConfig('title', $store) ?: 'One Step Checkout';
	}

	/************************ General Configuration *************************
	 *
	 * @param      $code
	 * @param null $store
	 * @return mixed
	 */
	public function getGeneralConfig($code = '', $store = null)
	{
		$code = $code ? self::GENERAL_CONFIGUARATION . '/' . $code : self::GENERAL_CONFIGUARATION;

		return $this->getConfigValue($code, $store);
	}

	/**
	 * One step checkout page description
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getCheckoutDescription($store = null)
	{
		return $this->getGeneralConfig('description', $store);
	}

	/**
	 * Get magento default country
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getDefaultCountryId($store = null)
	{
		return $this->objectManager->get('Magento\Directory\Helper\Data')->getDefaultCountry($store);
	}

	/**
	 * Default shipping method
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getDefaultShippingMethod($store = null)
	{
		return $this->getGeneralConfig('default_shipping_method', $store);
	}

	/**
	 * Default payment method
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getDefaultPaymentMethod($store = null)
	{
		return $this->getGeneralConfig('default_payment_method', $store);
	}

	/**
	 * Allow guest checkout
	 *
	 * @param $quote
	 * @param null $store
	 * @return bool
	 */
	public function getAllowGuestCheckout($quote, $store = null)
	{
		$allowGuestCheckout = boolval($this->getGeneralConfig('allow_guest_checkout', $store));

		if ($this->scopeConfig->isSetFlag(
			\Magento\Downloadable\Observer\IsAllowedGuestCheckoutObserver::XML_PATH_DISABLE_GUEST_CHECKOUT,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
			$store
		)
		) {
			foreach ($quote->getAllItems() as $item) {
				if (($product = $item->getProduct())
					&& $product->getTypeId() == \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE
				) {
					return false;
				}
			}
		}

		return $allowGuestCheckout;
	}

	/**
	 * Redirect To OneStepCheckout
	 * @param null $store
	 * @return bool
	 */
	public function isRedirectToOneStepCheckout($store = null)
	{
		return boolval($this->getGeneralConfig('redirect_to_one_step_checkout', $store));
	}

	/**
	 * Show billing address
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getShowBillingAddress($store = null)
	{
		return boolval($this->getGeneralConfig('show_billing_address', $store));
	}

	/**
	 * Google api key
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getGoogleApiKey($store = null)
	{
		return $this->getGeneralConfig('google_api_key', $store);
	}

	/**
	 * Google restric country
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getGoogleSpecificCountry($store = null)
	{
		return $this->getGeneralConfig('google_specific_country', $store);
	}

	/**
	 * Check if the page is https
	 *
	 * @return bool
	 */
	public function isGoogleHttps()
	{
		$isEnable = ($this->getAutoDetectedAddress() == 'google');

		return $isEnable && $this->_request->isSecure();
	}

	/**
	 * Get auto detected address
	 * @param null $store
	 * @return null|'google'|'pca'
	 */
	public function getAutoDetectedAddress($store = null)
	{
		return $this->getGeneralConfig('auto_detect_address', $store);
	}

	/**
	 * Login link will be hide if this function return true
	 *
	 * @param null $store
	 * @return bool
	 */
	public function isDisableAuthentication($store = null)
	{
		return !$this->getDisplayConfig('is_enabled_login_link', $store);
	}

	/********************************** Display Configuration *********************
	 *
	 * @param $code
	 * @param null $store
	 * @return mixed
	 */
	public function getDisplayConfig($code = '', $store = null)
	{
		$code = $code ? self::DISPLAY_CONFIGUARATION . '/' . $code : self::DISPLAY_CONFIGUARATION;

		return $this->getConfigValue($code, $store);
	}

	/**
	 * Item detail will be hided if this function return 'true'
	 *
	 * @param null $store
	 * @return bool
	 */
	public function isDisabledReviewCartSection($store = null)
	{
		return !$this->getDisplayConfig('is_enabled_review_cart_section', $store);
	}

	/**
	 * Product image will be hided if this function return 'true'
	 *
	 * @param null $store
	 * @return bool
	 */
	public function isHideProductImage($store = null)
	{
		return !$this->getDisplayConfig('is_show_product_image', $store);
	}

	/**
	 * Coupon will be hided if this function return 'true'
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function disabledPaymentCoupon($store = null)
	{
		return $this->getDisplayConfig('show_coupon', $store) != ComponentPosition::SHOW_IN_PAYMENT;
	}

	/**
	 * Coupon will be hided if this function return 'true'
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function disabledReviewCoupon($store = null)
	{
		return $this->getDisplayConfig('show_coupon', $store) != ComponentPosition::SHOW_IN_REVIEW;
	}

	/**
	 * Comment will be hided if this function return 'true'
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function isDisabledComment($store = null)
	{
		return !$this->getDisplayConfig('is_enabled_comments', $store);
	}

	/**
	 * Term and condition checkbox in payment block will be hided if this function return 'true'
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function disabledPaymentTOC($store = null)
	{
		return $this->getDisplayConfig('show_toc', $store) != ComponentPosition::SHOW_IN_PAYMENT;
	}

	/**
	 * Term and condition checkbox in review will be hided if this function return 'true'
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function disabledReviewTOC($store = null)
	{
		return $this->getDisplayConfig('show_toc', $store) != ComponentPosition::SHOW_IN_REVIEW;
	}

	/**
	 * GiftMessage will be hided if this function return 'true'
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function isDisabledGiftMessage($store = null)
	{
		return !$this->getDisplayConfig('is_enabled_gift_message', $store);
	}

	/**
	 * Gift message items
	 * @param null $store
	 * @return bool
	 */
	public function isEnableGiftMessageItems($store = null)
	{
		return (bool)$this->getDisplayConfig('is_enabled_gift_message_items', $store);
	}


	/**
	 * Gift wrap block will be hided if this function return 'true'
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function isDisabledGiftWrap($store = null)
	{
		$giftWrapEnabled = $this->getDisplayConfig('is_enabled_gift_wrap', $store);
		$giftWrapAmount  = $this->getOrderGiftwrapAmount();

		return !$giftWrapEnabled || ($giftWrapAmount < 0);
	}

	/**
	 * Gift wrap amount
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getOrderGiftWrapAmount($store = null)
	{
		return doubleval($this->getDisplayConfig('gift_wrap_amount', $store));
	}

	/**
	 * @return array
	 */
	public function getGiftWrapConfiguration()
	{
		return [
			'gift_wrap_type'   => $this->getGiftWrapType(),
			'gift_wrap_amount' => $this->formatGiftWrapAmount()
		];
	}

	/**
	 * Gift wrap type
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getGiftWrapType($store = null)
	{
		return $this->getDisplayConfig('gift_wrap_type', $store);
	}

	/**
	 * @return mixed
	 */
	public function formatGiftWrapAmount()
	{
		$objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
		$giftWrapAmount = $objectManager->create('Magento\Checkout\Helper\Data')->formatPrice($this->getOrderGiftWrapAmount());

		return $giftWrapAmount;
	}

	/**
	 * Newsleter block will be hided if this function return 'true'
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function isDisabledNewsletter($store = null)
	{
		return !$this->getDisplayConfig('is_enabled_newsletter', $store);
	}

	/**
	 * Is newsleter subcribed default
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function isSubscribedByDefault($store = null)
	{
		return (bool)$this->getDisplayConfig('is_checked_newsletter', $store);
	}

	/**
	 * Social Login On Checkout Page
	 * @param null $store
	 * @return bool
	 */
	public function isDisabledSocialLoginOnCheckout($store = null)
	{
		return !$this->getDisplayConfig('is_enabled_social_login', $store);
	}

	/**
	 * Delivery Time
	 * @param null $store
	 * @return bool
	 */
	public function isDisabledDeliveryTime($store = null)
	{
		return !$this->getDisplayConfig('is_enabled_delivery_time', $store);
	}

	/**
	 * House Security Code
	 * @param null $store
	 * @return bool
	 */
	public function isDisabledHouseSecurityCode($store = null)
	{
		return !$this->getDisplayConfig('is_enabled_house_security_code', $store);
	}

	/**
	 * Delivery Time Format
	 *
	 * @param null $store
	 *
	 * @return string 'dd/mm/yy'|'mm/dd/yy'|'yy/mm/dd'
	 */
	public function getDeliveryTimeFormat($store = null)
	{
		$deliveryTimeFormat = $this->getDisplayConfig('delivery_time_format', $store);

		return $deliveryTimeFormat ?: \Mageplaza\Osc\Model\System\Config\Source\DeliveryTime::DAY_MONTH_YEAR;
	}

	/**
	 * Delivery Time Off
	 * @param null $store
	 * @return bool|mixed
	 */
	public function getDeliveryTimeOff($store = null)
	{
		return $this->getDisplayConfig('delivery_time_off', $store);
	}

	/**
	 * Survey
	 * @param null $store
	 * @return bool
	 */
	public function isDisableSurvey($store = null){
		return !$this->getDisplayConfig('is_enabled_survey', $store);
	}

	/**
	 * Survey Question
	 * @param null $store
	 * @return mixed
	 */
	public function getSurveyQuestion($store = null){
		return $this->getDisplayConfig('survey_question', $store);
	}

	/**
	 * Survey Answers
	 * @param null $stores
	 * @return mixed
	 */
	public function getSurveyAnswers($stores = null){
		return unserialize($this->getDisplayConfig('survey_answers',$stores));
	}

	/**
	 * Allow Customer Add Other Option
	 * @param null $stores
	 * @return mixed
	 */
	public function isAllowCustomerAddOtherOption($stores = null){
		return $this->getDisplayConfig('allow_customer_add_other_option',$stores);
	}

	/**
	 * Get layout tempate: 1 or 2 or 3 columns
	 *
	 * @param null $store
	 * @return string
	 */
	public function getLayoutTemplate($store = null)
	{
		return 'Mageplaza_Osc/' . $this->getDesignConfig('page_layout', $store);
	}

	/***************************** Design Configuration *****************************
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function getDesignConfig($code = '', $store = null)
	{
		$code = $code ? self::DESIGN_CONFIGUARATION . '/' . $code : self::DESIGN_CONFIGUARATION;

		return $this->getConfigValue($code, $store);
	}

	/**
	 * @return bool
	 */
	public function isUsedMaterialDesign(){
		return $this->getDesignConfig('page_design') == 'material' ? true : false;
	}

	/***************************** GeoIP Configuration *****************************
	 *
	 * @param null $store
	 * @return mixed
	 */
	public function isEnableGeoIP($store = null)
	{
		return boolval($this->getConfigValue(self::GEO_IP_CONFIGUARATION.'/is_enable_geoip', $store));
	}

	/**
	 * @param null $store
	 * @return mixed
	 */
	public function getDownloadPath($store = null){
		return $this->getConfigValue(self::GEO_IP_CONFIGUARATION.'/download_path',$store);
	}
	/***************************** Compatible Modules *****************************
	 *
	 * @return bool
	 */
	public function isEnabledMultiSafepay()
	{
		return $this->_moduleManager->isOutputEnabled('MultiSafepay_Connect');
	}

	/**
	 * @return bool
	 */
	public function isEnableModulePostNL()
	{
		return $this->isModuleOutputEnabled('TIG_PostNL');
	}

	/**
	 * Get current theme id
	 * @return mixed
	 */
	public function getCurrentThemeId(){
		return $this->getConfigValue(\Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID);
	}
}
