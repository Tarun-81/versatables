<?php
/**
 * * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\System\Config\Getauth;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magenest\ZohocrmIntegration\Model\Connector;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class GetAuthCode Controllers
 *
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\System\Config\GetAuthCode
 */
class GetAuthCode extends Action
{
    const ERROR_CONNECT_TO_ZOHOCRM = 'INVALID_CLIENTID';

    /**
     * @var /Magenest/ZohoCrm/Model/Connector
     */
    protected $_connector;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @param Context $context
     * @param Connector $connector
     */
    public function __construct(
        Context $context,
        Connector $connector,
        JsonFactory $jsonFactory
    ) {
        $this->resultJsonFactory = $jsonFactory;
        parent::__construct($context);
        $this->_connector   = $connector;
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        $redirectUri = $this->getUrl(Connector::XML_PATH_ZOHO_CONFIG_REDIRECT_URI);

        $data = $this->getRequest()->getParams();
        if ($data) {
            $data['redirect_uri'] = $redirectUri;
            $this->_connector->getAuthCode($data, true);
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->getUrl('admin/system_config/edit/section/zohocrm/'));
        return $resultRedirect;
    }
}
