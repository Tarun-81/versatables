<?php
/**
 *
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2017] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 */

namespace Cart2Quote\AutoProposal\Block\Adminhtml\System\Config;

/**
 * Class AutoProposalRanges
 *
 * @package Cart2Quote\AutoProposal\Block\Adminhtml\System\Config
 */
class AutoProposalRanges extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var array
     */
    protected $renderers;

    /**
     *
     */
    protected function _prepareToRender()
    {
        $columns = [
            \Cart2Quote\AutoProposal\Api\Data\RangeInterface::MIN_VALUE_IDENTIFIER => [
                'label' => __('Minimum value'),
                'class' => 'validate-number validate-zero-or-greater',
                'renderer' => [
                    'type' => 'Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field\FloatInput',
                    'data' => []
                ],
            ],
            \Cart2Quote\AutoProposal\Api\Data\RangeInterface::MAX_VALUE_IDENTIFIER => [
                'label' => __('Maximum value'),
                'class' => 'validate-number validate-zero-or-greater',
                'renderer' => [
                    'type' => 'Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field\FloatInput',
                    'data' => []
                ]
            ],
            \Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISCOUNT_IDENTIFIER => [
                'label' => __('Discount (%)'),
                'class' => 'validate-number validate-zero-or-greater',
                'renderer' => [
                    'type' => 'Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field\FloatInput',
                    'data' => []
                ]
            ],
            \Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISABLE_AUTOPROPOSAL_IDENTIFIER => [
                'label' => __('Disable auto proposal'),
                'renderer' => [
                    'type' => 'Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field\Checkbox',
                    'data' => [
                        'onclick' => 'toggleValueElements(this, Element.previous(this.parentNode));',
                    ]
                ]
            ],
            \Cart2Quote\AutoProposal\Api\Data\RangeInterface::SHIPPING_AMOUNT_IDENTIFIER => [
                'label' => __('Shipping amount'),
                'class' => 'validate-number validate-zero-or-greater',
                'renderer' => [
                    'type' => 'Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field\FloatInput',
                    'data' => [
                        'disabled' => true
                    ]
                ]
            ],
            \Cart2Quote\AutoProposal\Api\Data\RangeInterface::ENABLE_SHIPPING_IDENTIFIER => [
                'label' => __('Enable shipping amount'),
                'renderer' => [
                    'type' => 'Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field\Checkbox',
                    'data' => [
                        'onclick' => 'toggleValueElements(this, Element.previous(this.parentNode),[], !this.checked);'
                    ]
                ]
            ],
            \Cart2Quote\AutoProposal\Api\Data\RangeInterface::NOTIFY_SALESREP_IDENTIFIER => [
                'label' => __('Notify salesrep'),
                'renderer' => [
                    'type' => 'Cart2Quote\AutoProposal\Block\Adminhtml\Form\Field\Checkbox',
                    'data' => []
                ]
            ]
        ];

        foreach ($columns as $columnName => $columnData) {
            $this->addColumn($columnName, $columnData);
        }

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add range');
    }

    /**
     * @param string $name
     * @param array $params
     */
    public function addColumn($name, $params)
    {
        if (is_array($params['renderer'])) {
            $params['renderer'] = $this->addRenderer(
                $name,
                $params['renderer']['type'],
                ['data' => $params['renderer']['data']]
            );
        }
        parent::addColumn($name, $params);
    }

    /**
     * @param        $name
     * @param string $type
     * @param array $data
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function addRenderer($name, $type, $data = [])
    {
        if (!isset($this->renderers[$name])) {
            $this->renderers[$name] = $this->getLayout()->createBlock($type, '', $data);
            $this->renderers[$name]->setClass($name);
        }

        return $this->renderers[$name];
    }
}
