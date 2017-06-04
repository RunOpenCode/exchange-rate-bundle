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


