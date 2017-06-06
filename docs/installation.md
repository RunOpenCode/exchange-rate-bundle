Installation
============

## Step 1. - Download the bundle

Open a command console, enter your project directory, and execute the
following command to download the latest stable version of this bundle
and add it as a dependency to your project:

    composer require runopencode/exchange-rate-bundle

## Step 2. - Enable the bundle

Enable the bundle by adding `new RunOpenCode\Bundle\ExchangeRate\ExchangeRateBundle()`
to the bundles array of the `registerBundles` method in your project's
`app/AppKernel.php` file:

    <?php

    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new RunOpenCode\Bundle\ExchangeRate\ExchangeRateBundle()
            );
            // ...
        }
        // ...
    }

## Step 3. - Registering routes

Register bundle's routes by add the following to your project's routing file:

    # app/config/routing.yml
    _runopencode_exchange_rate_all:
        resource: "@ExchangeRate/Resources/config/routing/all.xml"
        prefix: /exchange-rate

For details about routes configuration and customization of which controller
will be included in project, please see [Routes configuration](routes-configuration.md)
section of this documentation.

## Step 4. - Define your sources

Configure sources for exchange rates according to instructions provided in [Sources](sources.md)
section of this documentation.

## Step 5 - Configure other bundle options

Configure bundle according to instructions provided in [Configuration](configuration.md)
section of this documentation.

## Step 6 - Setup cron task to fetch rates periodically

Configure cron to fetch rates every day according to instructions provided
in [Cron configuration](fetch-rates.md) section of this documentation.

[Table of contents](index.md) | [Routes configuration>>](routes-configuration.md)