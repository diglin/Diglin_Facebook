# Diglin_Facebook - Facebook Audience Tracking #

This Magento 1 module add the necessary Facebook tags for events done on category, product, cms and checkout pages.
Necessary to make Facebook retargeting with Facebook Product Ads for example.

## Requirements

- Facebook Pixel ID

## Installation

### Via modman
- Install [modman](https://github.com/colinmollenhour/modman)
- Use the command from your Magento installation folder: `modman clone https://github.com/diglin/Diglin_Facebook.git`

### Via Composer

- Install [composer](http://getcomposer.org/download/)
- Create a composer.json into your project like the following sample:

```json
{
    ...
    "require": {
        "diglin/diglin_facebook":"*"
    },
    "repositories": [
	    {
            "type": "composer",
            "url": "http://packages.firegento.com"
        }
    ],
    "extra":{
        "magento-root-dir": "./"
    }
}

```

- Then from your composer.json folder: `php composer.phar install` or `composer install`


### Manually
- You can copy the files from the folders of this repository to the same folders of your installation starting from the `src` folder

## Documentation

Go to the menu `System > Configuration > Diglin > Facebook` and set your Pixel ID which can be found from your Facebook Business account.


## Uninstall

### Via modman

`modman remove Diglin_Facebook`

### Via manually

- Delete the files
	- app/code/community/Diglin/Facebook
	- app/design/frontend/base/default/layout/diglin/facebook
	- app/design/frontend/base/default/template/diglin/facebook
	- app/etc/modules/Diglin_Facebook.xml

## Author

* Sylvain Rayé
* http://www.diglin.com/
* [@diglin](https://twitter.com/diglin_)
* [Follow me on github!](https://github.com/diglin)