<?php

// UserFixtures.php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setName("Name$i");
            $user->setFirstname("Firstname$i");
            $user->setAddress("Address $i");
            $user->setPhone("123456789$i");
            $user->setCard($this->generateLuhnCardNumber(16));
            $user->setCrypto("123");
            $user->setExpiry("12/25");
            
            $manager->persist($user);

            $this->addReference("user_$i", $user);
        }

        $adminUser = new User();
        $adminUser->setEmail("admin@gmail.com");
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'root'));
        $adminUser->setName("Admin");
        $adminUser->setFirstname("Root");
        $adminUser->setAddress("Address for admin");
        $adminUser->setPhone("987654321");
        $adminUser->setCard($this->generateLuhnCardNumber(16));
        $adminUser->setCrypto("321");
        $adminUser->setExpiry("12/25");

        $manager->persist($adminUser);
        
        $manager->flush();
    }

    private function generateLuhnCardNumber(int $length): string
    {
        $digits = '';
        $digits .= rand(1, 9);

        for ($i = 1; $i < $length - 1; $i++) {
            $digits .= rand(0, 9);
        }

        $sum = 0;
        $digitPosition = $length % 2;

        foreach (str_split(strrev($digits)) as $digit) {
            $sum += ($digitPosition++ % 2 === 0) ? $digit : array_sum(str_split((string) ($digit * 2)));
        }

        $checkDigit = (10 - ($sum % 10)) % 10; 

        return $digits . $checkDigit;
    }
}
