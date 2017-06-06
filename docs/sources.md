Sources
=======

In order to work with exchange rates in your applications, you have to provide
a sources for exchange rates. The purpose of source is to fetch desired
rates on given date.

List of currently known implementations of sources can be found
[here](https://github.com/RunOpenCode/exchange-rate#known-implementations-of-sources).
If you want to implement your own source, it is required to implement
[`RunOpenCode\ExchangeRate\Contract\SourceInterface`](https://github.com/RunOpenCode/exchange-rate/blob/master/src/RunOpenCode/ExchangeRate/Contract/SourceInterface.php)
interface.

There are two methods how you can configure your sources, booth are explained
in text below.

## Configure sources trough Symfony's service container

Whether your implementation of source has additional dependencies or not,
you can register it trough Symfony's service container by simply tagging it
with `runopencode.exchange_rate.source` tag.

In order to optimize service container, it is required to set definition
attribute `public` to `false` where applicable.

## Configure sources trough bundle's configuration

If your implementation of source does not have additional dependencies,
you can register it trough bundle's configuration by adding its class
to a list of classes under `sources` key, example:

    runopencode_exchange_rate:
        sources:
            [ '\MyNameSpace\MySourceImplementation\First', '\MyNameSpace\MySourceImplementation\Second']

[<<Routes configuration](routes-configuration.md) | [Table of contents](index.md) | [Configuration>>](configuration.md)
