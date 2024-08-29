<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class CheckedUser implements UserCheckerInterface{
    public function checkPreAuth(UserInterface $user):void
    {
        if (!$user instanceof User) {
            return;
        }
    }

    public function checkPostAuth(UserInterface $user):void
    {
        if (!$user instanceof User) {
            return;
        }
        if(!$user->isChecked()){
            throw new CustomUserMessageAuthenticationException('Veuillez confirmer votre email');
        }
    }
}   