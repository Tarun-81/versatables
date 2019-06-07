## Documentation

- Installation guide: https://docs.mageplaza.com/kb/installation.html
- User Guide: https://docs.mageplaza.com/one-step-checkout-m2/
- Product page: https://www.mageplaza.com/magento-2-one-step-checkout-extension/
- Get Support: https://mageplaza.freshdesk.com/ or support@mageplaza.com
- Changelog: https://www.mageplaza.com/changelog/m2-one-step-checkout.txt
- License agreement: https://www.mageplaza.com/LICENSE.txt



## How to install

### Method 1: Install ready-to-paste package (Recommended)

- Download the latest version at https://store.mageplaza.com/my-downloadable-products.html
- Installation guide: https://docs.mageplaza.com/kb/installation.html



### Method 2: Manually install via composer

1. Access to your server via SSH
2. Create a folder (Not Magento root directory) in called: `mageplaza`, 
3. Download the zip package at https://store.mageplaza.com/my-downloadable-products.html
4. Upload the zip package to `mageplaza` folder.


3. Add the following snippet to `composer.json`

```
	{
		"repositories": [
		 {
		 "type": "artifact",
		 "url": "mageplaza/"
		 }
		]
	}
```

4. Run composer command line

```
composer require mageplaza/magento-2-one-step-checkout-extension
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## How to upgrade

1. Backup
Backup your Magento code, database before upgrading.
2. Remove OSC folder 
In case of customization, you should backup the customized files and modify in newer version. 
Now you remove `app/code/Mageplaza/Osc` folder. In this step, you can copy override Osc folder but this may cause of compilation issue. That why you should remove it.
3. Upload new version
Upload this package to Magento root directory
4. Run command line:

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```



## FAQs


#### Q: I got error: `Mageplaza_Core has been already defined`
A: Read solution: https://github.com/mageplaza/module-core/issues/3

#### Q: I got compile error
Total Errors Count: 5 Errors during compilation:
A: There are 2 major Mageplaza Osc version: OSC v1.x and OSC v2.x . If you are upgrade from OSC v1.x to V2.x, you should remove app/code/Mageplaza/Osc folder before upgrading.

#### Q: My site is down
A: Please follow this guide: https://www.mageplaza.com/blog/magento-site-down.html

