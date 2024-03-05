<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'There is already an account with this id')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 20)]
    private ?string $login = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\OneToMany(targetEntity: Connection::class, mappedBy: 'user1')]
    private Collection $connection1;

    #[ORM\OneToMany(targetEntity: Connection::class, mappedBy: 'user2')]
    private Collection $connections;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user')]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Ressource::class, mappedBy: 'likes')]
    private Collection $liked;

    #[ORM\ManyToMany(targetEntity: Ressource::class, mappedBy: 'saves')]
    private Collection $saved;

    #[ORM\ManyToMany(targetEntity: Ressource::class, mappedBy: 'views')]
    private Collection $viewed;

    #[ORM\OneToMany(targetEntity: Ressource::class, mappedBy: 'user')]
    private Collection $ressources;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messageSender;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'receiver')]
    private Collection $messageReceiver;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deactivationDate = null;

    public function __construct()
    {
        $this->connection1 = new ArrayCollection();
        $this->connections = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->liked = new ArrayCollection();
        $this->saved = new ArrayCollection();
        $this->viewed = new ArrayCollection();
        $this->ressources = new ArrayCollection();
        $this->messageSender = new ArrayCollection();
        $this->messageReceiver = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
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

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Connection>
     */
    public function getConnection1(): Collection
    {
        return $this->connection1;
    }

    public function addConnection1(Connection $connection1): static
    {
        if (!$this->connection1->contains($connection1)) {
            $this->connection1->add($connection1);
            $connection1->setUser1($this);
        }

        return $this;
    }

    public function removeConnection1(Connection $connection1): static
    {
        if ($this->connection1->removeElement($connection1)) {
            // set the owning side to null (unless already changed)
            if ($connection1->getUser1() === $this) {
                $connection1->setUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Connection>
     */
    public function getConnections(): Collection
    {
        return $this->connections;
    }

    public function addConnection(Connection $connection): static
    {
        if (!$this->connections->contains($connection)) {
            $this->connections->add($connection);
            $connection->setUser2($this);
        }

        return $this;
    }

    public function removeConnection(Connection $connection): static
    {
        if ($this->connections->removeElement($connection)) {
            // set the owning side to null (unless already changed)
            if ($connection->getUser2() === $this) {
                $connection->setUser2(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getLiked(): Collection
    {
        return $this->liked;
    }

    public function addLiked(Ressource $liked): static
    {
        if (!$this->liked->contains($liked)) {
            $this->liked->add($liked);
            $liked->addLike($this);
        }

        return $this;
    }

    public function removeLiked(Ressource $liked): static
    {
        if ($this->liked->removeElement($liked)) {
            $liked->removeLike($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getSaved(): Collection
    {
        return $this->saved;
    }

    public function addSaved(Ressource $saved): static
    {
        if (!$this->saved->contains($saved)) {
            $this->saved->add($saved);
            $saved->addSave($this);
        }

        return $this;
    }

    public function removeSaved(Ressource $saved): static
    {
        if ($this->saved->removeElement($saved)) {
            $saved->removeSave($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getViewed(): Collection
    {
        return $this->viewed;
    }

    public function addViewed(Ressource $viewed): static
    {
        if (!$this->viewed->contains($viewed)) {
            $this->viewed->add($viewed);
            $viewed->addView($this);
        }

        return $this;
    }

    public function removeViewed(Ressource $viewed): static
    {
        if ($this->viewed->removeElement($viewed)) {
            $viewed->removeView($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessource(Ressource $ressource): static
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources->add($ressource);
            $ressource->setUser($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): static
    {
        if ($this->ressources->removeElement($ressource)) {
            // set the owning side to null (unless already changed)
            if ($ressource->getUser() === $this) {
                $ressource->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSender(): Collection
    {
        return $this->messageSender;
    }

    public function addSender(Message $messageSender): static
    {
        if (!$this->messageSender->contains($messageSender)) {
            $this->messageSender->add($messageSender);
            $messageSender->setSender($this);
        }

        return $this;
    }

    public function removeSender(Message $messageSender): static
    {
        if ($this->messageSender->removeElement($messageSender)) {
            // set the owning side to null (unless already changed)
            if ($messageSender->getSender() === $this) {
                $messageSender->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessageReceiver(): Collection
    {
        return $this->messageReceiver;
    }

    public function addMessageReceiver(Message $messageReceiver): static
    {
        if (!$this->messageReceiver->contains($messageReceiver)) {
            $this->messageReceiver->add($messageReceiver);
            $messageReceiver->setReceiver($this);
        }

        return $this;
    }

    public function removeMessageReceiver(Message $messageReceiver): static
    {
        if ($this->messageReceiver->removeElement($messageReceiver)) {
            // set the owning side to null (unless already changed)
            if ($messageReceiver->getReceiver() === $this) {
                $messageReceiver->setReceiver(null);
            }
        }

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getDeactivationDate(): ?\DateTimeInterface
    {
        return $this->deactivationDate;
    }

    public function setDeactivationDate(?\DateTimeInterface $deactivationDate): static
    {
        $this->deactivationDate = $deactivationDate;

        return $this;
    }

}
