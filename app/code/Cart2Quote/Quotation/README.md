# Cart2Quote Quotation Encoded

## LICENSE

CART2QUOTE CONFIDENTIAL

[2009] - [2017] Cart2Quote B.V.
All Rights Reserved.
https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)

NOTICE OF LICENSE

All information contained herein is, and remains
the property of Cart2Quote B.V. and its suppliers,
if any.  The intellectual and technical concepts contained
herein are proprietary to Cart2Quote B.V.
and its suppliers and may be covered by European and Foreign Patents,
patents in process, and are protected by trade secret or copyright law.
Dissemination of this information or reproduction of this material
is strictly forbidden unless prior written permission is obtained
from Cart2Quote B.V.


## SUMMARY

The purpose of the Cart2Quote module is to enrich Magento with 
a complete quotation manager. With Cart2Quote users can create 
quotes in the Magento front-end or backend and manage the complete 
quotation process and at the end convert it to an order.


## REQUIREMENTS

PHP: 		    5.6 / 5.6 / 7.0.2 / 7.0.6 / 7.1.0

MAGENTO:        2.1.* / 2.2.*

ENCODED VERSION ONLY:
ext-ionCube-Loader:     * (at least version 6.0.1)

## INSTALLATION

### OPENSOURCE:

Please follow the installation manual:
https://www.cart2quote.com/documentation/magento-2-cart2quote-installation-manual/

### ENCODED (packagist):

1.      composer require cart2quote/module-quotation-encoded

1.      php -f bin/magento module:enable Cart2Quote_Quotation

2.      php bin/magento setup:upgrade

3.      rm -rf var/generation/* var/cache/* pub/static/frontend/* pub/static/adminhtml/* var/page_cache/* var/di/* var/di

4.      php -f bin/magento setup:di:compile

5.      php -f bin/magento setup:static-content:deploy

6.      php -f bin/magento indexer:reindex

## CONTACT

Quick Start manual:
https://www.cart2quote.com/media/manuals/Cart2Quote_Quick_Start_M2.pdf

User manual:
https://www.cart2quote.com/documentation/magento-2-cart2quote-user-manual/

Request a support ticket:
https://cart2quote.zendesk.com/hc/en-us/requests/new

Request a customization:
https://www.cart2quote.com/magento-customizations

Request an update or upgrade:
https://www.cart2quote.com/cart2quote-update-upgrade.html
