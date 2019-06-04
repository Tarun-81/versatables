<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Attachment\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * @method Tabs setTitle(\string $title)
 */
class Tabs extends WidgetTabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('attachment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attachment Information'));
    }
}
