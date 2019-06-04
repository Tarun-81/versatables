<?php
/**
 *
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2017] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 */

namespace Cart2Quote\AutoProposal\Setup;

/**
 * Class Uninstall
 *
 * @package Cart2Quote\AutoProposal\Setup
 */
class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $connection = $setup->getConnection();

        $quotationQuoteTable = $setup->getTable('quotation_quote');

        $columns = [
            \Cart2Quote\AutoProposal\Api\Data\AutoProposalInterface::SEND_NOTIFY_SALESREP_EMAIL,
            \Cart2Quote\AutoProposal\Api\Data\AutoProposalInterface::NOTIFY_SALESREP_EMAIL_SENT
        ];
        foreach ($columns as $column) {
            if ($connection->tableColumnExists($quotationQuoteTable, $column)) {
                $connection->dropColumn($quotationQuoteTable, $column);
            }
        }

        $setup->endSetup();
    }
}
