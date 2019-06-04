<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Ui\DataProvider\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory;
use MageWorx\Downloads\Model\ResourceModel\Attachment\Collection;
use MageWorx\Downloads\Model\Attachment;
use MageWorx\Downloads\Model\ResourceModel\Attachment as AttachmentResource;

/**
 * Class AttachmentDataProvider
 *
 * @method Collection getCollection
 */
class AttachmentDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var AttachmentResource
     */
    protected $attachmentResource;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * AttachmentExcludeDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param AttachmentResource $attachmentResource
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        AttachmentResource $attachmentResource,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->attachmentResource = $attachmentResource;
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $productId = $this->request->getParam('current_product_id', 0);
        $attachmentIds = $this->attachmentResource->getAttachmentIdsByProductId($productId);

        if ($attachmentIds) {
            $this->getCollection()->addExcludeAttachmentFilter($attachmentIds);
        }

        $items = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $items['items'][] = $item->toArray([]);
        }

        return $items;
    }
}
