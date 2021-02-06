<?php

namespace App\Tests;

use App\Entity\User;
use App\Service\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /** @var UserServices  */
    protected $userService;

    protected function setUp(): void
    {
        static::bootKernel();
        $this->em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->userService = self::$kernel->getContainer()->get('app.service.user');
    }

    public function testUserSuccess()
    {
        $user = (new User("a@a.fr", "first","last","azertyuii", new \DateTime("1997-01-01")));

        self::assertEquals([], $this->userService->isValid($user));

        if (!$this->em->getRepository(User::class)->findOneBy(["email" => $user->getEmail()]))
            $this->em->persist($user);
            $this->em->flush();

    }

    public function testUserAlreadyExist()
    {
        $user = (new User("a@a.fr", "first","last","azertyuii", new \DateTime("1997-01-01")));

        self::assertEquals(["exist" => "Un compte existe déjà"],  $this->userService->isValid($user, true));
    }

    public function testUserTooYoung()
    {
        $user = (new User("a@a.fr", "first","last","azertyuii", new \DateTime("2010-01-01")));

        self::assertEquals(["age" => "Vous devez avoir plus de 13 ans"],  $this->userService->isValid($user));
    }

    public function testUserEmailNotValid()
    {
        $user = (new User("a@.fr", "first","last","azertyuii", new \DateTime("1997-01-01")));

        self::assertEquals(["email" => "Votre email n'est pas valide"],  $this->userService->isValid($user));
    }

    public function testUserPasswordTooShort()
    {
        $user = (new User("a@a.fr", "first","last","aztyui", new \DateTime("1997-01-01")));
        self::assertEquals(["password" => "Votre mot de passe doit faire entre 8 et 40 caractères"],  $this->userService->isValid($user));
    }

    public function testUserPasswordTooLong()
    {
        $user = (new User("a@a.fr", "first","last","azertyuii111111111111111111111111555555555", new \DateTime("1997-01-01")));
        self::assertEquals(["password" => "Votre mot de passe doit faire entre 8 et 40 caractères"],  $this->userService->isValid($user));
    }

    public function testUserFirstNameEmpty()
    {
        $user = (new User("a@a.fr", "first","","azertyuii", new \DateTime("1997-01-01")));
        self::assertEquals(["firstName" => "Le prénom doit être renseigné"],  $this->userService->isValid($user));
    }

    public function testUserLastNameEmpty()
    {
        $user = (new User("a@a.fr", "","last","azertyuii", new \DateTime("1997-01-01")));

        self::assertEquals(["lastName" => "Le nom doit être renseigné"],  $this->userService->isValid($user));
    }
}
