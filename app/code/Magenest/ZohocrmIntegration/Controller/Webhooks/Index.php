<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 14/11/2016
 * Time: 14:39
 */

namespace Magenest\ZohocrmIntegration\Controller\Webhooks;



class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $productLinkFactory;
    protected $_customerFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magenest\ZohocrmIntegration\Model\ProductLinkFactory $productLinkFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory

    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->productLinkFactory = $productLinkFactory;
        $this->_customerFactory = $customerFactory;
        parent::__construct($context);
    }

    public function execute()
    {

        $data = $this->getRequest()->getParams();

        $params = json_decode($data['params'], true);
        $userData = array("username" => $data['username'], "password" => $data['password']);
        $urlBase = $this->_url->getBaseUrl();
        $ch = curl_init($urlBase."index.php/rest/V1/integration/admin/token");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));
        $token = curl_exec($ch);


        $linkCol = $this->productLinkFactory->create()
            ->getCollection()
            ->addFieldToFilter("zoho_entity_id",  $params['id'])
            ->addFieldToFilter("type", "Contacts")
            ->getFirstItem();
        $customerId = $linkCol->getEntityId();
        $customer = $this->_customerFactory->create()->load($customerId);
        //billing
        $billingAddressId = $customer->getDefaultBilling();
        //shipping
        $shippingAddressId = $customer->getDefaultShipping();

        if ($customerId) {
            $customerData = [
                'customer' => [
                    'id' => $customerId,
                    "email" => $params['email'],
                    "firstname" => $params['firstname'],
                    "lastname" => $params['lastname'],
                    "websiteId" => $customer->getWebsiteId(),
                    "dob" => $params['dob'],
                    "custom_attributes" => [
                        "attribute_code" => "magenest_company",
                        "value" => $params['account_name']
                    ],
                    "addresses" => [
                        0 => [
                            "id" => $billingAddressId,
                            "firstname" =>  $params['firstname'],
                            "lastname" => $params['lastname'],
                            "street" => [
                                $params['billing_street']
                            ],
                            "city" => $params['billing_city'],
                            "postcode" => $params['billing_code'],
                            "region" => $params['billing_state'],
                            "telephone" => $params['phone'],
                            "fax" => $params['fax']
                        ],
                        1 => [
                            "id" => $shippingAddressId,
                            "firstname" =>  $params['firstname'],
                            "lastname" => $params['lastname'],
                            "street" => [
                                $params['shipping_street']
                            ],
                            "city" => $params['shipping_city'],
                            "postcode" => $params['shipping_code'],
                            "region" => $params['shipping_state'],
                            "telephone" => $params['phone'],
                            "fax" => $params['fax']
                        ]
                    ]

                ],
            ];
            $ch = curl_init($urlBase."index.php/rest/V1/customers/" . $customerId);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customerData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
            curl_exec($ch);
        }
    }
}
