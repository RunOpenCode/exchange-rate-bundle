Exchange rate bundle
====================

*Fetch, store and use currency exchange rates in your application*

Exchange rate bundle is Symfony wrapper for [RunOpenCode/exchange-rate](https://github.com/RunOpenCode/exchange-rate)
library.

[![Packagist](https://img.shields.io/packagist/v/RunOpenCode/exchange-rate-bundle.svg)](https://packagist.org/packages/runopencode/exchange-rate-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/RunOpenCode/exchange-rate-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/RunOpenCode/exchange-rate-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/RunOpenCode/exchange-rate-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/RunOpenCode/exchange-rate-bundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/RunOpenCode/exchange-rate-bundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/RunOpenCode/exchange-rate-bundle/build-status/master)
[![Build Status](https://travis-ci.org/RunOpenCode/exchange-rate-bundle.svg?branch=master)](https://travis-ci.org/RunOpenCode/exchange-rate-bundle)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7d45e1cd-63a2-474e-a252-9a11ee8faafb/big.png)](https://insight.sensiolabs.com/projects/7d45e1cd-63a2-474e-a252-9a11ee8faafb)

## Features

- Easy integration with exchange rate sources
(such as [National bank of Serba](https://github.com/RunOpenCode/exchange-rate-nbs)
and [Banca Intesa Serbia](https://github.com/RunOpenCode/exchange-rate-intesa-rs)) via configuration.
- Console commands for debugging configuration.
- Console commands for fetching configured rates via cron tasks or queue
implementations.
- Configurable e-mail notifications support for successful retrieval of
rates, as well as errors.
- CRUD controllers for viewing, editing, deleting and creating rates, with
configurable role-based security.
- Public REST api enabling you to deliver rates to other applications
and third parties.
- Easy extensibility and customization of each portion of bundle.

## Documentation

For more detailed information about the features of this bundle,
refer to the [documentation](docs/index.md).

## License

This bundle is published under MIT license. Please see [LICENSE](LICENSE) file distributed
with this package.