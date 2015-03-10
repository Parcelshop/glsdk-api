GLS parcelshop (for Denmark)
============================

[![Build Status](https://travis-ci.org/lsv/glsdk-api.svg)](https://travis-ci.org/lsv/glsdk-api) [![Coverage Status](https://coveralls.io/repos/lsv/glsdk-api/badge.svg?branch=master)](https://coveralls.io/r/lsv/glsdk-api?branch=master)

Get parcelshops from either

* A parcelshop number
* A danish zipcode
* Nearby an address
* Or get all parcelshops in Denmark

### Get single parcelshop by Id

````php
<?php
require 'vendor/autoload.php';

use Lsv\GlsDk\ParcelShop;

$p = new ParcelShop();
$shop = $p->getParcelshop( ID );
````

Throws ````Exceptions\ParcelNotFoundException```` if not found

Returns ````$shop```` is a ````Entity\Parcelshop```` object

### Get parcelshops from a zipcode

````php
<?php
require 'vendor/autoload.php';

use Lsv\GlsDk\ParcelShop;

$p = new ParcelShop();
$shops = $p->getParcelshopsFromZipcode( ZIPCODE );
````

Throws ````Exceptions\NoParcelsFoundInZipcodeException```` if none found

Returns ````$shops```` is a array of ````Entity\Parcelshop````

### Get parcelshops near address
 
````php
<?php
require 'vendor/autoload.php';

use Lsv\GlsDk\ParcelShop;

$p = new ParcelShop();
$shops = $p->getParcelshopsNearAddress( STREET , ZIPCODE, 20 );
````

Third argument is how many you want

Throws ````Exceptions\MalformedAddressException```` if address is unknown

Returns ````$shops```` is a array of ````Entity\Parcelshop````
 
### Get all parcelshops in Denmark

````php
<?php
require 'vendor/autoload.php';

use Lsv\GlsDk\ParcelShop;

$p = new ParcelShop();
$shops = $p->getAllParcelshops();
````

Returns ````$shops```` is a array of ````Entity\Parcelshop````

### Add retry guzzle client

First install it with composer

````
composer require guzzlehttp/retry-subscriber
````

Now create our client

````php
<?php
require 'vendor/autoload.php';

use Lsv\GlsDk\ParcelShop;
use GuzzleHttp\Subscriber\Retry\RetrySubscriber;

$retry = new RetrySubscriber([
    'filter' => RetrySubscriber::createStatusFilter()
]);

$client = new GuzzleHttp\Client();
$client->getEmitter()->attach($retry);

$p = new ParcelShop($client);
$shops = $p->getAllParcelshops();
````

### Change to another GLS country

````php
<?php
require 'vendor/autoload.php';

use Lsv\GlsDk\ParcelShop;

$p = new ParcelShop(null, 'url-to-webservice');
$shops = $p->getAllParcelshops();
````
