<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Section;

use MageWorx\Downloads\Model\Attachment;

class Links extends \MageWorx\Downloads\Block\Links
{

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

        $attachmentCollection->addFieldToFilter('section_id', ['in' => $ids]);
        return $attachmentCollection;
    }
}
