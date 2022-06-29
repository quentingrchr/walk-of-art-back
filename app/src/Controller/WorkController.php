<?php


namespace App\Controller;


use App\Common\JwtFindUserDecoder;
use App\Common\ResponseRenderer;
use App\Entity\Work;
use App\Entity\WorkFiles;
use App\Repository\ExhibitionRepository;
use App\Repository\ReservationRepository;
use App\Repository\WorkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkController extends AbstractController
{
    private JwtFindUserDecoder $user;
    private WorkRepository $workRepository;
    private ExhibitionRepository $exhibitionRepository;
    private ReservationRepository $reservationRepository;
    private EntityManagerInterface $entityManager;
    private ResponseRenderer $response;

    public function __construct(JwtFindUserDecoder $user, WorkRepository $workRepository, ExhibitionRepository $exhibitionRepository,
                                ReservationRepository $reservationRepository, EntityManagerInterface $entityManager, ResponseRenderer $response)
    {
        $this->user = $user;
        $this->workRepository = $workRepository;
        $this->exhibitionRepository = $exhibitionRepository;
        $this->reservationRepository = $reservationRepository;
        $this->entityManager = $entityManager;
        $this->response = $response;
    }

    #[Route(path: '/api/works/', name: 'app_api_works')]
    public function works(): Response|string
    {
        $works = $this->workRepository->findBy(array('user' => $this->user->findUser()));

        $arrayOfWorks = [];

        foreach($works as $work) {
            $exhibition = $this->exhibitionRepository->findBy(array('work' => $work));

            $reservations = $this->reservationRepository->findBy(array('exhibition' => $exhibition));

            $arrayOfReservations = [];

            foreach($reservations as $reservation) {
                $arrayOfReservations[] = $reservation->jsonSerialize();
            }

            $arrayOfWorkFiles = [];
            foreach($work->getWorkFiles() as $workFile) {
                $arrayOfWorkFiles[] = $workFile->jsonSerialize();
            }

            $arrayOfWorks[] = [
                'travaux' => $work->jsonSerialize(),
                'fichiers' => $arrayOfWorkFiles,
                'reservations' => $arrayOfReservations
            ];
        }

        return $this->response->response($arrayOfWorks);
    }

    #[Route(path: '/api/work/{id}', name: 'app_api_work')]
    public function work(Work $work): Response|string
    {
        if($this->user->findUser()->getId() == $work->getUser()->getId()) {
            return $this->response->response($work->jsonSerialize());
        }

        return "Ce n'est pas la réservation de l'utilisateur connecté.";
    }

    #[Route(path: '/api/work-file/{id}', name: 'app_api_work_file')]
    public function workFile(WorkFiles $workFile): Response|string
    {
        if($this->user->findUser()->getId() == $workFile->getWork()->getUser()->getId()) {
            return $this->response->response($workFile->jsonSerialize());
        }

        return "Ce n'est pas la réservation de l'utilisateur connecté.";
    }
}