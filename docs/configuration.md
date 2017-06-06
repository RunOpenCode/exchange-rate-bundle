Configuration
=============

Configuration of this bundle is possible by using both `.yml` and `.xml`
(recommended) configuration file. In case that you use an `.xml` file format
for configuration, please reffer to provided [XMLSchema](../src/RunOpenCode/Bundle/ExchangeRate/Resources/config/schema/configuration-1.0.0.xsd)
within your IDE tool.

_**NOTE:** In order for this bundle to successfully be utilized, at least one source
for exchange rates must be included in project. For details about exchange
rate sources, please see relevant [section](sources.md) of this documentation.
For the sake of the simplicity, we will preasume in this chapter that source
for rates from [National bank of Serbia](https://github.com/RunOpenCode/exchange-rate-nbs)
is installed as well._

## Minimum configuration

Minimum configuration requires from you to configure your base currency,
as well as exchange rates with which you intend to work with. Example:

    runopencode_exchange_rate:
        base_currency: RSD
        rates:
            - { currency_code: EUR, rate_type: median, source: national_bank_of_serbia }
            - { currency_code: CHF, rate_type: median, source: national_bank_of_serbia }
            - { currency_code: USD, rate_type: median, source: national_bank_of_serbia }

In order to validate your configuration settings, you can execute command
`debug:runopencode:exchange-rate` in your console which will provide you
with configuration overview and useful debugging info.

## Other configuration options

### Configure repository

For now, two repository implementations are available for configuration,
a file repository, where rates are stored in plain text file, and Doctrine
Dbal repository, where rates are stored in table of configured,
relational database. In example below are provided default configuration values.

    runopencode_exchange_rate:
        repository: file
        file_repository:
            path: '%kernel.root_dir%/../var/db/exchange_rates.dat'
        doctrine_dbal_repository:
            connection: doctrine.dbal.default_connection
            table_name: runopencode_exchange_rate

With `repository` key, you can define whether you want to use `file` or
`doctrine_dbal` repository.

Each repository can be additionally configured, for file repository, file
location can be configured, while for Doctrine Dbal repository you can configure
which connection should be used as well as table name where rates should be stored.

For custom repository implementation and usage, see chapter [Custom implementations](custom-implementations.md).

### Configure CRUD security

Default security configuration presented below for CRUD controllers
will deny access to all users by default.

    runopencode_exchange_rate:
        security:
            enabled: true
            view: [  ]
            create: [  ]
            edit: [  ]
            delete: [  ]

In order to allow access, you can either disable security check by setting
configuration paramter `enabled` to `false` (not recommended), or state
required user roles for each CRUD activity, per example:

    runopencode_exchange_rate:
        security:
            enabled: true
            view: [ 'ROLE_USER', 'ROLE_ADMIN' ]
            create: [ 'ROLE_ADMIN' ]
            edit: [ 'ROLE_ADMIN' ]
            delete: [ 'ROLE_ADMIN' ]

which means that only user with `ROLE_USER` and `ROLE_ADMIN` can see rates,
but only user with `ROLE_ADMIN` can create, edit or delete rate.

### Configure form types

There are several form types that are available to you to use in your forms:

- `RunOpenCode\Bundle\ExchangeRate\Form\Type\SourceType`
- `RunOpenCode\Bundle\ExchangeRate\Form\Type\RateTypeType`
- `RunOpenCode\Bundle\ExchangeRate\Form\Type\CurrencyCodeType`
- `RunOpenCode\Bundle\ExchangeRate\Form\Type\ForeignCurrencyCodeType`
- `RunOpenCode\Bundle\ExchangeRate\Form\Type\RateType`

All before mentioned types are child of `Symfony\Component\Form\Extension\Core\Type\ChoiceType`
and their choices are pre-built based on your rate configuration.

Difference between `CurrencyCodeType` and `ForeignCurrencyCodeType` is that
`CurrencyCodeType` contains configured `base_currency`, while `ForeignCurrencyCodeType`.

`RateType` is choice also, but choices are compound values composed of
`source`, `rate_type` and `currency_code` in following format: _`source.rate_type.currency_code`_
so it can uniquely identify one configured rate.

You can configure `choice_translation_domain` and `preferred_choices` for
all choice types globally on project level via configuration. Of course,
you can do that as well on form type level as well, where you can configure
other options as well.

If additional customisation required, it is recommended that you create
your own form type based on above mentioned types.

Default configuration for mentioned types is:

    runopencode_exchange_rate:
        form_types:
            source_type:
                choice_translation_domain: runopencode_exchange_rate
                preferred_choices: [ ]
            rate_type_type:
                choice_translation_domain: runopencode_exchange_rate
                preferred_choices: [ ]
            currency_code_type:
                choice_translation_domain: runopencode_exchange_rate
                preferred_choices: [ ]
            foreign_currency_code_type:
                choice_translation_domain: runopencode_exchange_rate
                preferred_choices: [ ]
            rate_type:
                choice_translation_domain: runopencode_exchange_rate
                preferred_choices: [ ]

### Configure notifications

When exchange rates are fetched, you can be notified via e-mail about success
or fail of mentioned process. In order to do so, first you need to
configure `mailer` service in your Symfony project since that service is
used for sending mails. How to do that you can find in official Symfony
[documentation](http://symfony.com/doc/current/email.html).

After that, it is require to enable mail notifications in your configuration
and provide recipients mail addresses where you want those notifications
to be sent. Example:

    runopencode_exchange_rate:
        notifications:
            email:
                enabled: true
                recipients: [ 'test@test.com', 'other@test.com' ]

For custom implementation of notification service, see chapter [Custom implementations](custom-implementations.md).

 [<<Sources](sources.md) | [Table of contents](index.md) | [Cron configuration>>](fetch-rates.md)