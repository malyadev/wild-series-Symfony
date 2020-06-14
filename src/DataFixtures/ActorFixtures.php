<?php


namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;


class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Andrew Lincoln' => [
            'programs' => [
                'program_0',
                'program_5',
            ]
        ],
        'Norman Reedus' => [
            'programs' => [
                'program_0',
            ]
        ],
        'Lauren Cohan' => [
            'programs' => [
                'program_0',
            ]
        ],
        'Danai Gurira' => [
            'programs' => [
                'program_0',
            ],
        ],
    ];


    public function load(ObjectManager $manager)
    {
        foreach (self::ACTORS as $name => $data) {
            $actor = new Actor();
            $actor->setName($name);
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            foreach ($data['programs'] as $program) {
                $actor->addProgram($this->getReference($program));
            }
            $manager->persist($actor);
        }

        $faker  =  Faker\Factory::create('en_US');
        for ($i=0; $i < 20; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name);
            for ($j = 0; $j < 3; $j++) {
                $name = 'program_' . rand(0, 5);
                $actor->addProgram($this->getReference($name));
                $manager->persist($actor);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
