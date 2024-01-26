<?php
// src/Command/GenerateDataCommand.php

namespace App\Command;

use App\Entity\Actor;
use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xylis\FakerCinema\Provider\Person as CinemaPerson;
use Xylis\FakerCinema\Provider\Movie as CinemaMovie;


class GenerateDataCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setName('app:generate-data')
            ->setDescription('Generate fake data for actors and movies');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = Factory::create();
        $faker->addProvider(new CinemaPerson($faker));
        $faker->addProvider(new CinemaMovie($faker));

        for ($i = 0; $i < 100; $i++) {
            $lastName = $faker->unique()->lastName;
            $firstName = $faker->unique()->firstName;

            $actor = new Actor();
            $actor->setLastname($lastName);
            $actor->setFirstname($firstName);
            $actor->setDob($faker->dateTimeThisCentury());
            $actor->setReward($faker->word); // Ajout d'une récompense fictive
            $actor->setNationality($faker->country); // Ajout d'une nationalité fictive

            $actor->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($actor);
        }

        $faker->unique($reset = true); // Réinitialiser le fournisseur unique

        for ($i = 0; $i < 180; $i++) {
            $movieName = $faker->unique()->movie;
        
            $movie = new Movie();
            $movie->setName($movieName);
            $movie->setReleaseDate(\DateTimeImmutable::createFromMutable($faker->dateTimeThisDecade())); // Modification ici
            $movie->setGrade($faker->randomFloat(1, 1, 10));
            $movie->setDescription($faker->sentence);
        
            $actors = $this->entityManager->getRepository(Actor::class)->findAll();
        
            if (!empty($actors)) {
                $randomActors = $faker->randomElements($actors, $faker->numberBetween(1, 5));
        
                foreach ($randomActors as $actor) {
                    $movie->addActor($actor);
                }
            }
        
            $this->entityManager->persist($movie);
        }

        $this->entityManager->flush();

        $output->writeln('Data generation completed.');

        return Command::SUCCESS;
    }
}
