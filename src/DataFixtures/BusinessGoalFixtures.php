<?php

namespace App\DataFixtures;

use App\Entity\BusinessGoal;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class BusinessGoalFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['business_goal'];
    }

    public function load(ObjectManager $manager): void
    {
        $goals = [
            ['code' => 'SALES', 'label' => 'Increase sales and revenue'],
            ['code' => 'AWARENESS', 'label' => 'Build brand awareness'],
            ['code' => 'CUSTOMERS', 'label' => 'Attract new customers'],
            ['code' => 'INFORMATION', 'label' => 'Provide information about products or services'],
            ['code' => 'SUPPORT', 'label' => 'Offer customer support and assistance'],
            ['code' => 'ONLINE_PRESENCE', 'label' => 'Establish an online presence'],
            ['code' => 'BOOKINGS', 'label' => 'Enable online bookings and reservations'],
            ['code' => 'E_COMMERCE', 'label' => 'Sell products online (E-commerce)'],
            ['code' => 'COMMUNITY', 'label' => 'Build a community around the brand'],
            ['code' => 'OTHERS', 'label' => 'Others'],
        ];

        foreach ($goals as $data) {
            $goal = new BusinessGoal();
            $goal->setCode($data['code']);
            $goal->setLabel($data['label']);
            $manager->persist($goal);
        }

        $manager->flush();
    }
}