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

namespace Cart2Quote\Quotation\Model\Quote;

/**
 * Quote configuration model
 */
class Config
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\Collection
     */
    protected $collection;

    /**
     * Statuses per state array
     * @var array
     */
    protected $stateStatuses;
    /**
     * @var Status
     */
    protected $quoteStatusFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\CollectionFactory
     */
    protected $quoteStatusCollectionFactory;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;
    /**
     * @var array
     */
    protected $maskStatusesMapping = [
        \Magento\Framework\App\Area::AREA_FRONTEND => [
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OPEN => \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROCESSING,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW => \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROCESSING,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CHANGE_REQUEST => \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROCESSING,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PENDING => \Cart2Quote\Quotation\Model\Quote\Status::STATUS_QUOTE_AVAILABLE,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_SENT => \Cart2Quote\Quotation\Model\Quote\Status::STATUS_QUOTE_AVAILABLE,
        ],
    ];
    /**
     * @var array
     */
    protected $statuses;

    /**
     * @param StatusFactory $quoteStatusFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\CollectionFactory $quoteStatusCollectionFactory
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\StatusFactory $quoteStatusFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\CollectionFactory $quoteStatusCollectionFactory,
        \Magento\Framework\App\State $state
    ) {
        $this->quoteStatusFactory = $quoteStatusFactory;
        $this->quoteStatusCollectionFactory = $quoteStatusCollectionFactory;
        $this->state = $state;
    }

    /**
     * Retrieve default status for state
     * @param   string $state
     * @return  string
     */
    public function getStateDefaultStatus($state)
    {
        $status = false;
        $stateNode = $this->_getState($state);
        if ($stateNode) {
            $status = $this->quoteStatusFactory->create()->loadDefaultByState($state);
            $status = $status->getStatus();
        }

        return $status;
    }

    /**
     * @param string $state
     * @return Status|null
     */
    protected function _getState($state)
    {
        foreach ($this->_getCollection() as $item) {
            if ($item->getData('state') == $state) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\Collection
     */
    protected function _getCollection()
    {
        if ($this->collection == null) {
            $this->collection = $this->quoteStatusCollectionFactory->create()->joinStates();
        }

        return $this->collection;
    }

    /**
     * Retrieve status label
     * @param   string $code
     * @return  string
     */
    public function getStatusLabel($code)
    {
        $code = $this->maskStatusForArea($this->state->getAreaCode(), $code);
        $status = $this->quoteStatusFactory->create()->load($code);

        return $status->getStoreLabel();
    }

    /**
     * Mask status for quote for specified area
     * @param string $area
     * @param string $code
     * @return string
     */
    protected function maskStatusForArea($area, $code)
    {
        if (isset($this->maskStatusesMapping[$area][$code])) {
            return $this->maskStatusesMapping[$area][$code];
        }

        return $code;
    }

    /**
     * State label getter
     * @param   string $state
     * @return \Magento\Framework\Phrase|string
     */
    public function getStateLabel($state)
    {
        if ($stateItem = $this->_getState($state)) {
            $label = $stateItem->getData('label');

            return __($label);
        }

        return $state;
    }

    /**
     * Retrieve all statuses
     * @return array
     */
    public function getStatuses()
    {
        $statuses = $this->quoteStatusCollectionFactory->create()->toOptionHash();

        return $statuses;
    }

    /**
     * Get existing quote statuses
     * Visible or invisible on frontend according to passed param
     * @param bool $visibility
     * @return array
     */
    protected function _getStatuses($visibility)
    {
        if ($this->statuses == null) {
            //define arrays
            $this->statuses[(bool)true] = [];
            $this->statuses[(bool)false] = [];

            foreach ($this->_getCollection() as $item) {
                $visible = $item->getData('visible_on_front');
                $this->statuses[(bool)$visible][] = $item->getData('status');
            }
        }

        return $this->statuses[(bool)$visibility];
    }

    /**
     * Quote states getter
     * @return array
     */
    public function getStates()
    {
        $states = [];
        foreach ($this->_getCollection() as $item) {
            if ($item->getState()) {
                $states[$item->getState()] = __($item->getData('label'));
            }
        }

        return $states;
    }

    /**
     * Retrieve statuses available for state
     * Get all possible statuses, or for specified state, or specified states array
     * Add labels by default. Return plain array of statuses, if no labels.
     * @param mixed $state
     * @param bool $addLabels
     * @return array
     */
    public function getStateStatuses($state, $addLabels = true)
    {
        $key = md5(serialize([$state, $addLabels]));
        if (isset($this->stateStatuses[$key])) {
            return $this->stateStatuses[$key];
        }
        $statuses = [];

        if (!is_array($state)) {
            $state = [$state];
        }
        foreach ($state as $_state) {
            $stateNode = $this->_getState($_state);
            if ($stateNode) {
                $collection = $this->quoteStatusCollectionFactory->create()->addStateFilter($_state)->orderByLabel();
                foreach ($collection as $item) {
                    $status = $item->getData('status');
                    if ($addLabels) {
                        $statuses[$status] = $item->getStoreLabel();
                    } else {
                        $statuses[] = $status;
                    }
                }
            }
        }
        $this->stateStatuses[$key] = $statuses;

        return $statuses;
    }

    /**
     * Retrieve states which are visible on front end
     * @return array
     */
    public function getVisibleOnFrontStatuses()
    {
        return $this->_getStatuses(true);
    }

    /**
     * Get quote statuses, invisible on frontend
     * @return array
     */
    public function getInvisibleOnFrontStatuses()
    {
        return $this->_getStatuses(false);
    }
}
