<?php

namespace App\Utils;

class TmdbGenres
{
    public static array $MAP = [
        28     => 'Action',
        12     => 'Adventure',
        16     => 'Animation',
        35     => 'Comedy',
        80     => 'Crime',
        99     => 'Documentary',
        18     => 'Drama',
        10751  => 'Family',
        14     => 'Fantasy',
        36     => 'History',
        27     => 'Horror',
        10402  => 'Music',
        9648   => 'Mystery',
        10749  => 'Romance',
        878    => 'Science Fiction',
        10770  => 'TV Movie',
        53     => 'Thriller',
        10752  => 'War',
        37     => 'Western',
        10759  => 'Action & Adventure',
        10762  => 'Kids',
        10763  => 'News',
        10764  => 'Reality',
        10765  => 'Sci-Fi & Fantasy',
        10766  => 'Soap',
        10767  => 'Talk',
        10768  => 'War & Politics',
    ];

    public static function getName(int $id): string | null
    {
        return self::$MAP[$id] ?? null;
    }

    public static function fillDatas(array $data)
    {
        $map = [];

        foreach ($data['genres'] as $genre) {
            $map[$genre['id']] = $genre['name'];
        }
    }

    public static function getGenres(): array
    {
        return array_values(self::$MAP);
    }

    public static function addGenre(int $id, string $genre): void
    {
        self::$MAP[$id] = $genre;
    }

    public static function searchGenre(string $genreSearch): ?string
    {
        $normalizedSearch = self::normalize($genreSearch);

        foreach (self::$MAP as $label) {
            if (self::normalize($label) === $normalizedSearch) {
                return $label;
            }
        }
        return null;
    }

    private static function normalize(string $str): string
    {
        $str = mb_strtolower($str);
        $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        return preg_replace('/[^a-z0-9]/', '', $str);
    }

//    public static function getName(int $id): string {
//        return self::MAP[$id] ?? 'Inconnu';
//    }
//
//    public static function getGenres(): array {
//        $genres = [];
//        foreach (self::MAP as $genre => $label) {
//            $genres[] = $label;
//        }
//        return $genres;
//    }
//
//    public static function addGenre(int $id, string $genre): void
//    {
//        self::MAP[$id] = $genre;
//    }
//
//    public static function searchGenre(string $genreSearch): string | null {
//        foreach (self::MAP as $genre => $label) {
//            if ($genreSearch == $label) return $label;
//        }
//        return null;
//    }
}
