<?php

namespace App\Service;

use App\Entity\Item;
use App\Entity\ToDoList;
use App\Entity\User;
use Exception;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ToDoListService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendNotification()
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('nenberpiedagnel@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Il ne vous reste que deux items  a ajouté dans votre todolist')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $this->mailer->send($email);
    }

    public function create(User &$user)
    {
        if ($user->getToDoList()) {
            throw new Exception("Vous avez déjà créer une liste", 500);
        }

        $user->setToDoList(new TodoList());
    }

    public function insert(ToDoList $todo, Item $todoItem)
    {
        if ($todo->getItems()->count() > 0) {
            /** @var Item $last */
            $last = $todo->getItems()->last();
            $diff = $last->getDate()->diff($todoItem->getDate());
            if (($diff->format("%h") * 60 + $diff->format("%i")) < 30) {
                throw new Exception("Vous devez avoir 30 minutes de différences entre chaque ajout", 500);
            }
        }

        if ($todo->getItems()->count() === 10) {
            throw new Exception("Vous avez déjà le nombre maximum de tâche", 500);
        }

        return $todo->addItem($todoItem);
    }

    public function isValidTodolist(User $user)
    {
        $todoList = $user->getToDoList();
        if (count($todoList->getItems()) > 10) {
            return false;
        } else {
            if (count($todoList->getItems()) == 0 || $todoList->getItems() == null) {
                return true;
            } else {
                foreach ($todoList->getItems() as $key => $value) {
                    if (strlen($value->getContent()) > 1000) {
                        var_dump("content trop long");
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
