<?php


namespace App\DataFixtures;


use Faker;
use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{


    Const ACTORS = [
        'Andrew-lincoln',
        'Phill-collins',
        'Robert-patinson',
    ];

    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach (self::ACTORS as $name) {
            $actor = new Actor();
            $i++;
            $actor->setName($name);
            $manager->persist($actor);
            $actor->addProgram($this->getReference('program_' . $i));
            $this->addReference('actor_' . $i, $actor);
        }
        for ($i=0; $i<=50 ; $i++) {
            $actorFake = new Actor();
            $faker = Faker\Factory::create('us_US');
            $actorFake->addProgram($this->getReference('program_' . rand(1,5)));
            $actorFake->setName($faker->name);
            $manager->persist($actorFake);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }


}
