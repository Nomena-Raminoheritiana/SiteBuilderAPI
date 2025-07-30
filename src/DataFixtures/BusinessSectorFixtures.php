<?php

namespace App\DataFixtures;

use App\Entity\BusinessSector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class BusinessSectorFixtures extends Fixture implements FixtureGroupInterface
{

    public static function getGroups(): array
    {
        return ['business_sector'];
    }

    public function load(ObjectManager $manager): void
    {
        $sectors = [
            // Technologie & Informatique
            ['code' => 'tech', 'label' => 'Technology & Software'],
            ['code' => 'it', 'label' => 'IT Services'],
            ['code' => 'web', 'label' => 'Web Development & Design'],
            ['code' => 'ai', 'label' => 'Artificial Intelligence'],

            // Commerce & Vente
            ['code' => 'ecom', 'label' => 'E-commerce'],
            ['code' => 'retail', 'label' => 'Retail & Consumer Goods'],
            ['code' => 'wholesale', 'label' => 'Wholesale'],

            // Santé & Bien-être
            ['code' => 'health', 'label' => 'Healthcare & Medical'],
            ['code' => 'pharma', 'label' => 'Pharmaceuticals'],
            ['code' => 'fitness', 'label' => 'Fitness & Wellness'],
            ['code' => 'beauty', 'label' => 'Beauty & Cosmetics'],

            // Éducation & Formation
            ['code' => 'edu', 'label' => 'Education & Training'],
            ['code' => 'online-courses', 'label' => 'Online Courses'],
            ['code' => 'coaching', 'label' => 'Coaching & Personal Development'],

            // Finance & Services Professionnels
            ['code' => 'fin', 'label' => 'Finance & Banking'],
            ['code' => 'accounting', 'label' => 'Accounting & Audit'],
            ['code' => 'insurance', 'label' => 'Insurance'],
            ['code' => 'consulting', 'label' => 'Consulting Services'],
            ['code' => 'law', 'label' => 'Legal Services'],

            // Tourisme & Hôtellerie
            ['code' => 'tour', 'label' => 'Tourism & Travel'],
            ['code' => 'hotel', 'label' => 'Hotels & Accommodation'],
            ['code' => 'restaurant', 'label' => 'Restaurants & Cafés'],

            // Art & Médias
            ['code' => 'media', 'label' => 'Media & Entertainment'],
            ['code' => 'design', 'label' => 'Graphic Design & Multimedia'],
            ['code' => 'music', 'label' => 'Music & Performing Arts'],
            ['code' => 'photography', 'label' => 'Photography'],

            // Immobilier
            ['code' => 'realestate', 'label' => 'Real Estate'],
            ['code' => 'architecture', 'label' => 'Architecture & Interior Design'],

            // Industrie & Fabrication
            ['code' => 'agri', 'label' => 'Agriculture'],
            ['code' => 'food', 'label' => 'Food & Beverages'],
            ['code' => 'manufacturing', 'label' => 'Manufacturing & Industry'],
            ['code' => 'construction', 'label' => 'Construction'],

            // ONG & Services publics
            ['code' => 'ngo', 'label' => 'Non-Profit & NGOs'],
            ['code' => 'gov', 'label' => 'Government & Public Services'],

            // Mode & Lifestyle
            ['code' => 'fashion', 'label' => 'Fashion & Apparel'],
            ['code' => 'jewelry', 'label' => 'Jewelry & Accessories'],
            ['code' => 'sports', 'label' => 'Sports & Recreation'],

            // Autres
            ['code' => 'events', 'label' => 'Events & Conferences'],
            ['code' => 'auto', 'label' => 'Automotive'],
            ['code' => 'other', 'label' => 'Other'],
        ];

        foreach ($sectors as $sectorData) {
            $sector = new BusinessSector();
            $sector->setCode($sectorData['code']);
            $sector->setLabel($sectorData['label']);

            $manager->persist($sector);
        }

        $manager->flush();
    }
}
