<?php


namespace App\Controller;

use App\Entity\Exhibition;
use App\Repository\ExhibitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/api')]
class ExhibitionController extends AbstractController
{
    private ExhibitionRepository $exhibitionRepository;
    private EntityManagerInterface $entityManager;
    private Security $security;
    private SerializerInterface $serializer;

    public function __construct(ExhibitionRepository $exhibitionRepository, EntityManagerInterface $entityManager,
                                Security $security, SerializerInterface $serializer)
    {
        $this->exhibitionRepository = $exhibitionRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->serializer = $serializer;
    }

    #[Route(path: '/exhibitions/', name: 'app_api_exhibitions')]
    public function expositions(): JsonResponse
    {
        if (is_null($this->security->getUser())) {
            return new JsonResponse([
                'message' => 'User not logged in'
            ], status: Response::HTTP_CONFLICT);
        }

        $exhibitions = $this->exhibitionRepository->findBy(array('user' => $this->security->getUser()));

        $arrayOfExhibitions = [];

        if (is_null($arrayOfExhibitions)) {
            return new JsonResponse([
                'message' => 'This user don\'t have exhibitions'
            ], status: Response::HTTP_CONFLICT);
        }

        foreach($exhibitions as $exhibition) {
            $arrayOfExhibitions[] = $exhibition;
        }

        return new JsonResponse(
            json_decode($this->serializer->serialize($arrayOfExhibitions, 'json')),
            status: Response::HTTP_CREATED
        );
    }

    #[Route(path: '/exhibition/{id}', name: 'app_api_exhibition')]
    public function exposition(Exhibition $exhibition): JsonResponse
    {
        if (is_null($this->security->getUser())) {
            return new JsonResponse([
                'message' => 'User not logged in'
            ], status: Response::HTTP_CONFLICT);
        }

        if ($this->security->getUser()->getUserIdentifier() !== $exhibition->getUser()->getUserIdentifier()) {
            return new JsonResponse([
                'message' => 'It is not the exhibition of the current user.'
            ], status: Response::HTTP_CONFLICT);
        }

        return new JsonResponse(
            json_decode($this->serializer->serialize($exhibition, 'json')),
            status: Response::HTTP_CREATED
        );
    }
}