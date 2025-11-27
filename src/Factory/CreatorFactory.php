<?php

namespace App\Factory;

use App\Entity\Creator;

class CreatorFactory
{
    public function createFromTmdbData(array $creatorData): Creator
    {
        $creator = new Creator();
        return $creator
            ->setName($creatorData['name'] ?? '');
    }
}
