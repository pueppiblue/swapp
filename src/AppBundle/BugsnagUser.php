<?php

namespace AppBundle;

use Evolution7\BugsnagBundle\UserInterface as BugsnagUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

class BugsnagUser implements BugsnagUserInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $token;

    /**
     * @param TokenStorageInterface $token
     */
    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token->getToken();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserAsArray()
    {
        if (
            is_null($this->token)
            || !$this->token->isAuthenticated()
            || !$this->token->getUser() instanceof SymfonyUserInterface
        ) {
            return [];
        }

        $user = $this->token->getUser();

        return [
            'id' => $user->getId(),
            'name' => $user->getUsername(),
            'email' => $user->getEmail(),
        ];
    }
}
