<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\RequestForQuote\Helper;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Escaper;

class Wysiwygeditor extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\Data\Form\Element\CollectionFactory
     */
    protected $_factoryCollection;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_factoryElement;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData = null;

    protected $element_id = "";

    protected $_wysiwygConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Backend\Helper\Data $backendData
        ) {
        parent::__construct($context);
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_layout = $layoutFactory;
        $this->_backendData = $backendData;
    }

    public function isBase64Encoded($data) {
        if(base64_encode($data) === $data) return false;
        if(base64_encode(base64_decode($data)) === $data){
            return true;
        }
        if (!preg_match('~[^0-9a-zA-Z+/=]~', $data)) {
            $check = str_split(base64_decode($data));
            $x = 0;
            foreach ($check as $char) if (ord($char) > 126) $x++;
            if ($x/count($check)*100 < 30) return true;
        }
        $decoded = base64_decode($data);
        // Check if there are valid base64 characters
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $data)) return false;
        // if string returned contains not printable chars
        if (0 < preg_match('/((?![[:graph:]])(?!\s)(?!\p{L}))./', $decoded, $matched)) return false;
        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) return false;

        return false;
    }
    public function render($element, $is_required=false){
        $field_id = isset($element['field_id'])?$element['field_id']:'';
        $element_html_id = isset($element['html_id'])?$element['html_id']:'';
        $element_name = isset($element['name'])?$element['name']:'';
        $label = isset($element['label'])?$element['label']:'';
        $value = isset($element['value'])?$element['value']:'';
        $class = isset($element['class'])?$element['class']:'';

        $html = '';
        $config = $this->_wysiwygConfig->getConfig();

        $element_id = $element_html_id.rand().time();
        $this->element_id = $element_id;

        $config['height'] = '300px';
        $config = json_encode($config->getData());

        /*
        if(!is_array($value)){
            $value = str_replace(" ","+", $value);
            if($this->isBase64Encoded($value)){
                $value = base64_decode($value);
                
                if($this->isBase64Encoded($value)){
                    $value = base64_decode($value);
                }
            }elseif(base64_encode(base64_decode($value)) === $value){
                $value = base64_decode($value);
            }
        }*/

        #return $value;

        if($is_required){
            $class .= ' required-entry';
        }

        $html .= '<div class="admin__field field field-options_'.$field_id.'  with-note">';
        $html .= $label;

        $html .= '<div class="admin__field-control control">';
        $html .= '<textarea id="' . $element_id . '" name="' . $element_name . '" class="textarea admin__control-textarea wysiwyg-editor ' . $class . '" rows="5" cols="15" data-ui-id="product-tabs-attributes-tab-fieldset-element-textarea-' . $element_name . '" aria-hidden="true">'.$this->getEscapedValue($value).'</textarea>';

        $html .= $this->_getToggleButtonHtml(true);

        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Return custom button HTML
     *
     * @param array $data Button params
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getButtonHtml($data)
    {
        $html = '<button type="button"';
        $html .= ' class="scalable ' . (isset($data['class']) ? $data['class'] : '') . '"';
        $html .= isset($data['onclick']) ? ' onclick="' . $data['onclick'] . '"' : '';
        $html .= isset($data['style']) ? ' style="' . $data['style'] . '"' : '';
        $html .= isset($data['id']) ? ' id="' . $data['id'] . '"' : '';
        $html .= '>';
        $html .= isset($data['title']) ? '<span><span><span>' . $data['title'] . '</span></span></span>' : '';
        $html .= '</button>';

        return $html;
    }
     /**
     * Return HTML button to toggling WYSIWYG
     *
     * @param bool $visible
     * @return string
     */
    protected function _getToggleButtonHtml($visible = true)
    {
        $html = $this->_getButtonHtml(
            [
                'title' => $this->translate('Show / Hide Editor'),
                'class' => 'action-show-hide',
                'style' => $visible ? '' : 'display:none',
                'id' => 'toggle' . $this->getHtmlId(),
            ]
        );
        return $html;
    }
    /**
     * Translate string using defined helper
     *
     * @param string $string String to be translated
     * @return \Magento\Framework\Phrase
     */
    public function translate($string)
    {
        return (string)new \Magento\Framework\Phrase($string);
    }

    public function getHtmlId(){
        return $this->element_id;
    }

    /**
     * Escape a string's contents.
     *
     * @param string $string
     * @return string
     */
    protected function _escape($string)
    {
        return htmlspecialchars($string, ENT_COMPAT);
    }

    /**
     * Return the escaped value of the element specified by the given index.
     *
     * @param null|int|string $index
     * @return string
     */
    public function getEscapedValue($value = null)
    {
        return $this->_escape($value);
    }
}