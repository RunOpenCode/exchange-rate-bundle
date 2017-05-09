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
use RunOpenCode\Bundle\ExchangeRate\Form\Type\RateTypeType;
use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Registry\RatesConfigurationRegistry;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RateTypeTypeTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Form\Type
 */
class RateTypeTypeTest extends TestCase
{
    /**
     * @test
     */
    public function itConfiguresDefaults()
    {
        $ratesRegistry = new RatesConfigurationRegistry([
            new Configuration('EUR', 'median', 'nbs'),
            new Configuration('EUR', 'buying', 'nbs'),
            new Configuration('EUR', 'selling', 'nbs'),
        ]);

        $type = new RateTypeType($ratesRegistry, [
            'preferred_choices' => ['median']
        ]);

        $resolver = new OptionsResolver();

        $type->configureOptions($resolver);

        $defaults = $resolver->resolve([]);

        $this->assertEquals([
            'choice_translation_domain' => 'runopencode_exchange_rate',
            'choices' => [
                'median' => 'median',
                'buying' => 'buying',
                'selling' => 'selling',
            ],
            'preferred_choices' => [
                'median'
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
            new Configuration('EUR', 'buying', 'nbs'),
            new Configuration('EUR', 'selling', 'nbs'),
        ]);

        $type = new RateTypeType($ratesRegistry);

        $this->assertEquals(ChoiceType::class, $type->getParent());
    }
}
