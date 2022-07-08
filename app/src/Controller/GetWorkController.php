<?php


namespace App\Controller;

use App\Entity\Work;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route(path: '/api')]
class GetWorkController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Request $request)
    {
        $work = $request->attributes->get('data');

        if (!($work instanceof Work)){
            throw new \RuntimeException('Work expected.');
        }

        if (is_null($this->security->getUser())) {
            throw new \RuntimeException('The current user is not logged in.');
        }

        if($this->security->getUser()->getUserIdentifier() !== $work->getUser()->getUserIdentifier()) {
            throw new \RuntimeException('It is not the current user work.');
        }

        return $work;
    }
}