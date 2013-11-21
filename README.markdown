CartBundle
=================

[![Build Status](https://secure.travis-ci.org/leaphly/CartBundle.png?branch=master)](http://travis-ci.org/leaphly/CartBundle) [![Total Downloads](https://poser.pugx.org/leaphly/cart-bundle/downloads.png)](https://packagist.org/packages/leaphly/cart-bundle) [![Latest Stable Version](https://poser.pugx.org/leaphly/cart-bundle/v/stable.png)](https://packagist.org/packages/leaphly/cart-bundle)


Mission-statement
----------


The Leaphly project makes it easier for developers to add cart functionality to the Symfony2 applications or to those applications that could consume REST API.

This software provides the tools and guidelines for building decoupled, high-quality and long-life e-commerce applications.

[continue reading on the website](http://leaphly.org)

Demo
----

[demo](http://leaphly.org/#demo)

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

Test
----

``` bash
composer.phar create-project leaphly/cart-bundle`
vendor/bin/phpunit
```

All the Functional testing are into the [leaphly-sandbox](https://github.com/leaphly/leaphly-sandbox).

About
-----

CartBundle is a [leaphly](https://github.com/leaphly) initiative.
See also the list of [contributors](https://github.com/leaphly/CartBundle/contributors).
CartBundle has been inspired by the architecture of FosUserBundle.

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/leaphly/CartBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.


TODO
-----------------------

- Complete the Rest controllers.

- Better usage of the finite transitions.

- Decouple Bundle and library.

- Add more DB drivers

