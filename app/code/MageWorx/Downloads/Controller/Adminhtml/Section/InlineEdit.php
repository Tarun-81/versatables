<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use Magento\Backend\App\Action\Context;
use MageWorx\Downloads\Model\SectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\Downloads\Controller\Adminhtml\Section as SectionController;
use Magento\Framework\Registry;
use MageWorx\Downloads\Model\Section;

class InlineEdit extends SectionController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     *
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param SectionFactory $sectionFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Registry $registry,
        SectionFactory $sectionFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $sectionFactory, $context);

    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the sent data.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $sectionId) {
            /** @var \MageWorx\Downloads\Model\Section $section */
            //$section = $this->sectionFactory->create()->load($sectionId);
            $section = $this->sectionFactory->create();
            $section->getResource()->load($section, $sectionId);
            try {
                $sectionData = $this->filterData($postItems[$sectionId]);
                $section->addData($sectionData);
                $section->getResource()->save($section);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithSectionId($section, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithSectionId($section, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithSectionId(
                    $section,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add section id to error message
     *
     * @param Section $section
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithSectionId(Section $section, $errorText)
    {
        return '[Section ID: ' . $section->getId() . '] ' . $errorText;
    }
}
