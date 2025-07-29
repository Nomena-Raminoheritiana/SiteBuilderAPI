<?php

namespace App\DataFixtures;

use App\Entity\BusinessLegalStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class BusinessLegaStatusFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups(): array
    {
        return ['business_legal_status'];
    }

    public function load(ObjectManager $manager): void
    {
        $statuses = [
            ['code' => 'SARL', 'label' => 'Limited Liability Company (SARL)'],
            ['code' => 'SARLU', 'label' => 'Single-Member Limited Liability Company (SARLU)'],
            ['code' => 'SA', 'label' => 'Public Limited Company (SA)'],
            ['code' => 'SAS', 'label' => 'Simplified Joint Stock Company (SAS)'],
            ['code' => 'SASU', 'label' => 'Single-Member Simplified Joint Stock Company (SASU)'],
            ['code' => 'EI', 'label' => 'Sole Proprietorship'],
            ['code' => 'EURL', 'label' => 'Single-Member Private Limited Company (EURL)'],
            ['code' => 'LLC', 'label' => 'Limited Liability Company (LLC)'],
            ['code' => 'LTD', 'label' => 'Private Limited Company (LTD)'],
            ['code' => 'OTHERS', 'label' => 'Others'],
        ];

        foreach ($statuses as $statusData) {
            $status = new BusinessLegalStatus();
            $status->setCode($statusData['code']);
            $status->setLabel($statusData['label']);
            $manager->persist($status);
        }

        $manager->flush();
    }
}
