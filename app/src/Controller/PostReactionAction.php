<?php

namespace App\Controller;

use App\Entity\Reaction;
use App\Repository\ExhibitionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
class PostReactionAction
{
    public function __construct(private ExhibitionRepository $exhibitionRepository){}

    public function __invoke(string $boardId, Request $request): Reaction|JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);

        $reaction = new Reaction();

        if (!$boardId) {
            return new JsonResponse([
                'message' => 'No board sended in route.'
            ], status: Response::HTTP_GONE);
        }

        $exhibition = $this->exhibitionRepository->findBy(['board' => $boardId]);

        if (!$exhibition) {
            return new JsonResponse([
                'message' => 'This board don\'t have exhibition.'
            ], status: Response::HTTP_GONE);
        }

        $reaction->setExhibition($exhibition[0]);
        $reaction->setReaction($jsonData['reaction']);
        return $reaction->setVisitor($jsonData['visitorId']);
    }
}