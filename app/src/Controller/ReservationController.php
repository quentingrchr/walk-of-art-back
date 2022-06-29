<?php


namespace App\Controller;

use App\Common\JwtFindUserDecoder;
use App\Common\ResponseRenderer;
use App\Entity\Reservation;
use App\Repository\ExhibitionRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[AsController]
class ReservationController extends AbstractController
{
    private JwtFindUserDecoder $user;
    private ReservationRepository $reservationRepository;
    private ExhibitionRepository $exhibitionRepository;
    private EntityManagerInterface $entityManager;
    private ResponseRenderer $response;

    public function __construct(JwtFindUserDecoder $user, ReservationRepository $reservationRepository, ExhibitionRepository $exhibitionRepository, EntityManagerInterface $entityManager, ResponseRenderer $response)
    {
        $this->user = $user;
        $this->reservationRepository = $reservationRepository;
        $this->exhibitionRepository = $exhibitionRepository;
        $this->entityManager = $entityManager;
        $this->response = $response;
    }

    #[Route(path: '/api/reservations', name: 'app_api_reservations')]
    public function reservations(): Response|string
    {
        $exhibitions = $this->exhibitionRepository->findBy(array('user' => $this->user->findUser()));

        $reservations = $this->reservationRepository->findBy(array('exhibition' => $exhibitions));

        $arrayOfReservations = [];

        foreach($reservations as $reservation) {

            $arrayOfWorkFiles = [];

            foreach ($reservation->getExhibition()->getWork()->getWorkFiles() as $workFile) {
                $arrayOfWorkFiles[] = $workFile->jsonSerialize();
            }

            $arrayOfReservations[] = [
                'reservations' => $reservation->jsonSerialize(),
                'expositions' => $reservation->getExhibition()->jsonSerialize(),
                'travaux' => $reservation->getExhibition()->getWork()->jsonSerialize(),
                'fichiers' => $arrayOfWorkFiles
            ];
        }

        return $this->response->response($arrayOfReservations);
    }

    #[Route(path: '/api/reservation/{id}', name: 'app_api_reservation')]
    public function reservation(Reservation $reservation): Response|string
    {
            $reservation = $this->reservationRepository->find($reservation);

            if($this->user->findUser()->getId() == $reservation->getExhibition()->getUser()->getId()) {
                return $this->response->response(['reservations' => $reservation->jsonSerialize(), 'expositions' => $reservation->getExhibition()->jsonSerialize()]);
            }

            return "Ce n'est pas la réservation de l'utilisateur connecté.";
    }
}