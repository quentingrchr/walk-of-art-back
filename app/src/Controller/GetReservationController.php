<?php


namespace App\Controller;

use App\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route(path: '/api')]
class GetReservationController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Request $request)
    {
        $reservation = $request->attributes->get('data');

        if (!($reservation instanceof Reservation)){
            throw new \RuntimeException('Reservation expected.');
        }

        if (is_null($this->security->getUser())) {
            throw new \RuntimeException('The current user is not logged in.');
        }

        if($this->security->getUser()->getUserIdentifier() !== $reservation->getExhibition()->getUser()->getUserIdentifier()) {
            throw new \RuntimeException('It is not the reservation of the current user.');
        }

        return $request->attributes->get('data');
    }
}