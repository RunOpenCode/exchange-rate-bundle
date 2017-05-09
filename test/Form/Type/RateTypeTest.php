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
use RunOpenCode\Bundle\ExchangeRate\Form\Type\RateType;
use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Registry\RatesConfigurationRegistry;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RateTypeTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Form\Type
 */
class RateTypeTest extends TestCase
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

        $type = new RateType($ratesRegistry, [
            'preferred_choices' => ['nbs.median.EUR', 'nbs.median.CHF']
        ]);

        $resolver = new OptionsResolver();

        $type->configureOptions($resolver);

        $defaults = $resolver->resolve([]);

        $this->assertEquals([
            'choice_translation_domain' => 'runopencode_exchange_rate',
            'choices' => [
                'nbs.median.EUR' => 'nbs.median.EUR',
                'nbs.median.CHF' => 'nbs.median.CHF',
                'nbs.median.USD' => 'nbs.median.USD',
            ],
            'preferred_choices' => [
                'nbs.median.EUR',
                'nbs.median.CHF',
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

        $type = new RateType($ratesRegistry);

        $this->assertEquals(ChoiceType::class, $type->getParent());
    }
}
