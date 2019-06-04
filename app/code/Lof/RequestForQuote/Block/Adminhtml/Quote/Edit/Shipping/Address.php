<?php

namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Edit\Shipping;

class Address extends \Magento\Backend\Block\Template
{
    /**
     * @var null
     */
    protected $_quote_address = null;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrderAddress
     */
    protected $quoteToOrderAddressConverter;

    /**
     * Address constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteToOrderAddressConverter
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteToOrderAddressConverter,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->addressRenderer = $addressRenderer;
        $this->quoteToOrderAddressConverter = $quoteToOrderAddressConverter;
    }

    /**
     * @return mixed|null
     */
    public function getMageQuoteAddress()
    {
        if (!$this->_quote_address) {
            $mage_quote = $this->getMageQuote();
            $addresses = $mage_quote->getAddressesCollection();
            foreach ($addresses as $address) {
                $address_type = $address->getAddressType();
                if ($address_type == "shipping") {
                    $this->_quote_address = $address;
                    break;
                }
            }
        }

        return $this->_quote_address;
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getMageQuote()
    {
        return $this->_coreRegistry->registry('mage_quote');
    }

    /**
     * @param $address
     * @return null|string
     * @throws \Exception
     */
    public function getFormattedAddress($address) {
        if ($address instanceof \Magento\Quote\Model\Quote\Address) {
            $address = $this->quoteToOrderAddressConverter->convert($address);
        }

        if (!$address instanceof \Magento\Sales\Model\Order\Address) {
            throw new \Exception(__('Expected instance of \Magento\Sales\Model\Order\Address, got ' . get_class($address)));
        }

        return $this->addressRenderer->format($address, 'html');
    }
}