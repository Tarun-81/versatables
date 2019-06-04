<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block;

use MageWorx\Downloads\Model\Attachment;

class Links extends \MageWorx\Downloads\Block\AttachmentContainer
{
    /**
     * Prepare URL rewrite editing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('attachment_container.phtml');

        $title = trim($this->getTitle());
        if (empty($title)) {
            $this->setTitle($this->helperData->getFileDownloadsTitle());
        }

        $this->getAttachments();

        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function prepareIds() {
        $id = $this->getId();
        if (empty($id) && $this->getIds()) {
            $id = implode(',', $this->getIds());
        }

        if (empty($id)) {
            return '';
        }

        return explode(',', $id);
    }

    /**
     * @param $ids
     * @return \Magento\Framework\DataObject[]
     */
    public function getAttachmentCollection($ids) {
        $attachmentCollection = $this->attachmentCollectionFactory->create();
        $attachmentCollection
            ->addSortOrder()
            ->addFieldToFilter('is_active', Attachment::STATUS_ENABLED)
            ->addFieldToFilter('section_table.is_active', \MageWorx\Downloads\Model\Section::STATUS_ENABLED);

        $attachmentCollection = $this->addIdFilterToAttachmentCollection($attachmentCollection, $ids);

        $attachmentCollection->addCustomerGroupFilter($this->getCustomerGroupId());

        return $attachmentCollection;
    }

    /**
     * @param $items
     * @param $inGroupIds
     */
    public function prepareAttachments($items, $inGroupIds) {
        foreach ($items as $item) {
            if (!$this->isAllowByCount($item)) {
                continue;
            }

            if ($this->isAllowByCustomerGroup($item, $inGroupIds)) {
                $item->setIsInGroup('1');
            } else {
                $this->isHasNotAllowedLinks = true;
            }
            $this->attachments[] = $item;
        }
    }

    /**
     *
     * @return array
     */
    public function getAttachments()
    {
        if (!$this->attachments) {
            $ids = $this->prepareIds();
            $attachmentCollection = $this->getAttachmentCollection($ids);
            $items = $attachmentCollection->getItems();
            $inGroupIds = $attachmentCollection->getAllIds();
            $this->prepareAttachments($items, $inGroupIds);
        }

        return $this->attachments;
    }

    /**
     * @param $attachmentCollection
     * @param $ids
     * @return mixed
     */
    public function addIdFilterToAttachmentCollection($attachmentCollection, $ids) {
        $attachment = $this->attachmentFactory->create();

        if (strtolower(trim($ids[0])) == 'all') {
            return $attachmentCollection;
        }

        $attachmentCollection->addFieldToFilter($attachment->getIdFieldName(), $ids);
        return $attachmentCollection;
    }
}
