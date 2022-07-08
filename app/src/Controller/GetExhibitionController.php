<?php


namespace App\Controller;

use App\Entity\Exhibition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route(path: '/api')]
class GetExhibitionController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Request $request)
    {
        $exhibition = $request->attributes->get('data');

        if (!($exhibition instanceof Exhibition)){
            throw new \RuntimeException('Exhibition expected.');
        }

        if (is_null($this->security->getUser())) {
            throw new \RuntimeException('The current user is not logged in.');
        }

        if ($this->security->getUser()->getUserIdentifier() !== $exhibition->getUser()->getUserIdentifier()) {
            throw new \RuntimeException('It is not the exhibition of the current user.');
        }

        return $request->attributes->get('data');
    }
}