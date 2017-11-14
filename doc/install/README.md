# Installation Guide

## Step 1: Install Your Web Server
### nginx [RECOMMENDED]
nginx is the recommended web server to use when deploying a new ie2 instance.

### Apache
TBD

## Step 2: Install PHP / PHP Extensions
ie2 supports both PHP version 5.4 and 7.1. It is recommended you use the latest version of PHP (7.1), however.

### PHP 7.1 [RECOMMENDED]
Install the following packages:

* php7.1
* php7.1-mcrypt
* php7.1-mysql

### PHP 5.4
Install the following packages:

* php5
* php5-mcrypt
* php5-mysql

## Step 3: Install Composer
[Composer](https://getcomposer.org/) is a package manager for PHP. We use this to bundle up our dependencies, to keep our repository pretty light.

## Step 4: Get the Code + Dependencies
* ```git clone https://github.com/ubnetdef/ie2.git /var/www/ie2 && cd /var/www/ie2 && composer install --no-dev```

## Step 5: Configure the System
* ```cp dot.env .env```
* For more information on the configuration variables, please see [this wiki page](config.md)

## Step 6: Install the Database
* ```./app/Console/cake engine install```

## Step 7: Link the webroot
* ```rm -rf /var/www/html && ln -s /var/www/ie2/public /var/www/html```

## Step 8: Setup the System
Attempt to login to your newly deployed ie2 instance on your web server, using the credentials from step #6. Once you login, please follow the [Admin Guide](../admin_guide/README.md) from here.