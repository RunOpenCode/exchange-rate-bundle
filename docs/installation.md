Installation
============

## Step 1. - Download the Bundle

Open a command console, enter your project directory, and execute the
following command to download the latest stable version of this bundle
and add it as a dependency to your project:

    composer require runopencode/exchange-rate-bundle

## Step 2. - Enable the Bundle

Enable the bundle by adding `new RunOpenCode\Bundle\ExchangeRate\ExchangeRateBundle()`
to the bundles array of the registerBundles method in your project's
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

