<?php

namespace Lof\RequestForQuote\Block\Wishlist\Item\Column;

class Quote extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
{

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $httpContext, $data);
    }

    public function getAddToQuoteQty(\Magento\Wishlist\Model\Item $item)
    {
        $qty = $item->getQty();
        return $qty ? $qty : 1;
    }

    public function getProductItem()
    {
        return $this->getItem()->getProduct();
    }

    public function getItemAddToQuoteParams($item)
    {
        $addToQuoteDataObject = json_decode($this->_getHelper()->getAddToCartParams($item));
        $addToQuoteDataObject->action = str_replace('wishlist/index/cart', 'quotation/wishlist/toquote', $addToQuoteDataObject->action);
        return json_encode($addToQuoteDataObject);
    }
}
