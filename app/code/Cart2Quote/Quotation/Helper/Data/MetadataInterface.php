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

namespace Cart2Quote\Quotation\Helper\Data;

/**
 * Interface MetadataInterface
 * @package Cart2Quote\Quotation\Helper\Data
 */
interface MetadataInterface
{
    /**
     * @return string
     */
    public function getPhpVersion();

    /**
     * @return string
     */
    public function getMagentoVersion();

    /**
     * @return string
     */
    public function getMagentoEdition();

    /**
     * @return bool|string
     */
    public function getIoncubeVersion();

    /**
     * This function gets the ionCube version from the integer version string
     * It also has a fallback for ionCube < v3.1
     *
     * @return string
     */
    public function getIoncubeLoaderVersion();

    /**
     * @return bool|string
     */
    public function getCart2QuoteVersion();

    /**
     * @return bool|string
     */
    public function getNot2OrderVersion();

    /**
     * @return bool|string
     */
    public function getSupportDeskVersion();

    /**
     * @return bool|string
     */
    public function getDeskEmailVersion();

    /**
     * Get the Module version and return unknown if not found
     *
     * @param $moduleName
     * @return bool|string
     */
    public function getModuleVersion($moduleName);
}
