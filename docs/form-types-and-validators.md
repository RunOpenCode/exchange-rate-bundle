Form types and validator constraints
====================================

There are several pre-built form types and validators in relation to
exchange rates which can help you in your project.

## Form types

### `RunOpenCode\Bundle\ExchangeRate\Form\Type\SourceType`

Child of `Symfony\Component\Form\Extension\Core\Type\ChoiceType`. Contains
choices of all configured sources. `choice_translation_domain` is set to
`runopencode_exchange_rate`. It can be [configured](configuration.md)
on project level.

### `RunOpenCode\Bundle\ExchangeRate\Form\Type\RateTypeType`

Child of `Symfony\Component\Form\Extension\Core\Type\ChoiceType`. Contains
choices of all configured rate types. `choice_translation_domain` is set to
`runopencode_exchange_rate`. It can be [configured](configuration.md)
on project level.

### `RunOpenCode\Bundle\ExchangeRate\Form\Type\CurrencyCodeType`

Child of `Symfony\Component\Form\Extension\Core\Type\ChoiceType`. Contains
choices of all configured currency codes types, along with base currency code.
`choice_translation_domain` is set to `runopencode_exchange_rate`.
It can be [configured](configuration.md) on project level.

### `RunOpenCode\Bundle\ExchangeRate\Form\Type\ForeignCurrencyCodeType`

Child of `Symfony\Component\Form\Extension\Core\Type\ChoiceType`. Contains
choices of all configured currency codes types, without base currency code.
`choice_translation_domain` is set to `runopencode_exchange_rate`.
It can be [configured](configuration.md) on project level.

### `RunOpenCode\Bundle\ExchangeRate\Form\Type\RateType`

Child of `Symfony\Component\Form\Extension\Core\Type\ChoiceType` as well,
but choices are compound values composed of `source`, `rate_type` and `currency_code`
in following format: _`source.rate_type.currency_code`_ so it can uniquely
identify one configured rate.

`choice_translation_domain` is set to `runopencode_exchange_rate`.
It can be [configured](configuration.md) on project level.

## Validator constraints

### BaseCurrency constraint

Validator constraint `RunOpenCode\Bundle\ExchangeRate\Validator\Constraints\BaseCurrency`
validates that value is configured base currency

Validation message translation domain is `runopencode_exchange_rate`.

### ExchangeRate constraint

Validator constraint `RunOpenCode\Bundle\ExchangeRate\Validator\Constraints\ExchangeRate`
validates that value identifies configured rate. Purpose of its use is to
validate value provided with `RunOpenCode\Bundle\ExchangeRate\Form\Type\RateType`
form type.

Validation message translation domain is `runopencode_exchange_rate`.

[<<Extend and override](extend-and-override.md) | [Table of contents](index.md)

