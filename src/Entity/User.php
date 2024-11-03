<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields:['email'],message: "Un autre utilisateur possède déjà cette adresse e-mail, merci de la modifier")]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email(message:"Veuillez renseigner une adresse email valide")]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\Length(min:6,max:255,minMessage:"Votre mot de passe doit faire plus de 6 caractères",maxMessage:"Votre mot de passe ne doit pas faire plus de 255 caractères")]
    #[Assert\Regex(pattern:'/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W_]).+$/',message:"Votre mot de passe doit contenir au minimum une majuscule, une minuscule, un chiffre et un caractère spécial")]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $password = null;

    #[Assert\EqualTo(propertyPath:"password", message:"Vous n'avez pas correctement confirmé votre mot de passe")]
    public ?string $passwordConfirm = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2,max:255,minMessage:"Le nom doit dépasser 2 caractères",maxMessage:"Le nom ne doit pas dépasser 255 caractères")]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:2,max:255,minMessage:"Votre prénom doit dépasser 2 caractères",maxMessage:"Votre prénoml ne doit pas dépasser 255 caractères")]
    #[Assert\NotBlank(message:"Ce champ ne peut pas être vide")]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?bool $checked = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column]
    private ?bool $familleAccueil = null;

    /**
     * @var Collection<int, Parrainage>
     */
    #[ORM\ManyToMany(targetEntity: Parrainage::class, mappedBy: 'user')]
    private Collection $parrainages;

    public function __construct()
    {
        $this->parrainages = new ArrayCollection();
    }

    /**
     * Permet d'initialiser le slug automatiquement
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if(empty($this->slug))
        {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->lastName."-".$this->firstName);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function isChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): static
    {
        $this->checked = $checked;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function isFamilleAccueil(): ?bool
    {
        return $this->familleAccueil;
    }

    public function setFamilleAccueil(bool $familleAccueil): static
    {
        $this->familleAccueil = $familleAccueil;

        return $this;
    }

    /**
     * @return Collection<int, Parrainage>
     */
    public function getParrainages(): Collection
    {
        return $this->parrainages;
    }

    public function addParrainage(Parrainage $parrainage): static
    {
        if (!$this->parrainages->contains($parrainage)) {
            $this->parrainages->add($parrainage);
            $parrainage->addUser($this);
        }

        return $this;
    }

    public function removeParrainage(Parrainage $parrainage): static
    {
        if ($this->parrainages->removeElement($parrainage)) {
            $parrainage->removeUser($this);
        }

        return $this;
    }
}
