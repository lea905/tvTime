<?php

namespace App\Utils;

class TmdbGenres
{
    public const MAP = [
        28 => 'Action',
        12 => 'Aventure',
        16 => 'Animation',
        35 => 'Comédie',
        80 => 'Crime',
        99 => 'Documentaire',
        18 => 'Drame',
        10751 => 'Familial',
        14 => 'Fantastique',
        36 => 'Histoire',
        27 => 'Horreur',
        10402 => 'Musique',
        9648 => 'Mystère',
        10749 => 'Romance',
        878 => 'Science-Fiction',
        10770 => 'Téléfilm',
        53 => 'Thriller',
        10752 => 'Guerre',
        37 => 'Western',
    ];

    public static function getName(int $id): string {
        return self::MAP[$id] ?? 'Inconnu';
    }

    public static function getGenres(): array {
        $genres = [];
        foreach (self::MAP as $genre => $label) {
            $genres[] = $label;
        }
        return $genres;
    }

    public static function searchGenre(string $genreSearch): string | null {
        foreach (self::MAP as $genre => $label) {
            if ($genreSearch == $label) return $label;
        }
        return null;
    }
}
