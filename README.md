# Shipmate php Library

a simple unofficial library for the use of Shipmate api.
### Requirements\

php 5.3.3 and later
### Composer

You can install the bindings via Composer. Run the following command:

```
composer require shipmates/shipmates-php
```

To use the bindings, use Composer's autoload:

```
require_once('vendor/autoload.php');
```
There is no manual installation available at the moment.

### Gettion started

Simple usage looks like:
```PHP
\Shipmate\Shipmate::setApiKey('your-api-key');
$deletedConsignment = \Shipmate\Consignment::delete('consignment-reference-number');
echo $deletedConsignment;
```
or:
```PHP
\Shipmate\Shipmate::setApiKey('your-api-key');
$attributes = \Shipmate\Parcel::allAttributes();
echo $attributes;
```
The above will make it easier to interact with the Shipmate api, calling the api will be as easy as calling a method, the developer will not have to know anything about headers or the HTTPclient if using Laravel. Everything is managed by the library.

The library can be tested [here](http://deliverify26.herokuapp.com/shipmate)

### Documentation

the documentation is not complete as the library is not ready for production use, it is just an example.

### Tests

the library is not tested.
