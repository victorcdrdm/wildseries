<?php


namespace App\DataFixtures;


use App\Entity\Season;
use App\Service\Slugify;
use Faker;
use App\Entity\Episode;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('us_US');
        for ($i=0; $i<=200 ; $i++)
        {
            $episode = new Episode();
            $episode->setNumber($faker->randomDigit);
            $episode->setTitle($faker->name);
            $episode->setSynopsis($faker->text);
            $episode->setSeason($this->getReference('season_' . rand(1, 50)));
            $slug = $this->slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $manager->persist($episode);
            $this->addReference('episode_' . $i, $episode);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }

}
