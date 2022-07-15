<?php

namespace App\Controller;

use App\Repository\ExhibitionRepository;
use App\Repository\ReactionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
class GetReactionsAction
{
    public function __construct(private ExhibitionRepository $exhibitionRepository,
                                private ReactionRepository $reactionRepository){}

    public function __invoke(string $boardId)
    {
        if (!$boardId) {
            return new JsonResponse([
                'message' => 'No board sended in route.'
            ], status: Response::HTTP_GONE);
        }

        $exhibition = $this->exhibitionRepository->findBy(['board' => $boardId]);

        if (!$exhibition) {
            return new JsonResponse([
                'message' => 'Exhibition not found.'
            ], status: Response::HTTP_GONE);
        }

        $reactions = $this->reactionRepository->findReactionsByExhibition($exhibition[0]->getId());

        if(empty($reactions)) {
            return new JsonResponse([
                'message' => 'No reactions for this board.'
            ], status: Response::HTTP_GONE);
        }

        $arrayReactions = [];

        // Remplacement de la key '1' du count de la requète SQL pour la key 'count'
        foreach($reactions as $reaction) {
            $reactionKeys = array_keys( $reaction);
            $reactionKeys[ array_search( '1', $reactionKeys ) ] = 'count';

            $arrayReactions[] = array_combine( $reactionKeys, $reaction );
        }

        return $arrayReactions;
    }
}