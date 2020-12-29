<?php

namespace App\Entity;


use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $password;


    /**
     * @ORM\OneToOne(targetEntity=ToDoList::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $toDoList;


    function __construct($email, $name, $surname, $password, $birthdate)
    {
        $this->setToDoList(new ToDoList());

        $this->setEmail($email);
        $this->setName($name);
        $this->setSurname($surname);
        $this->setPassword($password);
        $this->setBirthdate($birthdate);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getToDoList(): ?object
    {
        return $this->toDoList;
    }

    public function setToDoList(object $toDoList): self
    {
        $this->toDoList = $toDoList;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $pass): ?string
    {
        $this->password = $pass;
        return $this->password;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthdate(): ?string
    {
        return $this->birthdate;
    }

    public function setBirthdate(string $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function age(): int
    {
        $date = $this->getBirthdate();
        $today = date("d-m-Y");
        $diff = date_diff(date_create($date), date_create($today));
        return $diff->format('%y');
    }
    private function isValidBirthdate($date): bool
    {
        // $date = date('d-m-Y', strtotime($this->getBirthdate()));
        $date = new \DateTime($this->getBirthdate());
        $dateNow = new \DateTime();
        // $dateNow = date('d-m-Y', strtotime('now'));
        $dateDifference = date_diff($dateNow, $date)->format('%y');

        if ($dateDifference > 13) {
            return true;
        } else {
            return false;
        }
    }
    private function isValidTodolist()
    {
        $todoList = $this->getToDoList();
        if (count($todoList->getItems()) > 10) {
            return false;
        } else if (count($todoList->getItems()) == 0 || $todoList->getItems() == null) {
            return true;
        } else {
            foreach ($todoList->getItems() as $key => $value) {
                if (strlen($value->getContent()) > 1000) {
                    var_dump("content trop long");
                    return false;
                }
            }
        }
        return true;
    }

    public function isValid()
    {
        if (!$this->isValidName($this->getName())) {
            throw new \Exception("Nom pas bon");
            // return "Nom pas bon";
        } else {
            if (!$this->isValidSurname($this->getSurname())) {
                throw new \Exception("Insérez prénom");
                // return "Insérez prénom";
            } else {
                if (!$this->isValidEmail($this->getEmail())) {
                    throw new \Exception("Mauvais format de mail");
                    // return "Mauvais format de mail";
                } else {
                    if (!$this->isValidPassword($this->getPassword())) {
                        throw new \Exception("Mot de passe trop court ou trop long");
                        // return "Mot de passe trop court ou trop long";
                    } else {
                        if (!$this->isValidBirthdate($this->getBirthdate())) {
                            throw new \Exception("Vous êtes trop jeune");
                            // return "Vous êtes trop jeune";
                        } else {
                            if (!$this->isValidTodolist())
                                throw new \Exception("ToDoList non conforme");
                            return "Votre profil est conforme !";
                        }
                    }
                }
            }
        }
    }

    private function isValidName($inputName): bool
    {
        if ($inputName == null || $inputName == "") {
            return false;
        } else {
            return true;
        }
    }

    private function isValidSurname($inputSurname): bool
    {
        if ($inputSurname != null && $inputSurname != "") {
            return true;
        } else {
            return false;
        }
    }

    private function isValidEmail($inputEmail): bool
    {
        if (filter_var($inputEmail, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    private function isValidPassword($password): bool
    {
        if (strlen($password) > 8 && strlen($password) < 40) {
            return true;
        } else {
            return false;
        }
    }
}
