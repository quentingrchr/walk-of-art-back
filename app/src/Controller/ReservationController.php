<?php


namespace App\Controller;


use App\Entity\Reservation;
use App\Repository\ExhibitionRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsController]
class ReservationController extends AbstractController
{
    private UserRepository $userRepository;
    private ReservationRepository $reservationRepository;
    private ExhibitionRepository $exhibitionRepository;
    private EntityManagerInterface $entityManager;
    private JWTTokenManagerInterface $jwtManager;
    private TokenStorageInterface $tokenStorageInterface;

    public function __construct(UserRepository $userRepository, ReservationRepository $reservationRepository, ExhibitionRepository $exhibitionRepository, EntityManagerInterface $entityManager
        , JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorageInterface)
    {
        $this->userRepository = $userRepository;
        $this->reservationRepository = $reservationRepository;
        $this->exhibitionRepository = $exhibitionRepository;
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    #[Route(path: '/reservation/{id}', name: 'app_api_reservation')]
    public function reservation(Reservation $reservation): array|string
    {
        $data =  $this->jwtManager->decode($this->tokenStorageInterface->getToken());

        if($data != null) {
            $user = $this->userRepository->findBy(['email' => $data["username"]])[0];
            $reservation = $this->reservationRepository->find($reservation);

            if($user->getId() == $reservation->getExhibition()->getUser()->getId()) {
                return [
                    'reservation' => $reservation,
                    'exhibition' => $reservation->getExhibition()
                ];
            }

            return "Ce n'est pas la réservation de l'utilisateur connecté.";
        }
        return "Aucun utilisateur n'est connecté.";
    }
}