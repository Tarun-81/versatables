<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ZohocrmIntegration
 * @author   ThaoPV
 */
namespace Magenest\ZohocrmIntegration\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magenest\ZohocrmIntegration\Model\Connector;

/**
 * Class GetAuth
 *
 * @package Magenest\ZohocrmIntegration\Block\Adminhtml\System\Config
 */
class GetAuthorizationCode extends FormField
{
    /**
     * client_id
     *
     * @var string
     */
    protected $client_id = 'zohocrm_config_client_id';


    /**
     * Get Auth Token Label
     *
     * @var string
     */
    protected $_authCodeButtonLabel = 'Get Authorization Code';

    protected $_scope = "ZohoCRM.modules.ALL,ZohoCRM.settings.all";

    protected $_redirectUri = "zohocrm/system_config_getauth/getAuthCode";

    protected $connector;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Connector $connector,
        array $data = []
    )
    {
        $this->connector = $connector;
        parent::__construct($context, $data);
    }
    /**
     * Set ClientId ZohoCRM
     *
     * @param  $clientId
     * @return $this
     */
    public function setClientId($clientId)
    {
        $this->client_id = $clientId;
        return $this;
    }

    /**
     * Get ClientId ZohoCRM
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    public function getScope()
    {
        return $this->_scope;
    }
    /**
     * Get getRedirectUri
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->getUrl($this->_redirectUri);
    }
    /**
     * Get Url get token
     *
     * @return string
     */
    public function getUrlGetToken(){
        return $this->connector->getUrlGetAuth();
    }
    /**
     * Set Get Authorization Code Button label
     *
     * @param  $getAuthButtonLabel
     * @return $this
     */
    public function setButtonLabel($getAuthCodeButtonLabel)
    {
        $this->_authCodeButtonLabel = $getAuthCodeButtonLabel;
        return $this;
    }

    /**
     * Set template to itself
     *
     * @return \Magenest\ZohocrmIntegration\Block\Adminhtml\System\Config\GetAuth
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/getauthcode.phtml');
        }

        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel  = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->_authCodeButtonLabel;
        $this->addData(
            [
             'button_label' => __($buttonLabel),
             'html_id'      => $element->getHtmlId(),
             'ajax_url'     => $this->_urlBuilder->getUrl('zohocrm/system_config_getauth/getAuthCode'),
            ]
        );

        return $this->_toHtml();
    }




}
