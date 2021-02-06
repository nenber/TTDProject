<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserServices
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager){
        $this->em = $entityManager;
    }

    /**
     * @param User $user
     * @return array
     */
    public function isValid(User $user, bool $exist = false): array
    {
        $errors = [];

        if (!$this->isValidName($user->getName())) {
            $errors["lastName"] = "Le nom doit être renseigné";
        }
        if (!$this->isValidSurname($user->getSurname())) {
            $errors["firstName"] = "Le prénom doit être renseigné";
        }
        if (!$this->isValidEmail($user->getEmail())) {
            $errors["email"] = "Votre email n'est pas valide";
        }
        if (!$this->isValidPassword($user->getPassword())) {
            $errors["password"] = "Votre mot de passe doit faire entre 8 et 40 caractères";
        }
        if (!$this->isValidBirthdate($user->getBirthdate())) {
            $errors["age"] = "Vous devez avoir plus de 13 ans";
        }

        if ($this->em->getRepository(User::class)->findOneBy(["email" => $user->getEmail()]) && $exist)
            $errors["exist"] = "Un compte existe déjà";

        return $errors;
    }


    private function isValidName(string $name): bool {
        return  (!is_null($name) && !empty($name));
    }

    private function isValidSurname(string $surname): bool {
        return  (!is_null($surname) && !empty($surname));
    }

    private function isValidEmail($inputEmail): bool {
        return filter_var($inputEmail, FILTER_VALIDATE_EMAIL);
    }

    private function isValidPassword($password): bool {
        return (strlen($password) > 8 && strlen($password) < 40);
    }

    private function isValidBirthdate(\DateTime $birthdate): bool
    {
        return date_diff($birthdate, new \DateTime())->format('%y') >= 13;
    }
}
