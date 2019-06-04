<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Attachment\Helper;

use Magento\Framework\Data\Form\Element\File as FileField;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\Data\Form\Element\CollectionFactory as ElementCollectionFactory;
use Magento\Framework\Escaper;
use MageWorx\Downloads\Model\Attachment\Link as FileLinkModel;

/**
 * @method string getValue()
 * @method bool getDisabled()
 * @method File setExtType(\string $extType)
 */
class File extends FileField
{
    /**
     *
     * @var FileLinkModel
     */
    protected $fileLinkModel;

    /**
     *
     * @param FileLinkModel $fileLinkModel
     * @param ElementFactory $factoryElement
     * @param ElementCollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        FileLinkModel $fileLinkModel,
        ElementFactory $factoryElement,
        ElementCollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = []
    ) {

        $this->fileLinkModel = $fileLinkModel;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('file');
        $this->setExtType('file');
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $this->addClass('input-file');
        $html .= parent::getElementHtml();

        if ($this->getValue()) {
            $url = $this->_getUrl();
            if (!preg_match("/^http\:\/\/|https\:\/\//", $url)) {
                $url = $this->fileLinkModel->getBaseUrl() . $url;
            }
            $html .= '<br /><a href="' . $url . '" target="_blank">' . $this->_getUrl() . '</a> ';
        }
        return $html;
    }

    /**
     * @return string
     */
    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="'.parent::getName().'[value]" value="'.$this->getValue() . '" />';
    }

    /**
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->getData('name');
    }
}
