<?php


namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ExhibitionRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/api')]
class ReservationController extends AbstractController
{
    private ReservationRepository $reservationRepository;
    private ExhibitionRepository $exhibitionRepository;
    private EntityManagerInterface $entityManager;
    private Security $security;
    private SerializerInterface $serializer;


    public function __construct(ReservationRepository $reservationRepository, ExhibitionRepository $exhibitionRepository, EntityManagerInterface $entityManager,
                                Security $security, SerializerInterface $serializer)
    {
        $this->reservationRepository = $reservationRepository;
        $this->exhibitionRepository = $exhibitionRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->serializer = $serializer;
    }

    #[Route(path: '/reservations', name: 'app_api_reservations')]
    public function reservations(): JsonResponse
    {
        if (is_null($this->security->getUser())) {
            return new JsonResponse([
                'message' => 'User not logged in'
            ], status: Response::HTTP_CONFLICT);
        }

        $exhibitions = $this->exhibitionRepository->findBy(array('user' => $this->security->getUser()));

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

        if (is_null($arrayOfReservations)) {
            return new JsonResponse([
                'message' => 'This user don\'t have reservations'
            ], status: Response::HTTP_CONFLICT);
        }

        return new JsonResponse(
            json_decode($this->serializer->serialize($arrayOfReservations, 'json')),
            status: Response::HTTP_CREATED
        );
    }

    #[Route(path: '/reservation/{id}', name: 'app_api_reservation')]
    public function reservation(Reservation $reservation): JsonResponse
    {
        if (is_null($this->security->getUser())) {
            return new JsonResponse([
                'message' => 'User not logged in'
            ], status: Response::HTTP_CONFLICT);
        }

        $reservation = $this->reservationRepository->find($reservation);

        if ($this->security->getUser()->getUserIdentifier() !== $reservation->getExhibition()->getUser()->getUserIdentifier()) {
            return new JsonResponse([
                'message' => 'It is not the current user reservation.'
            ], status: Response::HTTP_CONFLICT);
        }

        return new JsonResponse(
            json_decode($this->serializer->serialize($reservation, 'json')),
            status: Response::HTTP_CREATED
        );
    }
}