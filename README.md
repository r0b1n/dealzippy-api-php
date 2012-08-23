dealzippy-api-php
=================

Simple dealzippy.co.uk api php client
API docs: http://www.dealzippy.co.uk/api/documentation/


#####Small example#####
Get active Deals 
```php
$dz = new DealZippy("api-key", true);
$results = $dz->getDeals();
```