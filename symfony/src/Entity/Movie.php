<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\QueryCollection;



/**
 *  Secured resource.
 */
#[ApiResource(
    operations: [
        new Get(),
        new Post(security: "is_granted('ROLE_ADMIN')", securityMessage: 'Only admins can add books.'),
        new GetCollection(),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
    ],
    paginationType: 'page',
    graphQlOperations: [
        new Query(),
        new QueryCollection(),
        new DeleteMutation(security: "is_granted('ROLE_ADMIN')", name: 'delete'),
        new Mutation(security: "is_granted('ROLE_ADMIN')", name: 'create'),
        new Mutation(security: "is_granted('ROLE_ADMIN')", name: 'update'),
    ]
)]
#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial'])]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\ManyToMany(targetEntity: Actor::class, inversedBy: 'movies')]
    private Collection $actors;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Type('datetime')]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $boxOffice = null;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'movies')]
    private Collection $categories;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $duration = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Assert\Range(
     *      min = 0,
     *      max = 10,
     *      minMessage = "La valeur doit être au moins {{ limit }}.",
     *      maxMessage = "La valeur ne peut pas dépasser {{ limit }}."
     * )
     */
    private ?int $note = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive]
    private ?int $budget = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $director = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'L\'url {{ value }} is not a valid url')]
    private ?string $website = null;

    #[ORM\ManyToMany(targetEntity: MediaObject::class, inversedBy: 'movies')]
    private Collection $mediaobject;

    public function __construct()
    {
        $this->actors = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->mediaobject = new ArrayCollection();
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

    /**
     * @return Collection<int, Actor>
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(Actor $actor): static
    {
        if (!$this->actors->contains($actor)) {
            $this->actors->add($actor);
        }

        return $this;
    }

    public function removeActor(Actor $actor): static
    {
        $this->actors->removeElement($actor);

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getBoxOffice(): ?int
    {
        return $this->boxOffice;
    }

    public function setBoxOffice(?int $boxOffice): static
    {
        $this->boxOffice = $boxOffice;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addMovie($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeMovie($this);
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getBudget(): ?int
    {
        return $this->budget;
    }

    public function setBudget(?int $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(string $director): static
    {
        $this->director = $director;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return Collection<int, MediaObject>
     */
    public function getMediaobject(): Collection
    {
        return $this->mediaobject;
    }

    public function addMediaobject(MediaObject $mediaobject): static
    {
        if (!$this->mediaobject->contains($mediaobject)) {
            $this->mediaobject->add($mediaobject);
        }

        return $this;
    }

    public function removeMediaobject(MediaObject $mediaobject): static
    {
        $this->mediaobject->removeElement($mediaobject);

        return $this;
    }
}
