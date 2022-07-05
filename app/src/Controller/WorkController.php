<?php


namespace App\Controller;

use App\Entity\Work;
use App\Entity\WorkFiles;
use App\Repository\ExhibitionRepository;
use App\Repository\ReservationRepository;
use App\Repository\WorkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/api')]
class WorkController extends AbstractController
{
    private WorkRepository $workRepository;
    private ExhibitionRepository $exhibitionRepository;
    private ReservationRepository $reservationRepository;
    private EntityManagerInterface $entityManager;
    private Security $security;
    private SerializerInterface $serializer;

    public function __construct(WorkRepository $workRepository, ExhibitionRepository $exhibitionRepository,
                                ReservationRepository $reservationRepository, EntityManagerInterface $entityManager,
                                Security $security, SerializerInterface $serializer)
    {
        $this->workRepository = $workRepository;
        $this->exhibitionRepository = $exhibitionRepository;
        $this->reservationRepository = $reservationRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->serializer = $serializer;
    }

    #[Route(path: '/works/', name: 'app_api_works')]
    public function works(): JsonResponse
    {
        if (is_null($this->security->getUser())) {
            return new JsonResponse([
                'message' => 'User not logged in'
            ], status: Response::HTTP_CONFLICT);
        }

        $works = $this->workRepository->findBy(array('user' => $this->security->getUser()));

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

        if (is_null($arrayOfWorks)) {
            return new JsonResponse([
                'message' => 'This user don\'t have works'
            ], status: Response::HTTP_CONFLICT);
        }

        return new JsonResponse(
            json_decode($this->serializer->serialize($arrayOfWorks, 'json')),
            status: Response::HTTP_CREATED
        );
    }

    #[Route(path: '/work/{id}', name: 'app_api_work')]
    public function work(Work $work): JsonResponse
    {
        if (is_null($this->security->getUser())) {
            return new JsonResponse([
                'message' => 'User not logged in'
            ], status: Response::HTTP_CONFLICT);
        }

        if ($this->security->getUser()->getUserIdentifier() !== $work->getUser()->getUserIdentifier()) {
            return new JsonResponse([
                'message' => 'It is not the current user work.'
            ], status: Response::HTTP_CONFLICT);
        }

        return new JsonResponse(
            json_decode($this->serializer->serialize($work, 'json')),
            status: Response::HTTP_CREATED
        );
    }

    #[Route(path: '/work-file/{id}', name: 'app_api_work_file')]
    public function workFile(WorkFiles $workFile): JsonResponse
    {
        if (is_null($this->security->getUser())) {
            return new JsonResponse([
                'message' => 'User not logged in'
            ], status: Response::HTTP_CONFLICT);
        }

        if ($this->security->getUser()->getUserIdentifier() !== $workFile->getWork()->getUser()->getUserIdentifier()) {
            return new JsonResponse([
                'message' => 'It is not the current user file.'
            ], status: Response::HTTP_CONFLICT);
        }

        return new JsonResponse(
            json_decode($this->serializer->serialize($workFile, 'json')),
            status: Response::HTTP_CREATED
        );
    }
}