<?php

namespace App\DataFixtures;

use App\Entity\BusinessCustomerTarget;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class BusinessCustomerTargetFixtures extends Fixture implements FixtureGroupInterface
{

    public static function getGroups(): array
    {
        return ['business_customer_target'];
    }

    public function load(ObjectManager $manager): void
    {
        $targets = [
            ['code' => 'B2C', 'label' => 'Individual customers (B2C)'],
            ['code' => 'B2B', 'label' => 'Businesses and organizations (B2B)'],
            ['code' => 'STARTUPS', 'label' => 'Startups and entrepreneurs'],
            ['code' => 'STUDENTS', 'label' => 'Students and young professionals'],
            ['code' => 'PROFESSIONALS', 'label' => 'Freelancers and independent professionals'],
            ['code' => 'GOV', 'label' => 'Government and public sector'],
            ['code' => 'NONPROFIT', 'label' => 'Nonprofit and NGOs'],
            ['code' => 'INTERNATIONAL', 'label' => 'International customers'],
            ['code' => 'LOCAL', 'label' => 'Local community customers'],
            ['code' => 'ALL', 'label' => 'All customer types'],
        ];

        foreach ($targets as $data) {
            $target = new BusinessCustomerTarget();
            $target->setCode($data['code']);
            $target->setLabel($data['label']);
            $manager->persist($target);
        }

        $manager->flush();
    }
}
