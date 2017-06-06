Routes configuration
====================

This bundle is delivered with 3 routing configurations:

- [`@ExchangeRate/Resources/config/routing/admin.xml`](../src/RunOpenCode/Bundle/ExchangeRate/Resources/config/routing/admin.xml)
- [`@ExchangeRate/Resources/config/routing/rest.xml`](../src/RunOpenCode/Bundle/ExchangeRate/Resources/config/routing/rest.xml)
- [`@ExchangeRate/Resources/config/routing/all.xml`](../src/RunOpenCode/Bundle/ExchangeRate/Resources/config/routing/all.xml)

First configuration contains all routes required for CRUD controllers
to be accessible in your project. Second one contains all routes
required for REST api to be accessible in your project. Third one
combines all beforementioned routes in one file.

Include only relevant routing file depending on features which you would
like to support in your project in regards to exchange rates.

**NOTE**: _When importing routing configuration, it is recommended to specify
prefix for routes, since provided route paths are very generic and it is
quite possilbe that colision with courent routes can occur if prefix is not provided._

**Example of proper inclusion:**

    # app/config/routing.yml
    _runopencode_exchange_rate_all:
        resource: "@ExchangeRate/Resources/config/routing/all.xml"
        prefix: /exchange-rate

[<<Instalation](installation.md) | [Table of contents](index.md) | [Sources>>](sources.md)