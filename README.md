Social Network with Apigility
=============================
This application is my first contact with Zend Framework 2 and [Apigility](https://github.com/zfcampus/apigility.org) 
It's based on **[Zend Framework 2 Application Development](http://www.packtpub.com/zend-framework-2-application-development/book)** book of [Christopher Valles](https://github.com/christophervalles)

I suggest you read it if you started in Zend Framework 2.

----------
I added several modifications, such as using Apigility, and oauth2

Installation
------------

### Via Git (clone)

First, clone the repository:

    git clone git@github.com:oussamak/social-network.git
Next, go to social_api (apigility) directory.

    cd ./social_api

At this point, you need to use [Composer](https://getcomposer.org/) to install
dependencies. Assuming you already have Composer:

    php composer self-update
    php composer.phar install


### All methods

Once you have the basic installation, you need to put it in development mode:

    php public/index.php development enable # put the skeleton in development mode


Now, fire it up! Do one of the following:

- Create a vhost in your web server that points the DocumentRoot to the
  `public/` directory of the project
- Fire up the built-in web server in PHP (5.4.8+) (**note**: do not use this for
  production!)

In the latter case, do the following:

    php -S 0.0.0.0:8080 -t public public/index.php


You can then visit the site at http://localhost:8080/ - which will bring up a
welcome page and the ability to visit the dashboard in order to create and
inspect your APIs.

##### NOTE ABOUT USING THE PHP BUILT-IN WEB SERVER

PHP's built-in web server did not start supporting the `PATCH` HTTP method until
5.4.8. Since the admin API makes use of this HTTP method, you must use a version
&gt;= 5.4.8 when using the built-in web server.


### Database
go to `./social_api/data` and import sql file to `sn` database 

### Client
go to Client directory.

    cd ../client
    php composer self-update
    php composer.phar install
    bower install


TODO
=

- use bootstrap 3.0
- add contact form
- some AngularJs features in client side
- form validation with image nudity detect
- etc
