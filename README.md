# Test Challanage Kodeia


## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.4/installation#installation)

Alternative installation is possible without local dependencies relying on [Docker](#docker). 

Clone the repository

    git clone https://github.com/sanjayaharshana/test_challange.git

Switch to the repo folder

    cd test_challange

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Set Database Credential .env
    
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=challange_test
    DB_USERNAME=root
    DB_PASSWORD=

Set Woo commerce Configuration .env file
 
    WOOCOM_ENDPOINT=https://woocommerce.kodeia.com/wp-json/wc/v3/products
    WOOCOM_CUSUMER_KEY=ck_e6415ebb42b985a9985b5f99b5fddcbfbffda72a
    WOOCOM_CUSUMER_SECRECT=cs_4414f67bcf3dcf8d2dfc1cd7a0c1117010211d88
    SYNC_PRODUCT_LIMIT=10

Generate a new application key

    php artisan key:generate