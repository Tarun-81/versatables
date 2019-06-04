<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**#@+
     * Admin config settings
     */
    const XML_DOWNLOADS_DISPLAY_SIZE                   = 'mageworx_downloads/main/display_size';
    const XML_DOWNLOADS_SIZE_PRECISION                 = 'mageworx_downloads/main/size_precision';
    const XML_DOWNLOADS_GROUP_BY_SECTION               = 'mageworx_downloads/main/group_by_section';
    const XML_DOWNLOADS_SORT_ORDER                     = 'mageworx_downloads/main/sort_order';
    const XML_DOWNLOADS_DISPLAY_DOWNLOADS              = 'mageworx_downloads/main/display_downloads';
    const XML_DOWNLOADS_PRODUCT_DOWNLOADS_TAB_TITLE    = 'mageworx_downloads/main/product_downloads_tab_title';
    const XML_DOWNLOADS_PRODUCT_DOWNLOADS_TITLE        = 'mageworx_downloads/main/product_downloads_title';
    const XML_DOWNLOADS_FILE_DOWNLOADS_TITLE           = 'mageworx_downloads/main/file_downloads_title';
    const XML_DOWNLOADS_HIDE_FILES                     = 'mageworx_downloads/main/hide_files';
    const XML_DOWNLOADS_HOW_TO_DOWNLOAD_MESSAGE        = 'mageworx_downloads/main/how_to_download_message';
    const XML_MAXIMUM_ALLOWED_FILE_SIZE                = 'mageworx_downloads/main/max_file_size';
    const XML_FILE_DOWNLOADS_TITLE                     = 'mageworx_downloads/main/file_downloads_title';


    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var \Magento\Framework\File\Size
     */
    protected $fileSize;

    /**
     * Data constructor.
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\File\Size $fileSize
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->customerUrl = $customerUrl;
        $this->fileSize = $fileSize;
        parent::__construct($context);
    }


    /**
     * Check if display file size
     *
     * @param int $storeId
     * @return bool
     */
    public function isDisplaySize($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_DOWNLOADS_DISPLAY_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve size precision
     *
     * @param int $storeId
     * @return int
     */
    public function getSizePrecision($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_DOWNLOADS_SIZE_PRECISION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if group attachments by section
     *
     * @param int $storeId
     * @return bool
     */
    public function isGroupBySection($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_DOWNLOADS_GROUP_BY_SECTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve sort order
     *
     * @param int $storeId
     * @return int
     */
    public function getSortOrder($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_DOWNLOADS_SORT_ORDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if display downloads
     *
     * @param int $storeId
     * @return bool
     */
    public function isDisplayDownloads($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_DOWNLOADS_DISPLAY_DOWNLOADS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve product downloads title
     *
     * @param int $storeId
     * @return string
     */
    public function getProductDownloadsTabTitle($storeId = null)
    {
        $title = (string)$this->scopeConfig->getValue(
            self::XML_DOWNLOADS_PRODUCT_DOWNLOADS_TAB_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (empty($title)) {
            $title = 'Downloads';
        }

        return $title;
    }

    /**
     * Retrieve product downloads title
     *
     * @param int $storeId
     * @return string
     */
    public function getProductDownloadsTitle($storeId = null)
    {
        $title = (string)$this->scopeConfig->getValue(
            self::XML_DOWNLOADS_PRODUCT_DOWNLOADS_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $title;
    }

    /**
     * Check if hide files
     *
     * @param int $storeId
     * @return bool
     */
    public function isHideFiles($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_DOWNLOADS_HIDE_FILES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve product downloads message
     *
     * @param int|null $storeId
     * @return string
     */
    public function getHowToDownloadMessage($storeId = null)
    {
        $title = trim($this->scopeConfig->getValue(
            self::XML_DOWNLOADS_HOW_TO_DOWNLOAD_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));

        if (empty($title)) {
            $title = htmlspecialchars(__('You have to %login% or %register% to download this file'));
        }

        $login = "<a href=" . $this->customerUrl->getLoginUrl() . ">" . __('Login') . "</a>";
        $register = "<a href=" . $this->customerUrl->getRegisterUrl() . ">" . __('Register') . "</a>";
        return str_replace(['%login%', '%register%'], [$login, $register], $title);
    }

    /**
     * Retrieve maximum allowed file size
     *
     * @param int $storeId
     * @return string
     */
    public function getMaximumAllowedFileSize($storeId = null)
    {
        $result = (string)$this->scopeConfig->getValue(
            self::XML_MAXIMUM_ALLOWED_FILE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $result = $this->parseSize($result);

        if ($result < 1) {
            $result = $this->fileSize->getMaxFileSize();
        }

        $result = min($result, $this->fileSize->getMaxFileSize());

        return $result;
    }

    /**
     * @param $size
     * @return float
     */
    public function parseSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }

    /**
     * @return string
     */
    public function getFileDownloadsTitle($storeId = null)
    {
        $title = $this->scopeConfig->getValue(
            self::XML_FILE_DOWNLOADS_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (empty($title)) {
            $title = __('File Downloads');
        }
        return $title;
    }
}
