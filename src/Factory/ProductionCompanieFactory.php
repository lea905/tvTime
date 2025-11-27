<?php

namespace App\Factory;

use App\Entity\ProductionCompanie;

class ProductionCompanieFactory
{
    public function createFromTmdbData(array $pcData): ProductionCompanie
    {
        $productionCompanie = new ProductionCompanie();
        return $productionCompanie
            ->setLogo($pcData['logo_path'] ?? '')
            ->setName($pcData['name'] ?? '')
            ->setOriginCountry($pcData['origin_country'] ?? '');
    }
}
