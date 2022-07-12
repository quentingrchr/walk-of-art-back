<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailSender
{
    /** @var MailerInterface  */
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;

    }
    public function emailSender(User $user)
    {
        $email = (new TemplatedEmail())
            ->to('lucasperez.apple@gmail.com')
/*            ->to('lucasperez.apple@gmail.com')*/
            ->subject('Welcome to Walk Of Art '. $user->getFirstname() .' ' . $user->getLastname() . '!')
            ->htmlTemplate('emails/signup.html.twig')
            ->context([
                'user' => $user,
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Mail sender error : '. $e);
        }
    }
}