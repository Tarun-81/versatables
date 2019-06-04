<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use MageWorx\Downloads\Model\Section;

class MassDisable extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 sections have been disabled';

    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while disabling sections.';
    
    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * @param Section $section
     * @return $this
     */
    protected function doTheAction(Section $section)
    {
        $section->setIsActive($this->isActive);
        $section->getResource()->save($section);
        return $this;
    }
}
