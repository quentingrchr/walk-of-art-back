<?php


namespace App\Controller;


use App\Repository\ExhibitionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetExhibitionByBoardId
{
    public function __construct(private ExhibitionRepository $exhibitionRepository){}

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

        return $exhibition;
    }
}