<?php


namespace App\Controller;

use App\Common\JwtFindUserDecoder;
use App\Common\ResponseRenderer;
use App\Entity\Exhibition;
use App\Repository\ExhibitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExhibitionController extends AbstractController
{
    private JwtFindUserDecoder $user;
    private ExhibitionRepository $exhibitionRepository;
    private EntityManagerInterface $entityManager;
    private ResponseRenderer $response;

    public function __construct(JwtFindUserDecoder $user, ExhibitionRepository $exhibitionRepository, EntityManagerInterface $entityManager,
                                ResponseRenderer $response)
    {
        $this->user = $user;
        $this->exhibitionRepository = $exhibitionRepository;
        $this->entityManager = $entityManager;
        $this->response = $response;
    }

    #[Route(path: '/api/expositions/', name: 'app_api_expositions')]
    public function expositions(): Response|string
    {
        $exhibitions = $this->exhibitionRepository->findBy(array('user' => $this->user->findUser()));

        $arrayOfExhibitions = [];

        foreach($exhibitions as $exhibition) {
            $arrayOfExhibitions[] = $exhibition->jsonSerialize();
        }

        return $this->response->response($arrayOfExhibitions);
    }

    #[Route(path: '/api/exposition/{id}', name: 'app_api_exposition')]
    public function exposition(Exhibition $exhibition): Response|string
    {
        if($this->user->findUser()->getId() == $exhibition->getUser()->getId()) {
            return $this->response->response($exhibition->jsonSerialize());
        }

        return "Ce n'est pas la réservation de l'utilisateur connecté.";
    }
}