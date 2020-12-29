<?php

namespace App\Service;

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
}
