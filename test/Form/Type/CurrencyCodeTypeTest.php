<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Form\Type;

use PHPUnit\Framework\TestCase;
use RunOpenCode\Bundle\ExchangeRate\Form\Type\CurrencyCodeType;
use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Registry\RatesConfigurationRegistry;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CurrencyCodeTypeTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Form\Type
 */
class CurrencyCodeTypeTest extends TestCase
{
    /**
     * @test
     */
    public function itConfiguresDefaults()
    {
        $ratesRegistry = new RatesConfigurationRegistry([
            new Configuration('EUR', 'median', 'nbs'),
            new Configuration('CHF', 'median', 'nbs'),
            new Configuration('USD', 'median', 'nbs'),
        ]);

        $type = new CurrencyCodeType($ratesRegistry, 'RSD', [
            'preferred_choices' => ['EUR', 'RSD']
        ]);

        $resolver = new OptionsResolver();

        $type->configureOptions($resolver);

        $defaults = $resolver->resolve([]);

        $this->assertEquals([
            'choice_translation_domain' => false,
            'choices' => [
                'RSD' => 'RSD',
                'EUR' => 'EUR',
                'CHF' => 'CHF',
                'USD' => 'USD',
            ],
            'preferred_choices' => [
                'EUR',
                'RSD',
            ]
        ], $defaults);
    }

    /**
     * @test
     */
    public function itIsChoiceType()
    {
        $ratesRegistry = new RatesConfigurationRegistry([
            new Configuration('EUR', 'median', 'nbs'),
            new Configuration('CHF', 'median', 'nbs'),
            new Configuration('USD', 'median', 'nbs'),
        ]);

        $type = new CurrencyCodeType($ratesRegistry, 'RSD', [
            'preferred_choices' => ['EUR', 'RSD']
        ]);

        $this->assertEquals(ChoiceType::class, $type->getParent());
    }
}
