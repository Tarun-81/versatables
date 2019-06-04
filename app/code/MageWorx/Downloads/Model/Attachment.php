<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model;

class Attachment extends \Magento\Framework\Model\AbstractModel
{

    const STATUS_ENABLED     = 1;
    const STATUS_DISABLED    = 0;

    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_downloads_attachment';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $cacheTag     = 'mageworx_downloads_attachment';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_downloads_attachment';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\Downloads\Model\ResourceModel\Attachment');
    }

    /**
     *
     * @return array
     */
    public function getDefaultValues()
    {
        return [
            'section_id'  => \MageWorx\Downloads\Model\Section::DEFAULT_ID,
            'assign_type' => 1,
            'is_active'   => self::STATUS_DISABLED
        ];
    }

    /**
     *
     * @return string|null
     */
    public function getLinkType()
    {
        if ($this->getId()) {
            if ($this->getFilename()) {
                return \MageWorx\Downloads\Helper\Download::LINK_TYPE_FILE;
            } elseif ($this->getUrl()) {
                return \MageWorx\Downloads\Helper\Download::LINK_TYPE_URL;
            }
        }
        return null;
    }

    /**
     * @return void
     */
    public function clearAttachment()
    {
        $this->setData('filename', '');
        $this->setData('type', '');
    }

    /**
     *
     * @return boolean
     */
    public function getContentType()
    {
        if ($this->getData('content_type')) {
            return $this->getData('content_type');
        }

        if ($this->getId()) {
            if ($this->getFilename()) {
                return \MageWorx\Downloads\Model\Attachment\Source\ContentType::CONTENT_FILE;
            } elseif ($this->getUrl()) {
                return \MageWorx\Downloads\Model\Attachment\Source\ContentType::CONTENT_URL;
            }
        }
        return null;
    }

    /**
     *
     * @return boolean
     */
    public function isFileContent()
    {
        $type = $this->getContentType();
        return \MageWorx\Downloads\Model\Attachment\Source\ContentType::CONTENT_FILE == $type;
    }

    /**
     * @return boolean
     */
    public function isUrlContent()
    {
        $type = $this->getContentType();
        return \MageWorx\Downloads\Model\Attachment\Source\ContentType::CONTENT_URL == $type;
    }

    /**
     * @return array|mixed
     */
    public function getProducts()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('products');
        if (is_null($array)) {
            $array = $this->getResource()->getProducts($this);
            $this->setData('products', $array);
        }
        return $array;
    }

    /**
     *
     * @return int
     */
    public function getDownloadsLeft()
    {
        $downloadsLeft = $this->getDownloadsLimit() - $this->getDownloads();
        return ($downloadsLeft < 0) ? 0 : $downloadsLeft;
    }
}
