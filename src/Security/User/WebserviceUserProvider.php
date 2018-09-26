<?php

namespace App\Security\User;

use App\Manager\ThingsManager;
use App\Security\User\WebserviceUser;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class WebserviceUserProvider implements UserProviderInterface
{
    public $thingsManager;

    public function __construct(ThingsManager $thingsManager)
    {
        $this->thingsManager = $thingsManager;
    }

    public function loadUserByUsername($username)
    {
        return $this->fetchUser($username);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        $username = $user->getUsername();

        return $this->fetchUser($username);
    }

    public function supportsClass($class)
    {
        return WebserviceUser::class === $class;
    }

    private function fetchUser($username)
    {
        // make a call to your webservice here
#        $userData = ...
        $mh = $this->thingsManager->getMysqlHelper();
        $sql = "SELECT * FROM users WHERE username='" . $mh->quote($username) . "'";
        $userData = $mh->getOneAssocArray($sql);
        // pretend it returns an array on success, false if there is no user

        if ($userData) {
            return new WebserviceUser(
                $username,
                $userData['password'],
                '',
                explode(',', $userData['roles'])
            );
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }
}
