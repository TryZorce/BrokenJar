<?php

// FineFixtures.php

namespace App\DataFixtures;

use App\Entity\Fine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\UserFixtures;


class FineFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $fine = new Fine();
            $fine->setEmail($this->getReference("user_$i"));
            $fine->setCode($this->generateValidFineCode($i)); // Utilisation de la méthode pour générer le code
            $fine->setName("Fine $i");
            $fine->setDescription("Description of fine $i");
            $fine->setValue(100.0 + $i);
            $fine->setPay(false);

            $manager->persist($fine);
        }

        $manager->flush();
    }

    private function generateValidFineCode(int $index): string
    {
        $letters = ['A', 'B', 'C', 'D', 'E'];
        shuffle($letters);

        sort($letters);

        $part1 = rand(1, 99);
        $part2 = 100 - $part1; 

        $code = $letters[0] . $letters[1] . '2024_' . $part1 . '_' . $part2;

        return $code;
    }

    
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}