<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{

    public static function getGroups(): array
    {
        return ['category'];
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['label' => 'Business', 'code' => 'BUSINESS'],
            ['label' => 'E-commerce', 'code' => 'ECOM'],
            ['label' => 'Portfolio', 'code' => 'PORTFOLIO'],
            ['label' => 'Blog', 'code' => 'BLOG'],
            ['label' => 'Restaurant & Food', 'code' => 'RESTAURANT'],
            ['label' => 'Education', 'code' => 'EDUCATION'],
            ['label' => 'Health & Wellness', 'code' => 'HEALTH'],
            ['label' => 'Events & Conferences', 'code' => 'EVENTS'],
            ['label' => 'Technology & IT', 'code' => 'TECH'],
            ['label' => 'Fashion & Beauty', 'code' => 'FASHION'],
            ['label' => 'Travel & Tourism', 'code' => 'TRAVEL'],
            ['label' => 'Real Estate', 'code' => 'REALESTATE'],
            ['label' => 'Finance & Consulting', 'code' => 'FINANCE'],
            ['label' => 'Non-Profit & Charity', 'code' => 'NONPROFIT'],
            ['label' => 'Music & Entertainment', 'code' => 'MUSIC'],
            ['label' => 'Sports & Fitness', 'code' => 'SPORTS'],
            ['label' => 'Photography', 'code' => 'PHOTO'],
            ['label' => 'Art & Design', 'code' => 'ART'],
            ['label' => 'Automotive', 'code' => 'AUTO'],
            ['label' => 'Construction & Architecture', 'code' => 'CONSTRUCTION'],
            ['label' => 'Law & Legal Services', 'code' => 'LAW'],
            ['label' => 'Medical & Healthcare', 'code' => 'MEDICAL'],
            ['label' => 'Personal Blog', 'code' => 'P_BLOG'],
            ['label' => 'Wedding', 'code' => 'WEDDING'],
            ['label' => 'Gaming', 'code' => 'GAMING'],
            ['label' => 'News & Media', 'code' => 'NEWS'],
            ['label' => 'Online Courses', 'code' => 'COURSES'],
            ['label' => 'Agency & Marketing', 'code' => 'AGENCY'],
            ['label' => 'Startups & Innovation', 'code' => 'STARTUP'],
            ['label' => 'Kids & Family', 'code' => 'KIDS'],
            ['label' => 'Pet Services', 'code' => 'PETS'],
            ['label' => 'Agriculture & Farming', 'code' => 'AGRICULTURE'],
            ['label' => 'Science & Research', 'code' => 'SCIENCE'],
            ['label' => 'Community & Forum', 'code' => 'COMMUNITY'],
            ['label' => 'Job & Recruitment', 'code' => 'JOBS'],
        ];

        foreach ($categories as $data) {
            $category = new Category();
            $category->setLabel($data['label']);
            $category->setCode($data['code']);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
