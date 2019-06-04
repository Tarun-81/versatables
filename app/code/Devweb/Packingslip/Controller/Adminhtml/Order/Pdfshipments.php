<?php

namespace Devweb\Packingslip\Controller\Adminhtml\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\Order\Pdf\Shipment;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\Order\ShipmentDocumentFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdfshipments extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @var ShipmentDocumentFactory
     */
    private $documentFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Shipment $shipment
     * @param ShipmentDocumentFactory $documentFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Shipment $shipment,
        ShipmentDocumentFactory $documentFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfShipment = $shipment;
        $this->collectionFactory = $collectionFactory;
        $this->documentFactory = $documentFactory;
        \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction::__construct($context, $filter);
    }

    /**
     * Print shipments for selected orders
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|\Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $shipments = [];
        foreach ($collection as $order) {
            $shipment = $this->documentFactory->create($order);
            if (!$shipment->getAllItems()) {
                continue;
            }
            $shipment->setIncrementId($order->getRealOrderId());
            $shipments[] = $shipment;
        }
        if (!$shipments) {
            $this->messageManager->addError(__('There are no printable documents related to selected orders.'));
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        return $this->fileFactory->create(
            sprintf('pickingslip%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
            $this->pdfShipment->getPdf($shipments)->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
