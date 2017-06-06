<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Security;

use RunOpenCode\ExchangeRate\Contract\RateInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\Role;

/**
 * Class AccessVoter
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Security
 */
class AccessVoter extends Voter
{
    /**
     * View action for exchange rate
     */
    const VIEW = 'view';

    /**
     * Create new exchange rate action
     */
    const CREATE = 'create';

    /**
     * Edit exchange rate action
     */
    const EDIT = 'edit';

    /**
     * Delete exchange rate action
     */
    const DELETE = 'delete';

    /**
     * @var array
     */
    protected $roles;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * AccessVoter constructor.
     *
     * @param array $roles
     *
     * @codeCoverageIgnore
     */
    public function __construct(array $roles = [], $enabled = true)
    {
        $this->roles = array_merge([
            self::VIEW => [],
            self::CREATE => [],
            self::EDIT => [],
            self::DELETE => [],
        ], $roles);

        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        $attribute = strtolower($attribute);

        if ($subject instanceof RateInterface && in_array($attribute, [ self::EDIT, self::DELETE, self::VIEW ], true)) {
            return true;
        }

        if (
            in_array($attribute, [ self::VIEW, self::CREATE], true)
            &&
            is_string($subject)
        ) {
            return (new \ReflectionClass($subject))->implementsInterface(RateInterface::class);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!$this->enabled) {
            return true;
        }

        $attribute = strtolower($attribute);

        return $this->hasAnyRole($token, $this->roles[$attribute]);
    }

    /**
     * Check if token has any of given roles.
     *
     * @param TokenInterface $token
     * @param array $roles
     *
     * @return bool
     */
    private function hasAnyRole(TokenInterface $token, array $roles)
    {
        $tokenRoles = array_filter(array_map(function(Role $role) {
            return $role->getRole() ?: false;
        }, $token->getRoles()));

        foreach ($tokenRoles as $tokenRole) {
            foreach ($roles as $role) {
                if (strtolower($tokenRole) === strtolower($role)) {
                    return true;
                }
            }
        }

        return false;
    }
}
