Exchange rate bundle
====================

*Fetch, store and use currency exchange rates in your Symfony application*

Exchange rate bundle is Symfony wrapper for [RunOpenCode/exchange-rate](https://github.com/RunOpenCode/exchange-rate)
library.

It will enable you to easily fetch, query and manage exchange rates in your
Symfony application when dealing with payments and prices in foreign currencies.

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

## Table of contents

- [Instalation](installation.md)
- [Configuration](configuration.md)