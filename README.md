# Aldrapay Payment Module for Magento 2 CE

This is a Payment Module for Magento 2 Community Edition, that gives you the ability to process payments through payment service providers running on Aldrapay platform.

## Requirements

  * Magento 2 Community Edition 2.x (Tested up to __2.1.3__)
  * [Aldrapay PHP API library v1.0.0](https://github.com/aldrapay/aldrapay-api-php) - (Integrated in Module)
  * PCI DSS certified server in order to use ```Aldrapay Direct``` (implementation not fully completed)

*Note:* this module has been tested only with Magento 2 __Community Edition__, it may not work as intended with Magento 2 __Enterprise Edition__

## Installation (composer)

  * Install __Composer__ - [Composer Download Instructions](https://getcomposer.org/doc/00-intro.md)

  * Install __aldrapay Gateway__

    * Install Payment Module

        ```sh
        $ composer require aldrapay/magento2-payment-module
        ```

    * Enable Payment Module

        ```sh
        $ php bin/magento module:enable Aldrapay_Aldrapay
        ```

        ```sh
        $ php bin/magento setup:upgrade
        ```
    * Deploy Magento Static Content (__Execute If needed__)

        ```sh
        $ php bin/magento setup:static-content:deploy
        ```    

## Installation (manual)

  * [Download the Payment Module archive](https://github.com/aldrapay/magento2-payment-module/archive/master.zip), unpack it and upload its contents to a new folder ```<root>/app/code/Aldrapay/Aldrapay/``` of your Magento 2 installation

  * Install Aldrapay PHP API Library

    ```sh
    $ composer require aldrapay/aldrapay-api-php
    ```

  * Enable Payment Module

    ```sh
    $ php bin/magento module:enable Aldrapay_Aldrapay --clear-static-content
    ```

    ```sh
    $ php bin/magento setup:upgrade
    ```

  * Deploy Magento Static Content (__Execute If needed__)

    ```sh
    $ php bin/magento setup:static-content:deploy
    ```   
    
## Functionality and Configuration Note

  * __The Checkout Method using external Payment Hosted Page is recommended.__ 
  * __!!! Direct Method may need additional configuration from the Magento developer, as it is still in development state !!!__

## Configuration

  * Login inside the __Admin Panel__ and go to ```Stores``` -> ```Configuration``` -> ```Sales``` -> ```Payment Methods```
  * If the Payment Module Panel ```Aldrapay``` is not visible in the list of available Payment Methods,
  go to  ```System``` -> ```Cache Management``` and clear Magento Cache by clicking on ```Flush Magento Cache```
  * Go back to ```Payment Methods``` and click the button ```Configure``` under the payment method ```Aldrapay Checkout``` or ```Aldrapay Direct``` to expand the available settings
  * Set ```Enabled``` to ```Yes```, set the correct credentials, select your prefered transaction types and additional settings and click ```Save config```

## Configure Magento over secured HTTPS Connection

This configuration is needed for ```Aldrapay Direct``` Method to be usable, however we strongly recommend to have it set whenever possible in all cases.

Steps:

  * Ensure you have installed a valid SSL Certificate on your Web Server & you have configured your Virtual Host correctly.
  * Login to Magento 2 Admin Panel
  * Navigate to ```Stores``` -> ```Configuration``` -> ```General``` -> ```Web```
  * Expand Tab ```Base URLs (Secure)``` and set ```Use Secure URLs on Storefront``` and ```Use Secure URLs in Admin``` to ```Yes```
  * Set your ```Secure Base URL``` and click ```Save Config```
  * It is recommended to add a **Rewrite Rule** from ```http``` to ```https``` or to configure a **Permanent Redirect** to ```https``` in your virtual host

## Test data

If you setup the module with default values, you can use the test data to make a test payment:

  * Shop Id ```XXX```
  * Shop Secret Key ```YYYYYYYYYYYY```
  * Checkout Domain ```secure.aldrapay.com```
  * Gateway Domain ```secure.aldrapay.com```

### Test card details

 For testing with creedit cards, please check online API docs, testing page https://secure.aldrapay.com/backoffice/docs/api/testing.html#test-cards
