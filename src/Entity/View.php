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

    #[ORM\Column(type: 'json', nullable: true)]
    private array $emotions = [];

    /**
     * @var Collection<int, Series>
     */
    #[ORM\ManyToMany(targetEntity: Series::class, inversedBy: 'views')]
    private Collection $seriesId;

    /**
     * @var Collection<int, Movie>
     */
    #[ORM\ManyToMany(targetEntity: Movie::class, inversedBy: 'views')]
    private Collection $movieId;

    #[ORM\ManyToOne(inversedBy: 'views')]
    private ?User $userId = null;

    public function __construct()
    {
        $this->seriesId = new ArrayCollection();
        $this->movieId = new ArrayCollection();
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

    /**
     * @return emotion[]
     */
    public function getEmotions(): array
    {
        // Convert stored strings back to Enum if needed, or just return array
        // If storing as simple strings in JSON:
        return array_map(fn($e) => $e instanceof emotion ? $e : emotion::tryFrom($e), $this->emotions);
    }

    public function setEmotions(array $emotions): self
    {
        // Ensure we store consistent data (e.g. enum values or strings)
        // Doctrine JSON type handles arrays nicely.
        $this->emotions = $emotions;
        return $this;
    }

    public function addEmotion(emotion $emotion): self
    {
        if (!in_array($emotion, $this->emotions)) {
            $this->emotions[] = $emotion;
        }
        return $this;
    }

    public function removeEmotion(emotion $emotion): self
    {
        $key = array_search($emotion, $this->emotions);
        if ($key !== false) {
            unset($this->emotions[$key]);
            $this->emotions = array_values($this->emotions);
        }
        return $this;
    }

    /**
     * @return Collection<int, Series>
     */
    public function getSeriesId(): Collection
    {
        return $this->seriesId;
    }

    public function addSeriesId(Series $series): static
    {
        if (!$this->seriesId->contains($series)) {
            $this->seriesId->add($series);
        }

        return $this;
    }

    public function addElementId(Series $elementId): static
    {
        return $this->addSeriesId($elementId);
    }

    public function removeElementId(Series $elementId): static
    {
        $this->seriesId->removeElement($elementId);

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

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }
}
