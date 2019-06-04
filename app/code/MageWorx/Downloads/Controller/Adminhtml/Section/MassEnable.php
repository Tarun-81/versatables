<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Section;

class MassEnable extends MassDisable
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 sections have been enabled';

    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while enabling sections.';
    
    /**
     * @var bool
     */
    protected $isActive = true;
}
