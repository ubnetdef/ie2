ie2
========

Cyber Security competition Inject Engine.  ie2 integrates with UBNetDef's [ScoreEngine](https://github.com/ubnetdef/scoreengine) and [Bank-API](https://github.com/ubnetdef/bank-api) via optional plugins.

## Requirements

* Web server (such as apache2)
* php5
* php5-mcrypt
* php5-mysql
* MySQL-server
* [Composer](https://getcomposer.org/download)

## Quick Installation

1. Install git and clone repository
2. Run ```cp dot.env .env```, and edit the file.
3. Run ```php composer.phar install``` to install the project dependencies & core cakephp files
4. Run ```cd app/Console && ./cake engine install``` to install the Inject Engine
5. Point your webroot to the directory "webroot"
6. You're done!

## Documentation
Please click [here](doc/)
