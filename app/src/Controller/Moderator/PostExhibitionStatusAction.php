<?php

namespace App\Controller\Moderator;

use App\Config\StatusEnum;
use App\Entity\Exhibition;
use App\Entity\ExhibitionStatus;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class PostExhibitionStatusAction
{
    public function __construct(private Security $security, private UserRepository $userRepository){}

    public function __invoke(Request $request)
    {
        $exhibition = $request->attributes->get('data');
        if (!($exhibition instanceof Exhibition)){
            throw new \RuntimeException('Exhibition attendu');
        }

        if ($exhibition->getStatutes()->last() != StatusEnum::PENDING){
            return new JsonResponse([
                'message' => 'Exhibition already moderate'
            ], status: Response::HTTP_GONE); // TODO: Pas la bonne erreur
        }

        $jsonData = json_decode($request->getContent(), true);

        $exhibitionStatus = (new ExhibitionStatus())
            ->setStatus(StatusEnum::tryFrom($jsonData['status']))
            ->setReason($jsonData['reason'])
            ->setDescription($jsonData['description'])
            ->setUser($this->userRepository->find($this->security->getUser()->getId())); // TODO: Enlevé l'appel a la db

        $exhibition->addExhibitionStatut($exhibitionStatus);

        return $exhibition;
    }
}