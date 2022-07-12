<?php

namespace App\Controller;

use App\Entity\Exhibition;
use App\Repository\BoardRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostExhibitionAction
{
    public function __construct(private BoardRepository $boardRepository){}

    public function __invoke(Request $request)
    {
        $jsonData = json_decode($request->getContent(), true);

        $exhibition = $request->attributes->get('data');

        if (!($exhibition instanceof Exhibition)){
            throw new \RuntimeException('Exhibition attendu');
        }

//        if (!($jsonData['orientation'] instanceof OrientationEnum)){ // TODO:: check Enum
//            throw new \RuntimeException('Orientation invalid');
//        }

        $board = $this->boardRepository->getBoardAvailableByGalleryByParam($jsonData['gallery'], $exhibition->getDateStart(), $exhibition->getDateEnd(), $jsonData['orientation']);

        if (!$board) {
            return new JsonResponse([
                'message' => 'No board available on this gallery'
            ], status: Response::HTTP_GONE);
        }

        $exhibition->setBoard($board);

        return $exhibition;
    }
}