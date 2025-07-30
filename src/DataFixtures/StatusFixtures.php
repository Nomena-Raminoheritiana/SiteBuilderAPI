<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class StatusFixtures extends Fixture implements FixtureGroupInterface
{
    private array $STATUS_DATA = [
        [
            'name' => 'Published',
            'code' => 'published'
        ],
        [
            'name' => 'Draft',
            'code' => 'draft'
        ],
        [
            'name' => 'Archived',
            'code' => 'archived'
        ]
    ];

    public static function getGroups(): array
    {
        return ['status'];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->STATUS_DATA as $value) {
            $status = new Status();
            $status->setName($value['name']);
            $status->setCode($value['code']);
            $manager->persist($status);
        }

        $manager->flush();
    }
}
