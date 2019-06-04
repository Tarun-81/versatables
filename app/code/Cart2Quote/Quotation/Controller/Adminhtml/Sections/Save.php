<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Sections;

/**
 * Class Save
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Sections
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section
     */
    private $sectionResourceModel;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\SectionFactory
     */
    private $sectionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Section\Provider
     */
    private $provider;

    /**
     * Save constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel
     * @param \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Section\Provider $provider
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel,
        \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory,
        \Cart2Quote\Quotation\Model\Quote\Section\Provider $provider,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->sectionResourceModel = $sectionResourceModel;
        $this->sectionFactory = $sectionFactory;
        $this->provider = $provider;
    }

    /**
     *
     */
    public function execute()
    {
        $sections = $this->getRequest()->getParam('sections');
        foreach ($sections as $sectionData) {
            $section = $this->sectionFactory->create();
            $section->setData(array_filter($sectionData));
            $section->isDeleted($section->getIsDeleted());
            $this->sectionResourceModel->save($section);
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}