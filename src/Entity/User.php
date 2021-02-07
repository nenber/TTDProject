<?php

namespace App\Entity;


use App\Repository\UserRepository;
use DateTime;
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
     * @ORM\Column(type="date")
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

    public function getBirthdate(): ?DateTime
    {
        return $this->birthdate;
    }

    public function setBirthdate(DateTime $birthdate): self
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

    public function removeTodoList()
    {
        $this->toDoList = null;
    }
}
