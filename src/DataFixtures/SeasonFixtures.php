<?php


namespace App\DataFixtures;

use Faker;
use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('us_US');
        for ($i=0; $i<=50; $i++) {

            $season = new Season();
            $season->setNumber($faker->randomDigit);
            $season->setYear(rand(1990, 2020));
            $season->setDescription($faker->text);
            $season->setProgram($this->getReference('program_'. rand(1, count(ProgramFixtures::PROGRAMS))));
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }
        $manager->flush();

    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
