<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use MageWorx\Downloads\Model\Attachment;

class MassDisable extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 attachments have been disabled';
    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while disabling attachments.';
    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * @param Attachment $attachment
     * @return $this
     */
    protected function doTheAction(Attachment $attachment)
    {
        $attachment->setIsActive($this->isActive);
        $attachment->getResource()->save($attachment);
        return $this;
    }
}
