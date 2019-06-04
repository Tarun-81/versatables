<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Helper;

class StoreUrl extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    /**
     * Get store base url
     *
     * @param int $storeId
     * @param string $type
     * @return string
     */
    public function getStoreBaseUrl($storeId = null, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        return rtrim($this->_storeManager->getStore($storeId)->getBaseUrl($type), '/') . '/';
    }

    /**
     * Get url
     *
     * @param string $url
     * @param int|null $storeId
     * @param string $type
     * @return string
     */
    public function getUrl($url, $storeId = null, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        return $this->getStoreBaseUrl($storeId, $type) . ltrim($url, '/');
    }
}
