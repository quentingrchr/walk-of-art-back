<?php


namespace App\Controller;

use App\Entity\Exhibition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route(path: '/api')]
class GetReservationsController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Request $request)
    {
        $reservations = $request->attributes->get('data');

        if (is_null($this->security->getUser())) {
            throw new \RuntimeException('The current user is not logged in.');
        }

        $arrayOfReservations = [];

        foreach ($reservations as $reservation) {
            dump($reservation->getExhibition()->getUser()->getUserIdentifier());
            if($this->security->getUser()->getUserIdentifier() === $reservation->getExhibition()->getUser()->getUserIdentifier()) {
                $arrayOfReservations[] = $reservation;
            }
        }

        if(is_null($arrayOfReservations)) {
            throw new \RuntimeException('It is not the reservation of the current user.');
        }

        return $arrayOfReservations;
    }
}