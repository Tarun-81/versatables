<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Model\ResourceModel;

use MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory as AttachmentCollectionFactory;

class Section extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     * @var \MageWorx\Downloads\Model\Attachment
     */
    protected $attachment;

    /**
     *
     * @var \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollectionFactory;

    public function __construct(
        AttachmentCollectionFactory $attachmentCollectionFactory,
        \MageWorx\Downloads\Model\Attachment $attachment,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->attachment = $attachment;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_downloads_section', 'section_id');
    }

    public function getEnabledSections()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('is_active = ?', \MageWorx\Downloads\Model\Section::STATUS_ENABLED)
            ->order('name ' . \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        return $this->getConnection()->fetchAll($select);
    }

    /**
     * Process section data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId() != \MageWorx\Downloads\Model\Section::DEFAULT_ID) {
            $data = $this->attachment->getResource()->getSectionAttachments($object->getId());
            if ($data) {
                $attachmentIds = array_keys($data);
                $attachmentCollection = $this->attachmentCollectionFactory->create();
                $attachmentCollection->addFieldToFilter($this->attachment->getIdFieldName(), $attachmentIds)->load();

                foreach ($attachmentCollection as $attachment) {
                    $attachment->setSectionId(\MageWorx\Downloads\Model\Section::DEFAULT_ID);
                    $attachment->save();
                }
            }
        }
        return parent::_beforeDelete($object);
    }
}
