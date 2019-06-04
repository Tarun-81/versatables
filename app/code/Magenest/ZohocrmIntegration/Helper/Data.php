<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 02/04/2018
 * Time: 11:44
 */

namespace Magenest\ZohocrmIntegration\Helper;

use Magento\Framework\App\ObjectManager;
use Magenest\ZohocrmIntegration\Model\Connector;
use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Model\Session as CustomerSession;
use \Magento\Framework\Stdlib\DateTime\DateTime;


class Data
{
    /**
     * Session Admin
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * Session Customer
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Connector
     *
     * @var \Magenest\ZohocrmIntegration\Model\Connector;
     */
    protected $connector;

    /**
     * Core Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    const COUNTRY_LIST = array(
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas the',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island (Bouvetoya)',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory (Chagos Archipelago)',
        'VG' => 'British Virgin Islands',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros the',
        'CD' => 'Congo',
        'CG' => 'Congo the',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote d\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FO' => 'Faroe Islands',
        'FK' => 'Falkland Islands (Malvinas)',
        'FJ' => 'Fiji the Fiji Islands',
        'FI' => 'Finland',
        'FR' => 'France, French Republic',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia the',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island and McDonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KP' => 'Korea',
        'KR' => 'Korea',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyz Republic',
        'LA' => 'Lao',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'AN' => 'Netherlands Antilles',
        'NL' => 'Netherlands the',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn Islands',
        'PL' => 'Poland',
        'PT' => 'Portugal, Portuguese Republic',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts and Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent and the Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia (Slovak Republic)',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia, Somali Republic',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard & Jan Mayen Islands',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland, Swiss Confederation',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States of America',
        'UM' => 'United States Minor Outlying Islands',
        'VI' => 'United States Virgin Islands',
        'UY' => 'Uruguay, Eastern Republic of',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Vietnam',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe'
    );


    /**
     * @param Connector $connector
     * @param Session $backendAuthSession
     * @param CustomerSession $customerSession
     */

    public function __construct(
        Connector $connector,
        DateTime $coreDate,
        Session $backendAuthSession,
        CustomerSession $customerSession
    )
    {
        $this->_coreDate = $coreDate;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_customerSession = $customerSession;
        $this->connector = $connector;

    }

    public static function getCountryByCode($code)
    {
        $code = strtoupper($code);
        if (array_key_exists($code, self::COUNTRY_LIST)) {
            return self::COUNTRY_LIST[$code];
        } else {
            return $code;
        }
    }

    /**
     * @param \Magento\Customer\Model\Customer $customerObj
     */
    public static function getCustomerPhone($customerObj)
    {
        if (isset($customerObj['billing_telephone'])) {
            return $customerObj['billing_telephone'];
        }
        if (isset($customerObj['shipping_telephone'])) {
            return $customerObj['shipping_telephone'];
        }
        return "";
    }

    /**
     * @param \Magento\Customer\Model\Customer $customerObj
     */
    public static function getCustomerFax($customerObj)
    {
        if (isset($customerObj['billing_fax'])) {
            return $customerObj['billing_fax'];
        }
        if (isset($customerObj['shipping_fax'])) {
            return $customerObj['shipping_fax'];
        }
        return "";
    }

    public function getInsertData($ids, $type)
    {
        $dataReturn = [];
        foreach ($ids as $id) {
            $dataReturn[] = [
                'type' => $type,
                'entity_id' => $id,
                'status' => 'pending',
                'enqueue_time' => date("Y-m-d H:i:s"),
                'priority' => 1,
            ];
        }
        return $dataReturn;
    }

    public function processInsertProductResponse($response, $productIds)
    {
        $dataInsert = [];
        $err = 0;
        $succ = 0;
        if (isset($response['result']['row']) && (is_array($response['result']['row'])) && (is_array($productIds))) {
            foreach ($response['result']['row'] as $k => $value) {
                if (isset($value['success'])) {
                    $zohoProductId = isset($value['success']['details']['FL']['0']) ? $value['success']['details']['FL']['0'] : "";
                    $magentoProductId = isset($productIds[$k]) ? $productIds[$k] : "";
                    if ($zohoProductId && $magentoProductId) {
                        $dataInsert[] = [
                            'product_id' => $magentoProductId,
                            'zoho_product_id' => $zohoProductId
                        ];
                    }
                    $succ++;
                } else {
                    $err++;
                }

            }
            $objectManager = ObjectManager::getInstance();
            /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\ProductLink\Collection $productLinkCollection */
            $productLinkCollection = $objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\ProductLink\Collection");
            $connection = $productLinkCollection->getResource()->getConnection();
            if (count($dataInsert) > 0) {
                $connection->insertOnDuplicate(
                    $productLinkCollection->getResource()->getTable('magenest_zohocrm_product_link'),
                    $dataInsert,
                    ['zoho_product_id']
                );
            }
        }
        return false;
    }

    public function processInsertEntityResponse($response, $ids, $type, $syncAuto = null)
    {
        $dataInsert = [];
        $dataReport = [];
        $oldKeys = [];
        $err = 0;
        $succ = 0;
        $datetime = $this->_coreDate->gmtDate();
        $admin_user = $this->_backendAuthSession->getUser();
        $current_user = $this->_customerSession->getCustomer();
        if ($admin_user) {
            $name = $admin_user->getName();
            $email = $admin_user->getEmail();
        } elseif ($current_user->getName()) {
            $name = $current_user->getName();
            $email = $current_user->getEmail();
        } else {
            $name = "Guest";
            $email = '';
        }
        if (isset($response['data']) && (is_array($response['data'])) && (is_array($ids))) {
            foreach ($response['data'] as $k => $value) {
                if (($value['code']) == 'SUCCESS') {
                    $zohoProductId = isset($value['details']['id']) ? $value['details']['id'] : "";
                    $magentoProductId = isset($ids[$k]) ? $ids[$k] : "";
                    if ($zohoProductId && $magentoProductId) {
                        $dataInsert[] = [
                            'entity_id' => $magentoProductId,
                            'zoho_entity_id' => $zohoProductId,
                            'type' => $type
                        ];
                        $action = $value['message'];

                        $dataReport[] = [
                            'id_magento' => $magentoProductId,
                            'record_id' => $zohoProductId,
                            'zohocrm_table' => $type,
                            'action' => $action,
                            'datetime' => $datetime,
                            'username' => $name,
                            'email' => $email,
                            'status' => 1
                        ];
                    }
                    $succ++;
                }  else {
                    $err++;
                    $oldKeys[] = $ids[$k];
                    $dataReport[] = [
                        'id_magento' => $ids[$k],
                        'record_id' => null,
                        'zohocrm_table' => $type,
                        'action' => $value['message'],
                        'datetime' => $datetime,
                        'username' => $name,
                        'email' => $email,
                        'status' => 0
                    ];
                }

            }

            $objectManager = ObjectManager::getInstance();
            /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\ProductLink\Collection $productLinkCollection */
            $productLinkCollection = $objectManager->get("Magenest\ZohocrmIntegration\Model\ResourceModel\ProductLink\Collection");
            $connection = $productLinkCollection->getResource()->getConnection();
            if (count($dataInsert) > 0) {
                $connection->delete(
                    $productLinkCollection->getResource()->getTable('magenest_zohocrm_link_entity'),
                    ['entity_id IN (?)' => $oldKeys, 'type = ? ' => $type]
                );

                $connection->insertOnDuplicate(
                    $productLinkCollection->getResource()->getTable('magenest_zohocrm_link_entity'),
                    $dataInsert,
                    ['zoho_entity_id', 'type']
                );
            }
            if(!$syncAuto){
                $this->saveMultiReport($dataReport);
            }
        } else {
            $oldKeys = $ids;
        }
        return $oldKeys;
    }

    /**
     * @return Session
     */
    public function getActionReport($code)
    {
        if ($code == 2000)
            $action = 'Record Added';
        else if ($code == 2001)
            $action = 'Record Updated';
        else if ($code == 2002)
            $action = 'Record Already Exists';
        return $action;
    }


    public function saveMultiReport($dataReport)
    {
        if ($this->connector->getReportConfig()) {
            $objectManager = ObjectManager::getInstance();
            /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Report\Collection $reportCollection */
            $reportCollection = $objectManager->get("Magenest\ZohocrmIntegration\Model\ResourceModel\Report\Collection");
            $connection = $reportCollection->getResource()->getConnection();
            if (count($dataReport) > 0) {
                $connection->insertMultiple(
                    $reportCollection->getResource()->getTable('magenest_zohocrm_report'),
                    $dataReport
                );
            }
        }
    }

    public static function getStateByNum($state)
    {
        if ($state == 1) {
            return "Created";
        }
        if ($state == 2) {
            return "Approved";
        }
        if ($state == 3) {
            return "Cancelled";
        }
        return $state;
    }

    public static function dateConverter($date, $format = null, $newFormat)
    {
        if ($format) {
            $dateObj = date_create_from_format($format, $date);
        } else {
            $dateObj = date_create($date);
        }
        if ($dateObj) {
            return $dateObj->format($newFormat);
        } else {
            return $date;
        }
    }


}
