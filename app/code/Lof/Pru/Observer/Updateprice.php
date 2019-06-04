<?php
namespace Lof\Pru\Observer;

class Updateprice implements \Magento\Framework\Event\ObserverInterface
{
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
		$item = $observer->getEvent()->getData('quote_item');         
		$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
		$price = 10; //set your price here
		$item->setCustomPrice($price);
		$item->setOriginalCustomPrice($price);
		$item->getProduct()->setIsSuperMode(true);

     return $this;
  }
}