<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {}

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i=0; $i < 5; $i++)
        {
            $user = new User();
            $user->setEmail($faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->userPasswordHasher->hashPassword($user,'ArethiA75!'))
                ->setPseudo(random_int(0,1)===1 ? $faker->firstNameFemale():$faker->firstNameMale())
                ->setCreatedAt(new DateTimeImmutable());
            $manager->persist($user);
        }

        for($j=0; $j< 5; $j++)
        {
            $redactor = new User();
            $redactor->setEmail($faker->email())
                ->setPassword($this->userPasswordHasher->hashPassword($redactor,'ArethiA75!'))
                ->setPseudo(random_int(0,1)===1 ? $faker->firstNameMale:$faker->firstNameFemale)
                ->setRoles(['ROLE_REDACTOR'])
                ->setCreatedAt(new DateTimeImmutable());
            $manager->persist($redactor);
        }
        $manager->flush();
    }
}
