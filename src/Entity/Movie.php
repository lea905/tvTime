<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    #[ORM\Column(length: 255)]
    private ?string $genre = null;

    #[ORM\Column(length: 255)]
    private ?string $resume = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $releaseDate = null;

    /**
     * @var Collection<int, View>
     */
    #[ORM\ManyToMany(targetEntity: View::class, mappedBy: 'movieId')]
    private Collection $views;

    /**
     * @var Collection<int, ProductionCompanie>
     */
    #[ORM\ManyToMany(targetEntity: ProductionCompanie::class, inversedBy: 'movies')]
    private Collection $productionCompanies;

    /**
     * @var Collection<int, WatchList>
     */
    #[ORM\ManyToMany(targetEntity: WatchList::class, mappedBy: 'movies')]
    private Collection $watchLists;

    public function __construct()
    {
        $this->views = new ArrayCollection();
        $this->productionCompanies = new ArrayCollection();
        $this->watchLists = new ArrayCollection();
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

    public function getIdWatchList(): ?int
    {
        return $this->idWatchList;
    }

    public function setIdWatchList(int $idWatchList): static
    {
        $this->idWatchList = $idWatchList;

        return $this;
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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return Collection<int, View>
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(View $view): static
    {
        if (!$this->views->contains($view)) {
            $this->views->add($view);
            $view->addMovieId($this);
        }

        return $this;
    }

    public function removeView(View $view): static
    {
        if ($this->views->removeElement($view)) {
            $view->removeMovieId($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductionCompanie>
     */
    public function getProductionCompanies(): Collection
    {
        return $this->productionCompanies;
    }

    public function addProductionCompany(ProductionCompanie $productionCompany): static
    {
        if (!$this->productionCompanies->contains($productionCompany)) {
            $this->productionCompanies->add($productionCompany);
        }

        return $this;
    }

    public function removeProductionCompany(ProductionCompanie $productionCompany): static
    {
        $this->productionCompanies->removeElement($productionCompany);

        return $this;
    }

    /**
     * @return Collection<int, WatchList>
     */
    public function getWatchLists(): Collection
    {
        return $this->watchLists;
    }

    public function addWatchList(WatchList $watchList): static
    {
        if (!$this->watchLists->contains($watchList)) {
            $this->watchLists->add($watchList);
            $watchList->addMovie($this);
        }

        return $this;
    }

    public function removeWatchList(WatchList $watchList): static
    {
        if ($this->watchLists->removeElement($watchList)) {
            $watchList->removeMovie($this);
        }

        return $this;
    }
}
