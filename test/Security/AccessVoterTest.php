<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Security;

use PHPUnit\Framework\TestCase;
use RunOpenCode\Bundle\ExchangeRate\Security\AccessVoter;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Class AccessVoterTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Security
 */
class AccessVoterTest extends TestCase
{
    /**
     * @test
     */
    public function itGrantsAccess()
    {
        $voter = new AccessVoter([
            AccessVoter::VIEW => ['ROLE_VIEW_RATE'],
            AccessVoter::CREATE => ['ROLE_CREATE_RATE'],
            AccessVoter::EDIT => ['ROLE_EDIT_RATE'],
            AccessVoter::DELETE => ['ROLE_DELETE_RATE'],
        ]);

        $rate = $this->getMockBuilder(RateInterface::class)->getMock();

        $inputs = [
            ['EDIT', $rate, 'ROLE_EDIT_RATE'],
            ['DELETE', $rate, 'ROLE_DELETE_RATE'],
            ['VIEW', $rate, 'ROLE_VIEW_RATE'],
            ['VIEW', get_class($rate), 'ROLE_VIEW_RATE'],
            ['CREATE', get_class($rate), 'ROLE_CREATE_RATE'],
        ];

        foreach ($inputs as $input) {

            list($attribute, $subject, $role) = $input;

            $token = $this->getMockBuilder(TokenInterface::class)->getMock();
            $roleInterface = $this->getMockBuilder(RoleInterface::class)->getMock();

            $token
                ->method('getRoles')
                ->willReturn([$roleInterface]);

            $roleInterface
                ->method('getRole')
                ->willReturn($role);

            $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $subject, [$attribute]));
        }
    }

    /**
     * @test
     */
    public function itDeniesAccess()
    {
        $voter = new AccessVoter([
            AccessVoter::VIEW => ['ROLE_VIEW_RATE'],
            AccessVoter::CREATE => ['ROLE_CREATE_RATE'],
            AccessVoter::EDIT => ['ROLE_EDIT_RATE'],
            AccessVoter::DELETE => ['ROLE_DELETE_RATE'],
        ]);

        $rate = $this->getMockBuilder(RateInterface::class)->getMock();

        $inputs = [
            ['EDIT', $rate, 'ROLE_ADMIN'],
            ['DELETE', $rate, 'ROLE_ADMIN'],
            ['VIEW', $rate, 'ROLE_ADMIN'],
            ['VIEW', get_class($rate), 'ROLE_ADMIN'],
            ['CREATE', get_class($rate), 'ROLE_ADMIN'],
        ];

        foreach ($inputs as $input) {

            list($attribute, $subject, $role) = $input;

            $token = $this->getMockBuilder(TokenInterface::class)->getMock();
            $roleInterface = $this->getMockBuilder(RoleInterface::class)->getMock();

            $token
                ->method('getRoles')
                ->willReturn([$roleInterface]);

            $roleInterface
                ->method('getRole')
                ->willReturn($role);

            $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $subject, [$attribute]));
        }
    }

    /**
     * @test
     */
    public function itAbstainsAccess()
    {
        $voter = new AccessVoter([
            AccessVoter::VIEW => ['ROLE_VIEW_RATE'],
            AccessVoter::CREATE => ['ROLE_CREATE_RATE'],
            AccessVoter::EDIT => ['ROLE_EDIT_RATE'],
            AccessVoter::DELETE => ['ROLE_DELETE_RATE'],
        ]);

        $rate = $this->getMockBuilder(RateInterface::class)->getMock();

        $inputs = [
            ['ROLE_USER', $rate],
            ['VIEW', new \stdClass()],
        ];

        foreach ($inputs as $input) {

            list($attribute, $subject) = $input;

            $token = $this->getMockBuilder(TokenInterface::class)->getMock();

            $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $voter->vote($token, $subject, [$attribute]));
        }
    }
}
