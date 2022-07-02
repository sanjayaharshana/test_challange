
# Thank you So Much 

**First of all thank you so much for giving me this challenge. I think I have completed the challenge given by you.**

## Note:

When installing the project check example.env file. I added 4 more key variables in the example.env file.


    WOOCOM_ENDPOINT = The endpoint of the woocommerce website you are using
    
    WOOCOM_CUSUMER_KEY = Consumer key of provided woocommerce wordpress website
    
    WOOCOM_CUSUMER_SECRECT = Consumer Secret of the provided woocommerce website
    
    SYNC_PRODUCT_LIMIT = How much want to store product from woocommerce website API


## Endpoints

	https://localhost:8000/api/register - POST
**params**: _[name, 	email, 	password]_

**return**:
	bearer_token


	https://locahost:8000/api/login - POST
**params**: _[email,	password]_

**return**:
	_bearer_token_

	
	https://locahost:8000/api/products - GET
**auth**: _bearer token_

**return**: log_list

	
	https://locahost:8000/api/logs/ - GET
**auth**: _bearer token_

**return**: log_list

## Laravel Queue

I have used the laravel queue, so use this command before test:

    php artisan queue: work
I used the laravel task schedule to daily schedule function.
and also I create a separate console command to run that function manually.

    php artisan sync:wooproducts

You can check if this function is working


## Unit Testing 
I have also created some Unit Testing to test some of these APIs. You can also test them. I was unable to release github action. There is a problem with the yml database.

    php artisan test
    
## Server Configration Task Scheduling 
When using Laravel's scheduler, we only need to add a single cron configuration entry to our server that runs the schedule:run command every minute. 

    * * * * * cd /test_challange && php artisan schedule:run >> /dev/null 2>&1
    
## Schedule Run Locally

    php artisan schedule:work
    
