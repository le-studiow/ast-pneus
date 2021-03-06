CHANGELOG
=========

v1.0.0
------

Getting more serious with a 1.0.0 release! 🎉

* Adds User-Agent with PHP and client version
* Adds ability to add User-Agent "components" to the request

v0.0.7
-------

* Eligibility check now returns `200 OK` with `{"eligible": false}` for non-eligible purchases:
    supports legacy `406` status code and the new version
* New eligibility check's negative response includes constraints that should be met to be eligible


v0.0.6
-------

* Adds the possibility to flag a payment as potentially fraudulent

v0.0.5
------

* Adds `Alma\API\Payment::STATE_PAID`


v0.0.4
------

* Adds support for Sandbox API root


v0.0.3
------

* Bug fixes:
    * Missing default Logger in Client instantiation - now using Psr\Log\NullLogger
    * Alma\Api\Entity\Payment attributes had been wrongly converted to `camelCase`

v0.0.2
------

* Updates root namespace to `Alma\API` instead of just `Alma`
* Adds PSR-4 autoload config to `composer.json`
* Logger is now just an option to the Client creation
* Uses PSR-3 logger spec

v0.0.1
------

* Initial "pre-release" of the API Client
* Includes two main endpoints: Payments and Merchants
* Provides what's necessary for a typical payment flow:
    * `Payments.createPayment` and `Payments.eligibility`
    * `Merchants.me`
* Base `Alma\API\Client` class can be configured with API key and live/test mode
* TLS is automatically forced to TLS 1.2 to meet Alma's security requirements, but configurable
