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

namespace Magenest\ZohocrmIntegration\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Connect to ZohocrmIntegration using REST API
 *
 * Class Connector
 *
 * @package Magenest\ZohocrmIntegration\Model
 */
class Connector
{

    /**
     * Configuration value
     *
     * @const
     */
    const XML_PATH_ZOHO_CONFIG_EMAIL = 'zohocrm/config/email';
    const XML_PATH_ZOHO_CONFIG_PASSWD = 'zohocrm/config/passwd';
    const XML_PATH_ZOHO_CONFIG_AUTHTOKEN = 'zohocrm/config/auth_token';
    const XML_PATH_ZOHO_CONFIG_SAVE_REPORT = 'zohocrm/config/save_report';
    const XML_PATH_ZOHO_CONTACT_ENABLE = 'zohocrm/zohocrm_sync/contact';
    const XML_PATH_ZOHO_LEAD_ENABLE = 'zohocrm/zohocrm_sync/lead';
    const XML_PATH_ZOHO_ACCOUNT_ENABLE = 'zohocrm/zohocrm_sync/account';
    const XML_PATH_ZOHO_ORDER_ENABLE = 'zohocrm/zohocrm_sync/order';
    const XML_PATH_ZOHO_PRODUCT_ENABLE = 'zohocrm/zohocrm_sync/product';
    const XML_PATH_ZOHO_CAMPAIGN_ENABLE = 'zohocrm/zohocrm_sync/campaign';
    const XML_PATH_ZOHO_INVOICE_ENABLE = 'zohocrm/zohocrm_sync/invoice';

    const XML_PATH_ZOHO_CONFIG_CLIENT_SECRET = 'zohocrm/config/client_secret';
    const XML_PATH_ZOHO_CONFIG_CLIENT_ID = 'zohocrm/config/client_id';
    const XML_PATH_ZOHO_CONFIG_ACCESS_TOKEN = 'zohocrm/config/access_token';
    const XML_PATH_ZOHO_CONFIG_REDIRECT_URI = 'zohocrm/system_config_getauth/getAuthCode';
    const XML_PATH_ZOHO_CONFIG_REFRESH_TOKEN = 'zohocrm/config/refresh_token';
    const XML_PATH_ZOHO_CONFIG_TIME_GET_TOKEN = 'zohocrm/config/time_get_token';
    const XML_PATH_ZOHO_CONFIG_ZOHO_DOMAINS = 'zohocrm/config/zoho_domains';
    const MAX_RECORD_PER_CONNECT = 100;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $_httpClientFactory;

    /**
     * Core Config Data
     *
     * @var $_scopeConfig \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Using save Report
     *
     * @var ReportFactory
     */
    protected $_reportFactory;

    /**
     * @var string
     */
    protected $base_url;


    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * Connector constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConfig $resourceConfig
     * @param ZendClientFactory $httpClientFactory
     * @param ReportFactory $reportFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResourceConfig $resourceConfig,
        ZendClientFactory $httpClientFactory,
        ReportFactory $reportFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter

    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_resourceConfig = $resourceConfig;
        $this->_reportFactory = $reportFactory;
        $this->base_url = 'https://www.zohoapis'.$this->getZohoDomains().'/crm/v2/';
        $this->_httpClientFactory = $httpClientFactory;
        $this->configWriter = $configWriter;

    }
    /**
     * @return string
     */
    public function getUrlGetAuth()
    {
        return 'https://accounts.zoho'.$this->getZohoDomains().'/oauth/v2/auth';
    }
    /**
     * @return string
     */
    public function getUrlGetToken()
    {
        return 'https://accounts.zoho'.$this->getZohoDomains().'/oauth/v2/token';
    }
    /**
     * @return string
     */
    public function getReportConfig()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_SAVE_REPORT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    protected function getZohoDomains(){
        return $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_ZOHO_DOMAINS, ScopeInterface::SCOPE_STORE);
    }

    public function getZohoRecordUrl()
    {
        return 'https://crmplus.zoho'.$this->getZohoDomains().'/index.do#crm/tab/';
    }


    public function _sendRequestV2($path, $paramter = null, $method = \Zend_Http_Client::POST)
    {
        $authtoken = $this->getAuthCode2();
        $url = $this->base_url . $path . '/upsert';
        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $authtoken
        ]);
        $client = $this->_httpClientFactory->create();
        $client->setUri($url);
        $client->setConfig(['timeout' => 300]);
        $client->setHeaders(['Authorization: Zoho-oauthtoken ' . $authtoken]);
        $client->setRawData(json_encode($paramter));
        $response = $client->request($method)->getBody();

        $array = json_decode($response, true);
        return $array;
    }

    /**
     * Get fields from a Module in ZohoCRM
     *
     * @param  string $table
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    public function getFields($table)
    {
        $authtoken = $this->getAuthCode2();
        $url = $this->base_url . 'settings/fields?module=' . $table;

        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $authtoken
        ]);
        $client = $this->_httpClientFactory->create();
        $client->setUri($url);
        $client->setConfig(['timeout' => 300]);
        $client->setHeaders(['Authorization: Zoho-oauthtoken ' . $authtoken]);

        $response = $client->request(\Zend_Http_Client::GET)->getBody();
        $result = json_decode($response, true);

        $field_zoho = [];
        $type_array = [
            'multiselectpicklist',
            'lookup',
            'picklist',
            'ownerlookup',
        ];
        foreach ($result['fields'] as $key => $value) {
            if (!empty($value['field_label'])) {
                $type = $value['data_type'];
                if (!in_array($type, $type_array)) {
                    $label = $value['field_label'];
                    $field_zoho[$label] = $label . ' (' . $type . ')';
                    // save Zoho Field to array
                }
            }
        }
        return serialize($field_zoho);
    }

    /**
     * Insert Record in ZohoCRM
     *
     * @param string $table
     * @param mixed $post
     * @return  mixed;
     */

    public function insertRecordsV2($table, $post)
    {
        $path = $table;
        $paramter = $post;
        $response = $this->_sendRequestV2($path, $paramter, \Zend_Http_Client::POST);
        return $response;
    }

    /**
     * Save Report: History sync     *
     * @param $id
     * @param $action
     * @param $table
     */
    public function saveReport($id, $action, $table)
    {
        if (!$this->_scopeConfig->isSetFlag(self::XML_PATH_ZOHO_CONFIG_SAVE_REPORT, ScopeInterface::SCOPE_STORE)) {
            return;
        }

        $model = $this->_reportFactory->create();
        $model->saveReport($id, $action, $table);
        return;
    }

    /**
     * Get Access Token
     *
     * @param  array $data
     * @param  bool|false $refresh
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function getAuthCode($data = array(), $refresh = false)
    {
        $authkey = $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_ACCESS_TOKEN, ScopeInterface::SCOPE_STORE);
        if (!$authkey || $refresh) {
            $redirectUri = $authcode = null;
            if (is_array($data) && !empty($data)) {
                $authcode = $data['code'];
                $redirectUri = $data['redirect_uri'];
                $url = $data['accounts-server'] . '/oauth/v2/token';
            }
            $clientId = $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_CLIENT_ID, ScopeInterface::SCOPE_STORE);
            $clientSecret = $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_CLIENT_SECRET, ScopeInterface::SCOPE_STORE);
            $url = $url ? $url : $this->getUrlGetToken();
            $data = [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'code' => $authcode,
                'grant_type' => 'authorization_code'
            ];
            $client = $this->_httpClientFactory->create();
            $client->setUri($url);
            $client->setConfig(['timeout' => 300]);

            $client->setParameterPost($data);

            $response = $client->request('POST')->getBody();


            $result = json_decode($response, true);
            if (isset($result['error'])) {
                $authkey = $result['error'];
            } else {
                $authkey = $result['access_token'];
                $refreshKey = $result['refresh_token'];
                $this->configWriter->save(self::XML_PATH_ZOHO_CONFIG_REFRESH_TOKEN, $refreshKey, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
                $this->configWriter->save(self::XML_PATH_ZOHO_CONFIG_TIME_GET_TOKEN, time(), $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
                $this->_resourceConfig->saveConfig(self::XML_PATH_ZOHO_CONFIG_REFRESH_TOKEN, $refreshKey, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
                $this->_resourceConfig->saveConfig(self::XML_PATH_ZOHO_CONFIG_TIME_GET_TOKEN, time(), $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

            }
            $this->_resourceConfig->saveConfig(self::XML_PATH_ZOHO_CONFIG_ACCESS_TOKEN, $authkey, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

        }
        return $authkey;
    }
    /**
     * Get Access Token Fresh
     *
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    function getAuthCodeFresh()
    {
        $refreshToken = $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_REFRESH_TOKEN, ScopeInterface::SCOPE_STORE);
        $clientId = $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_CLIENT_ID, ScopeInterface::SCOPE_STORE);
        $clientSecret = $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_CLIENT_SECRET, ScopeInterface::SCOPE_STORE);
        $url = $this->getUrlGetToken();
        $data = [
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'refresh_token'
        ];
        $client = $this->_httpClientFactory->create();
        $client->setUri($url);
        $client->setConfig(['timeout' => 300]);
        $client->setParameterPost($data);
        $response = $client->request('POST')->getBody();
        $result = json_decode($response, true);
        if (isset($result['error'])) {
            $authkey = $result['error'];
        } else {
            $authkey = $result['access_token'];
            $this->configWriter->save(self::XML_PATH_ZOHO_CONFIG_ACCESS_TOKEN, $authkey, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
            $this->configWriter->save(self::XML_PATH_ZOHO_CONFIG_TIME_GET_TOKEN, time(), $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
            $this->_resourceConfig->saveConfig(self::XML_PATH_ZOHO_CONFIG_TIME_GET_TOKEN, time(), $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

        }
        $this->_resourceConfig->saveConfig(self::XML_PATH_ZOHO_CONFIG_ACCESS_TOKEN, $authkey, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        return $authkey;

    }

    protected function getAuthCode2(){
        $timeGetToken = $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_TIME_GET_TOKEN, ScopeInterface::SCOPE_STORE);
        if ((time() - $timeGetToken) > 2000) {
            $authtoken = $this->getAuthCodeFresh();
        } else {
            $authtoken = $this->getAuthCode();
        }
        return $authtoken;
    }
}