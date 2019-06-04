<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Catalog\Product;

use Magento\Framework\View\Element\Template;
use MageWorx\Downloads\Helper\Data as HelperData;
use Magento\Framework\App\Filesystem\DirectoryList;
use MageWorx\Downloads\Model\Attachment\Source\FileSize;

class Link extends Template
{

    /**
     *
     * @var \MageWorx\Downloads\Helper\Data
     */
    protected $helperData;

    /**
     *
     * @param HelperData $helperData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorx\Downloads\Helper\Data $helperData,
        Template\Context $context,
        $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return boolean
     */
    public function isDisplaySize()
    {
        return $this->helperData->isDisplaySize();
    }

    /**
     * @param int $attachmentId
     * @return string
     */
    public function getAttachmentLink($attachmentId)
    {
        return $this->getUrl('mwdownloads/download/link', ['id' => $attachmentId]);
    }

    /**
     * Retrieve HTML wrapper for icon
     *
     * @param \MageWorx\Downloads\Model\Attachment $item
     * @return string
     */
    public function getIconImgTag($item)
    {
        $name = $item->getType();
        if ($name) {
            return '<img src="' . $this->getIconUrl($name) . '" alt="' . $name . '"/>';
        }
        return '';
    }

    /**
     * Retrieve attachment icon URL
     *
     * @param string $name
     * @return string
     */
    public function getIconUrl($name)
    {
        $iconName = strtolower($name) . '.png';
        $iconUrl  = $this->_assetRepo->getUrl('MageWorx_Downloads::images/filetypes/' . $iconName);

        if (!$this->isImageExists($iconUrl)) {
            $iconName = 'default.png';
            $iconUrl = $this->_assetRepo->getUrl('MageWorx_Downloads::images/filetypes/' . $iconName);
        }
        return $iconUrl;
    }

    /**
     *
     * @return boolean
     */
    public function isDisplayDownloads()
    {
        return $this->helperData->isDisplayDownloads();
    }

    /**
     * Retrieve formated string
     *
     * @param int $size
     * @return string
     */
    public function getPrepareFileSize($size)
    {
        $parsedSize = 0;
        $type       = '';
        $round      = 1;
        $b  = __('B');
        $kb = __('KB');
        $mb = __('MB');
        $kbSize = 1024;
        $mbSize = $kbSize * $kbSize;

        switch ($this->helperData->getSizePrecision()) {
            case FileSize::FILE_SIZE_PRECISION_AUTO:
                if ($size >= $kbSize && $size < $mbSize) {
                    $parsedSize = $size / $kbSize;
                    $type = $kb;
                } elseif ($size >= $mbSize) {
                    $parsedSize = $size / $mbSize;
                    $type = $mb;
                } else {
                    $parsedSize = $size;
                    $type = $b;
                    $round = 0;
                }
                break;

            case FileSize::FILE_SIZE_PRECISION_MEGA:
                $parsedSize = $size / $mbSize;
                $type = $mb;
                $round = 2;
                break;

            case FileSize::FILE_SIZE_PRECISION_KILO:
                $parsedSize = $size / $kbSize;
                $type = $kb;
                break;

            default:
                $parsedSize = $size;
                $type = $b;
                $round = 0;
                break;
        }
        return round($parsedSize, $round) . ' ' . $type;
    }

    /**
     *
     * @param string $iconUrl
     * @return boolean
     */
    protected function isImageExists($iconUrl)
    {
        $iconUrl = preg_replace('/\/version[0-9]*/', '', $iconUrl);
        
        if (strpos($iconUrl, '/' . DirectoryList::STATIC_VIEW . '/') !== false) {
            $lengtn = strpos($iconUrl, '/' . DirectoryList::STATIC_VIEW . '/') + strlen(DirectoryList::STATIC_VIEW) + 2;
            $path = substr_replace($iconUrl, '', 0, $lengtn);
            $absolutePath = $this->_filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW)->getAbsolutePath($path);
            if (file_exists($absolutePath)) {
                return true;
            }
        }
        return false;
    }
}
