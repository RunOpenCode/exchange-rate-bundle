Custom implementations
======================

## Repository

If you want to implement your repository, implement `RunOpenCode\ExchangeRate\Contract\RepositoryInterface`
and register it in service container with `runopencode.exchange_rate.repository` tag.

In order to use your custom implementation of repository, either provide `alias` value
for your repository service and use alias in configuration, or state full service
name in configuration.

Example of configuration via alias:

    services:
        my_custom_repository_service:
            class: MyCustomRepository
            tags:
                - { name: runopencode.exchange_rate.repository, alias: my_custom_repo }

    runopencode_exchange_rate:
        repository: my_custom_repo

Example of configuration without alias:

    services:
        my_custom_repository_service:
            class: MyCustomRepository
            tags:
                - { name: runopencode.exchange_rate.repository }

    runopencode_exchange_rate:
        repository: my_custom_repository_service


## Processor

Processor will allow you to modify collection of rates after they are
fetched from sources. There are few known features that can be implemented
via processors:

- **Validation**: Fetched rates can be validated after fetch process, disabling
errors that could occur, if, per example, a source experienced difficulties.
Currently, two processors are already used and registered with this bundle,
`RunOpenCode\ExchangeRate\Processor\BaseCurrencyValidator` and
`RunOpenCode\ExchangeRate\Processor\UniqueRatesValidator` which validates
that all rates have same base currency and that they are unique.
- **Setting custom margins**: If bundle is used for, per example, an
exchange office, tuning up or setting up a custom buying and selling rate
for each rate can be done in custom processor.
- **Custom rates**: Processor input is collection of rates and output is
collection of rates. Processor can filter out unwanted rates and add other
custom defined rates as well.

In order to create your processor, implement
`RunOpenCode\ExchangeRate\Contract\ProcessorInterface`, register your class
in service container with `runopencode.exchange_rate.processor` tag. It is
advisable to set your service as private in order to optimize service container.

Order of processors matters, in that matter, you can add a `priority` attribute
to your service tag in order to fine tune its order of execution (lower
value gives higher execution priority). Note that
validation processors should be executed last. `RunOpenCode\ExchangeRate\Processor\BaseCurrencyValidator`
and `RunOpenCode\ExchangeRate\Processor\UniqueRatesValidator` have priorities
of 900 and 1000 respectively.

## Notifications

`roc:exchange-rate:fetch` command can trigger ([see details](fetch-rates.md))
events defined in `RunOpenCode\Bundle\ExchangeRate\Event\FetchEvents`, and
emitted objects would be either `RunOpenCode\Bundle\ExchangeRate\Event\FetchSuccessEvent`
or `RunOpenCode\Bundle\ExchangeRate\Event\FetchErrorEvent`, depending on success
of fetch process.

You can add your own notification handler by hooking into Symfony's
kernel events, according to official [documentation](http://symfony.com/doc/current/event_dispatcher.html).

[<<Cron configuration](fetch-rates.md) | [Table of contents](index.md) | [Extend and override>>](extend-and-override.md)
