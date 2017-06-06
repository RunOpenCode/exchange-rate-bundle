Extend and override
===================

In order to extend and override this bundle, you should be familiar with
Symfony's methods and recommendation for overriding vendor's bundle
which is documented [here](http://symfony.com/doc/current/bundles/inheritance.html)
and [here](http://symfony.com/doc/current/templating/overriding.html).

## Override template

All templates are can be found in `Resources/views` directory. Templates
are very basic and they should provide you with guidelines only, they are
far from full, usable, product. You can use Symfony's recipe for overriding
bundle's templates, or you can override them by extending controllers and
modifying `getTwigTemplate()` method.

## Override routing and extend controllers

Since routes has to be manually added to project, you can replace them
with your own and point routes to your own controllers which inherits
base controllers provided with this bundle.

There are several protected methods which can be useful for modifications.
Their availability depends on relevant controller, of course.

- `getFormType()` provides form type class name and you can modify in order
to modify form (where needed).
- `getFilterFormType()` provides form type class name and you can modify in order
to modify filter form (where needed).
- `redirectAfterSuccess()` defines redirection route when action
is successfully executed.
- `getTwigTemplate()` defines template which ought to be rendered.

## Services

All service classes are provided as container parameters. In order to
override any service which your own custom implementation can be easy
as modifying container parameter in your configuration. Consult `Resources/config/services`
directory for further reference.

## Translations

For customizing translations, please consult `Resources/translations/runopencode_exchange_rate.en.yml`
file.

[<<Custom implementations](custom-implementations.md) | [Table of contents](index.md) | [Form types and validators>>](form-types-and-validators.md)
