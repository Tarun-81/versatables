<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Helper\Data;

/**
 * Class Metadata
 * @package Cart2Quote\Quotation\Helper\Data
 */
final class Metadata extends \Magento\Framework\App\Helper\AbstractHelper implements MetadataInterface
{

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var \Magento\Framework\Module\ResourceInterface
     */
    private $resource;

    /**
     * Metadata constructor.
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Module\ResourceInterface $resource
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Module\ResourceInterface $resource,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->productMetadata = $productMetadata;
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getPhpVersion()
    {
        return phpversion();
    }

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        if (defined('Magento\Framework\AppInterface::VERSION')) {
            return \Magento\Framework\AppInterface::VERSION;
        } else {
            return $this->productMetadata->getVersion();
        }
    }

    /**
     * @return string
     */
    public function getMagentoEdition()
    {
        if (defined('Magento\Framework\AppInterface::EDITION')) {
            return \Magento\Framework\AppInterface::EDITION;
        } else {
            return $this->productMetadata->getEdition();
        }
    }

    /**
     * @return bool|string
     */
    public function getIoncubeVersion()
    {
        if (extension_loaded('ionCube Loader')) {
            return $this->getIoncubeLoaderVersion();
        }

        return false;
    }

    /**
     * @return string
     */
    public function getIoncubeLoaderVersion()
    {
        if (function_exists('ioncube_loader_iversion')) {
            $ioncubeLoaderIversion = ioncube_loader_iversion();
            $extra = 0;
            if ($ioncubeLoaderIversion >= 100000) {
                $extra = 1;
            }

            $ioncubeLoaderVersionMajor = (int)substr($ioncubeLoaderIversion, 0, 1 + $extra);
            $ioncubeLoaderVersionMinor = (int)substr($ioncubeLoaderIversion, 1 + $extra, 2);
            $ioncubeLoaderVersionRevision = (int)substr($ioncubeLoaderIversion, 3 + $extra, 2);
            $ioncubeLoaderVersion = "$ioncubeLoaderVersionMajor.$ioncubeLoaderVersionMinor.$ioncubeLoaderVersionRevision";
        } else {
            $ioncubeLoaderVersion = ioncube_loader_version();
        }

        return $ioncubeLoaderVersion;
    }

    /**
     * @return bool|string
     */
    public function getCart2QuoteVersion()
    {
        return $this->getModuleVersion('Cart2Quote_Quotation');
    }

    /**
     * @param $moduleName
     * @return bool|string
     */
    public function getModuleVersion($moduleName)
    {
        $version = $this->resource->getDbVersion($moduleName);
        if (isset($version) && !empty($version)) {
            return $version;
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getNot2OrderVersion()
    {
        return $this->getModuleVersion('Cart2Quote_Not2Order');
    }

    /**
     * @return bool|string
     */
    public function getSupportDeskVersion()
    {
        return $this->getModuleVersion('Cart2Quote_Desk');
    }

    /**
     * @return bool|string
     */
    public function getDeskEmailVersion()
    {
        return $this->getModuleVersion('Cart2Quote_DeskEmail');
    }
}
