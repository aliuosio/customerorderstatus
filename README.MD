## Magento2 AliuOsio_CustomerOrderstatus

**Changes the status of an order depending on the E-Mail Confirmation status**

Backend Config `Stores -> Configuration -> Liberty Woman -> Customer Order status`

Upon an new order the status is set to onHold till the E-Mail has been verified.
If the Confirmation period which can be set in the backend is expired te customer can not orde rwith that Adress anymore.
This also includes orders made over the Backend


### Install
    composer require aliuosio/customerorderstatus;
    bin/magento se:up;
    bin/magento c:c;
