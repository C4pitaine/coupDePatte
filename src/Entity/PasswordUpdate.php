<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{
    #[Assert\NotBlank(message:"Vous devez renseigner votre ancien mot de passe")]
    private ?string $oldPassword = null;

    #[Assert\Length(min:6,max:255,minMessage:"Votre mot de passe doit faire plus de 6 caractères",maxMessage:"Votre mot de passe ne doit pas faire plus de 255 caractères")]
    #[Assert\Regex(pattern:'/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W_]).+$/',message:"Votre mot de passe doit contenir au minimum une majuscule, une minuscule, un chiffre et un caractère spécial")]
    private ?string $newPassword = null;

    #[Assert\EqualTo(propertyPath:"newPassword", message: "Vous n'avez pas correctement confirmé votre mot de passe")]
    private ?string $confirmPassword = null;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): static
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): static
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): static
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
