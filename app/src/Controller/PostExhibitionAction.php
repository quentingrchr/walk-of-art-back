<?php

namespace App\Controller;

use App\Config\StatusEnum;
use App\Entity\Exhibition;
use App\Entity\ExhibitionStatus;
use App\Repository\BoardRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class PostExhibitionAction
{
    public function __construct(private BoardRepository $boardRepository, private Security $security, private UserRepository $userRepository){}

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

        $exhibition->addExhibitionStatus(
            (new ExhibitionStatus())
            ->setStatus(StatusEnum::PENDING)
            ->setUser($this->userRepository->find($this->security->getUser()->getId())) // TODO: Enlevé l'appel a la db
        );

        return $exhibition;
    }
}
