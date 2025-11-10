<?php

namespace App\Entity;
use App\Enum\emotion;

use App\Repository\ViewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ViewRepository::class)]
class View
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $see = null;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateSee = null;

    #[ORM\Column(enumType: emotion::class)]
    private ?emotion $emotion = null;

    /**
     * @var Collection<int, Episode>
     */
    #[ORM\ManyToMany(targetEntity: Episode::class, inversedBy: 'views')]
    private Collection $episodeId;

    /**
     * @var Collection<int, Movie>
     */
    #[ORM\ManyToMany(targetEntity: Movie::class, inversedBy: 'views')]
    private Collection $movieId;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'views')]
    private Collection $userId;

    public function __construct()
    {
        $this->episodeId = new ArrayCollection();
        $this->movieId = new ArrayCollection();
        $this->userId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdElement(): ?int
    {
        return $this->idElement;
    }

    public function setIdElement(int $idElement): static
    {
        $this->idElement = $idElement;

        return $this;
    }

    public function isSee(): ?bool
    {
        return $this->see;
    }

    public function setSee(bool $see): static
    {
        $this->see = $see;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getDateSee(): ?\DateTime
    {
        return $this->dateSee;
    }

    public function setDateSee(\DateTime $dateSee): static
    {
        $this->dateSee = $dateSee;

        return $this;
    }
    public function getEmotion(): ?emotion
    {
        return $this->emotion;
    }

    public function setEmotion(emotion $emotion): self
    {
        $this->emotion = $emotion;
        return $this;
    }

    /**
     * @return Collection<int, Episode>
     */
    public function getEpisodeId(): Collection
    {
        return $this->episodeId;
    }

    public function addElementId(Episode $elementId): static
    {
        if (!$this->episodeId->contains($elementId)) {
            $this->episodeId->add($elementId);
        }

        return $this;
    }

    public function removeElementId(Episode $elementId): static
    {
        $this->episodeId->removeElement($elementId);

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getMovieId(): Collection
    {
        return $this->movieId;
    }

    public function addMovieId(Movie $movieId): static
    {
        if (!$this->movieId->contains($movieId)) {
            $this->movieId->add($movieId);
        }

        return $this;
    }

    public function removeMovieId(Movie $movieId): static
    {
        $this->movieId->removeElement($movieId);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserId(): Collection
    {
        return $this->userId;
    }

    public function addUserId(User $userId): static
    {
        if (!$this->userId->contains($userId)) {
            $this->userId->add($userId);
        }

        return $this;
    }

    public function removeUserId(User $userId): static
    {
        $this->userId->removeElement($userId);

        return $this;
    }
}
