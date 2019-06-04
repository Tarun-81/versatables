<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use MageWorx\Downloads\Model\Attachment;

class MassDelete extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 record(s) have been deleted';
    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while deleting record(s).';

    /**
     * @param $attachment
     * @return $this
     */
    protected function doTheAction(Attachment $attachment)
    {
        $attachment->getResource()->delete($attachment);
        return $this;
    }
}
