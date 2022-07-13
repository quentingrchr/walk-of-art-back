<?php


namespace App\Controller;

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

    /**
     * @param array $data
     */
    public function emailSender(array $data)
    {
        /*
         * exemples de l'array de data, pour l'inscription :
         * $data = ['user' => $user];
         *
         * Pour le changement de status :
         * $data = ['exhibition' => $exhibition, 'exhibitionStatus' => $exhibitionStatus];
         */

        /*
         * Mon test d'envoi de mail pour l'exhibition ci dessous, avec le status lié à cette exhibition, qui est soit validated soit refused
         * (pour le test j'ai appelé les repo)
         * $data = [
         *      'exhibition' => $this->exhibitionRepository->find('42f4e2dc-6f70-4579-b6bd-57a47dbc4260'),
         *      'exhibitionStatus' => $this->exhibitionStatutRepository->find('9a9f6389-7dc8-4e76-a0c5-4c709573ea3b')
         * ];
         */

        $email = (new TemplatedEmail())
            ->to(array_key_exists('exhibition', $data) ?
                $data['exhibition']->getUser()->getUserIdentifier() :
                $data['user']->getUserIdentifier())
            ->subject(array_key_exists('exhibition', $data) ?
                'Notification relative à une demande d\'exposition - '. $data['exhibition']->getTitle() :
                'Confirmation d\'inscription à Walk of Art - '. $data['user']->getFirstname() .' '. $data['user']->getLastname())
            ->htmlTemplate(array_key_exists('exhibition', $data) ?
                'notifications/notification-exposition.html.twig' :
                'notifications/confirmation-inscription.html.twig')
            ->context([
                'data' => $data,
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Mail sender error : '. $e);
        }
    }
}