Cron configuration
==================

In order to fetch rates, you should setup your crontab to execute `roc:exchange-rate:fetch`
command periodically.

The command by itself exposes several options:

- `date`: You can explicitly state for which date rates should be fetched.
If not provided, current date will be used.
- `source`: You can explicitly state from which sources rates should be
fetched. If not provided, all sources will be used. If you want to state
several sources, separate them with coma.
- `silent`: By default, when fetch rate is complete, either successfully or
with errors, an events from `RunOpenCode\Bundle\ExchangeRate\Event\FetchEvents`
will be fired. Depending on your settings and custom implementation, that
could trigger e-mail notifications and such. You can set `silent` to `true`
in order to prevent dispatch of events.

## Some recommendations from real life practice:

1. National banks usually publish their rates after 2PM for the next day.
Commercial banks do that usually after 7AM, 8AM on the same date. In that
matter, it is advisable to setup cron tasks for each source individually
according to the source's exchange rate publishing practice.
2. Sources do tend to fail, due to server failure or other unexpected
reasons. Because you can execute command several times without side effects,
it is advisable to setup cron task to fetch rates few times per day, per example,
4 times, every 30 minutes starting from the time when you know that source
publishes exchange rates. All fetch attempts should be set with `silent` parameter
as `true`, while last one you should leave to send notifications (if they are
configured to be delivered) in order for you to know if everything went fine.
3. Do not overflow source with unnecessary requests, more is not better.
The very idea of library and this bundle is to decrease number of requests
towards external service.

[<<Configuration](configuration.md) | [Table of contents](index.md) | [Custom implementations>>](custom-implementations.md)
