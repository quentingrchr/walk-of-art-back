<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetAvailableGalleriesAction extends AbstractController
{
    public function __construct(private GalleryRepository $galleryRepository){}

    public function __invoke(Request $request)
    {
        $params = json_decode($request->getContent(), true);

        if (is_null($params)) {
            return new JsonResponse([
                'message' => 'Incomplete form'
            ], status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        elseif ($params['dateStart']  > ($params['dateEnd'])) {
            return new JsonResponse([
                'message' => 'Start date more recent than End date.'
            ], status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->galleryRepository->findAvailableGalleriesByParams($params);
    }
}