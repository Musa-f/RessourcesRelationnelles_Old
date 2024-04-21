<?php

namespace App\Entity;

use App\Repository\RessourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RessourceRepository::class)]
class Ressource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("resource.index")]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups("resource.index")]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("resource.index")]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column]
    private ?int $visibility = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 255)]
    #[Groups("resource.index")]
    private ?string $type = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'ressource')]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'ressource')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("resource.index")]
    private ?Format $format = null;

    #[ORM\ManyToOne(inversedBy: 'ressource')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("resource.index")]
    private ?Category $category = null;

    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'ressource')]
    #[Groups("resource.index")]
    private Collection $files;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'liked')]
    #[ORM\JoinTable(name: "likes")]
    private Collection $likes;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'saved')]
    #[ORM\JoinTable(name: "saves")]
    private Collection $saves;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'viewed')]
    #[ORM\JoinTable(name: "views")]
    private Collection $views;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'shared')]
    #[ORM\JoinTable(name: "shares")]
    private Collection $shares;

    #[ORM\ManyToOne(inversedBy: 'ressources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $publicationDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups("resource.index")]
    private ?string $content = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->saves = new ArrayCollection();
        $this->views = new ArrayCollection();
        $this->shares = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getVisibility(): ?int
    {
        return $this->visibility;
    }

    public function setVisibility(int $visibility): static
    {
        $this->visibility = $visibility;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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
            $comment->setRessource($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getRessource() === $this) {
                $comment->setRessource(null);
            }
        }

        return $this;
    }

    public function getFormat(): ?Format
    {
        return $this->format;
    }

    public function setFormat(?Format $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setRessource($this);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getRessource() === $this) {
                $file->setRessource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }

        return $this;
    }

    public function removeLike(User $like): static
    {
        $this->likes->removeElement($like);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getSaves(): Collection
    {
        return $this->saves;
    }

    public function addSave(User $save): static
    {
        if (!$this->saves->contains($save)) {
            $this->saves->add($save);
        }

        return $this;
    }

    public function removeSave(User $save): static
    {
        $this->saves->removeElement($save);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(User $view): static
    {
        if (!$this->views->contains($view)) {
            $this->views->add($view);
        }

        return $this;
    }

    public function removeView(User $view): static
    {
        $this->views->removeElement($view);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getShares(): Collection
    {
        return $this->shares;
    }

    public function addShare(User $share): static
    {
        if (!$this->shares->contains($share)) {
            $this->shares->add($share);
        }

        return $this;
    }

    public function removeShare(User $share): static
    {
        $this->shares->removeElement($share);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTimeInterface $publicationDate): static
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

   

}
