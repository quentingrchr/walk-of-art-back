<?php

namespace App\Controller\Moderator;

use App\Entity\Exhibition;
use App\Repository\ExhibitionRepository;

class GetExhibitionToModerateAction
{
    public function __construct(private ExhibitionRepository $exhibitionRepository){}

    /**
     * @return Exhibition[]
     */
    public function __invoke()
    {
        return $this->exhibitionRepository->findAllForModaration();
    }
}