<?php
namespace Custom\Shippingamount\Model;
use \Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use \Magento\Quote\Model\Quote;
use \Magento\Quote\Api\Data\ShippingAssignmentInterface;
use \Magento\Quote\Model\Quote\Address\Total;
use \Magento\Checkout\Model\Cart;


class AddShippingPrice extends AbstractTotal
{

    protected  $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     *
     * @param Quote                       $quote              quote
     * @param ShippingAssignmentInterface $shippingAssignment shipping
     * @param Total                       $total              total
     *
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        /**
         * you can check your conditions
         * here or add some logic when to
         * make the shipping amount to 0
         */


        $subTotal = $this->cart->getQuote()->getSubtotal();

       if($subTotal <= 300) {
		    $percentage = 7.5;
			$shippingamount = ($subTotal*$percentage) / 100;
			if (!(is_int($shippingamount))) {
				$shippingamount = number_format((float)$shippingamount, 2, '.', '');
			}
		    $amount = $quote->getShippingAddress()->getShippingAmount();
			//$total->setShippingAmount($shippingamount);
			//$total->setShippingAmount(0);
			//$deduct_amount = 25 - $shippingamount;
			//$deduct_amount = 25;
			//$total->addTotalAmount($this->getCode(), -$deduct_amount);
			//$total->addBaseTotalAmount($this->getCode(), -$deduct_amount);
			
	   }	   
        return $this;
    }

    /**
     * This function will fetch the quote details
     *
     * @param Quote $quote quote
     * @param Total $total total
     *
     * @return array|null
     */
    public function fetch(Quote $quote, Total $total)
    {
        $result = null;
        $amount = $total->getDiscountAmount();
        if ($amount != 0) {
            $description = $total->getDiscountDescription();
            $result = [
                'code' => $this->getCode(),
                'title' => $description,
                'value' => $amount
            ];
        }
        return $result;
    }
}