<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GetAvailableGalleriesAction extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private GalleryRepository $galleryRepository){}

    public function __invoke(Request $request)
    {
        $params = json_decode($request->getContent(), true);

        if (is_null($params)) {
            throw new \RuntimeException('No parameters send.');
        }

        if($params['dateStart']  > ($params['dateEnd'])) {
            throw new \RuntimeException('Start date more recent than End date.');
        }

        return $this->galleryRepository->findAvailableGalleriesByParams($params);
    }
}