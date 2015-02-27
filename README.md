GLS parcelshop (for Denmark)
============================

[![Build Status](https://travis-ci.org/lsv/glsdk-api.svg)](https://travis-ci.org/lsv/glsdk-api) [![Coverage Status](https://coveralls.io/repos/lsv/glsdk-api/badge.svg)](https://coveralls.io/r/lsv/glsdk-api)

Get parcelshops from either

* A parcelshop number
* A danish zipcode
* Nearby an address
* Or get all parcelshops in Denmark

## Get single parcelshop by Id

````php
<?php
require 'vendor/autoload.php';

use Lsv\GlsDk\ParcelShop;

$p = new ParcelShop();
$p->getParcelshop( ID );
````

Throws ````Exceptions\ParcelNotFoundException```` if not found

Returns ````Entity\Parcelshop````

## Get parcelshops from a zipcode

````php
<?php
require 'vendor/autoload.php';

use Lsv\GlsDk\ParcelShop;

$p = new ParcelShop();
$p->getParcelshopsFromZipcode( ZIPCODE );
````

Throws ````Exceptions\NoParcelsFoundInZipcodeException```` if none found

Returns array of ````Entity\Parcelshop````

## Get parcelshops near address
 
````php
<?php
require 'vendor/autoload.php';

use Lsv\GlsDk\ParcelShop;

$p = new ParcelShop();
$p->getParcelshopsNearAddress( STREET , ZIPCODE, 20 );
````

Third argument is how many you want

Throws ````Exceptions\MalformedAddressException```` if address is unknown

Returns array of ````Entity\Parcelshop````
 
## Get all parcelshops in Denmark

````php
<?php
require 'vendor/autoload.php';

use Lsv\GlsDk\ParcelShop;

$p = new ParcelShop();
$p->getAllParcelshops();
````

Returns array of ````Entity\Parcelshop````